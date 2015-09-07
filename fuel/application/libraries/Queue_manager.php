<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Queue_manager
{
    private $CI = 0;
    private $db_table = "";
    
    public function __construct($params)
    {
        $this->db_table = $params['db_table'];
        $this->CI =& get_instance();
    }

    //hakee seuraavan ja lukitsee sen. paluutaulukon success kertoo miten kävi
    public function get_next()
    {
        $data = array('success' => false);
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa jonoitemiä uudestaan käsittelyyn 15 minuuttiin
        
        $this->CI->db->from($this->db_table);
        $this->CI->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
        $this->CI->db->order_by("lisatty", "asc"); 
        $query = $this->CI->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 

            $date->setTimestamp(time());
            $user = $this->CI->ion_auth->user()->row();
            $update_data = array('kasitelty' => $date->format('Y-m-d H:i:s'), 'kasittelija' => $user->tunnus);
            
            $this->CI->db->where('id', $data['id']);
            $this->CI->db->update($this->db_table, $update_data);
            
            $data['success'] = true;
        }
        
        return $data;
    }
    
    //palauttaa jonoitemin tiedot lukitsematta
    public function get_by_id($id)
    {
        $data = array('success' => false);
        
        $this->CI->db->from($this->db_table);
        $this->CI->db->where('id', $id);
        $query = $this->CI->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 
            $data['success'] = true;
        }
        
        return $data;
    }
    
    //palauttaa html:nä tiedot ja käsittelynapit
    //raw datassa pitää olla Labelinnimi => arvo
    //käsittelykontrollerin pitää olla <nykyurl>_kasittele ja saa päätöksen sekä id:n parametrina
    public function format_html($title, $raw_data, $id)
    {
        $html = '<div class="container"><h3>' . $title . '</h3>';
        
        foreach($raw_data as $key => $value)
        {
            $html .= "<p>" . $key . ": " . $value . "</p>";
        }
        
        $html .= '</div>';
        
        if($title != 'Tallianomus')
            $html .= '<p><form method="post" action="' . current_url() . '_kasittele/hyvaksy/' . $id . '"><input type="submit" value="Hyväksy"></form>';
        else
            $html .= '<p><form method="post" action="' . current_url() . '_kasittele/hyvaksy/' . $id . '">Tallilyhenteen kirjainosa (2-4 merkkiä): <input type="text" name="tnro_alpha"><input type="submit" value="Hyväksy"></form>';
        $html .= '<form method="post" action="' . current_url() . '_kasittele/hylkaa/' . $id . '">Hylkäyssyy: <input type="text" name="rejection_reason"><input type="submit" value="Hylkää"></form>';
        $html .= '<form method="post" action="' . current_url() . '"><input type="submit" value="Ohita ja ota seuraava"></form></p>';
        
        return $html;
    }
    
    //lisää datat db_table poislukien loppupääte _jonossa -tauluun, plus hyvaksyi ja hyvaksytty -kentät
    //poistaa id:n jonosta
    //lähettää recipientille msg:n adminilta
    public function process_queue_item($id, $approved, $insert_data, $msg_recipient, $msg)
    {
        $this->CI->load->model('tunnukset_model');
        $this->CI->tunnukset_model->send_message(1, $msg_recipient, $msg);
        
        if($approved == true)
        {
            $user = $this->CI->ion_auth->user()->row();
            $insert_data['hyvaksyi'] = $user->tunnus;
            $this->CI->db->insert(str_replace("_jonossa", "", $this->db_table), $insert_data);
        }
        
        $this->CI->db->delete($this->db_table, array('id' => $id));
    }
    
    //palauttaa html:nä montako jonossa on ja mikä on vanhimman datetime plus seuraavanhakunappi
    public function get_queue_frontpage()
    {
        $data = array();
        
        $this->CI->db->select('lisatty');
        $this->CI->db->from($this->db_table);
        $this->CI->db->order_by("lisatty", "asc"); 
        $query = $this->CI->db->get();
        
        if ($query->num_rows() > 0)
        {
            $db_data = $query->row_array(); 
            $data['oldest'] = $db_data['lisatty'];
            
            $this->CI->db->from($this->db_table);
            $data['queue_length'] = $this->CI->db->count_all_results();
            
            $data['html'] = "<p>Jonon pituus on " . $data['queue_length'] . ". Vanhin jonottaja on lisätty " . $data['oldest'] . ".</p><p>";
            $this->CI->load->library('form_builder', array('submit_value' => 'Hae seuraava'));
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => current_url());
            $data['html'] .= $this->CI->form_builder->render();
            $data['html'] .= "</p>";
        }
        else
        {
            $data['html'] = "<p>Jono on tyhjä.</p><p>";
            $this->CI->load->library('form_builder', array('submit_value' => 'Hae seuraava'));
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => current_url());
            $data['html'] .= $this->CI->form_builder->render();
            $data['html'] .= "</p>";
        }
        
        return $data;
    }
}



