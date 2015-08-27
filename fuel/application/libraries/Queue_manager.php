<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Queue_manager
{
    public function __construct($params)
    {
        $db_table = $params['db_table'];
        $CI =& get_instance();
    }

    //hakee seuraavan ja lukitsee sen. paluutaulukon success kertoo miten kävi
    public function get_next()
    {
        $data = array('success' => false);
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa jonoitemiä uudestaan käsittelyyn 15 minuuttiin
        
        $CI->db->from($db_table);
        $CI->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
        $CI->db->order_by("lisatty", "asc"); 
        $query = $CI->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 

            $date->setTimestamp(time());
            $user = $CI->ion_auth->user()->row();
            $update_data = array('kasitelty' => $date->format('Y-m-d H:i:s'), 'kasittelija' => $user->tunnus);
            
            $CI->db->where('id', $data['id']);
            $CI->db->update($db_table, $update_data);
            
            $data['success'] = true;
        }
        
        return $data;
    }
    
    //palauttaa jonoitemin tiedot lukitsematta
    public function get_by_id($id)
    {
        $data = array('success' => false);
        
        $CI->db->from($db_table);
        $CI->db->where('id', $id);
        $query = $CI->db->get();
        
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
        
        $html .= '<p><form method="post" action="' . current_url() . '_kasittele/hyvaksy/' . $id . '"><input type="submit" value="Hyväksy"></form>';
        $html .= '<form method="post" action="' . current_url() . '_kasittele/hylkaa/' . $id . '">Hylkäyssyy: <input type="text" name="rejection_reason"><input type="submit" value="Hylkää"></form>';
        $html .= '<form method="post" action="' . current_url() . '"><input type="submit" value="Ohita ja ota seuraava"></form></p>';
        
        return $html;
    }
    
    //lisää datat db_table poislukien loppupääte _jonossa -tauluun, plus hyvaksyi ja hyvaksytty -kentät
    //poistaa id:n jonosta
    //lähettää recipientille msg:n adminilta
    public function process_queue_item($id, $insert_data, $msg_recipient, $msg)
    {
        $CI->load->model('tunnukset_model');
        $CI->tunnukset_model->send_message(1, $msg_recipient, $msg);
        
        $user = $CI->ion_auth->user()->row();
        $insert_data['hyvaksyi'] = $user->tunnus;
        $insert_data['hyvaksytty'] = time();
        $CI->db->insert(str_replace("_jonossa", "", $db_table), $insert_data);
        
        $CI->db->delete($db_table, array('id' => $id));
    }
    
    //palauttaa html:nä montako jonossa on ja mikä on vanhimman datetime plus seuraavanhakunappi
    public function get_queue_frontpage()
    {
        $data = array('success' => false);
        
        $CI->db->select('lisatty');
        $CI->db->from($db_table);
        $CI->db->order_by("lisatty", "asc"); 
        $query = $CI->db->get();
        
        if ($query->num_rows() > 0)
        {
            $db_data = $query->row_array(); 
            $data['oldest'] = $db_data['lisatty'];
            
            $CI->db->from($db_table);
            $data['queue_length'] = $CI->db->count_all_results();
            
            $data['html'] = "<p>Jonon pituus on " . $data['queue_length'] . ". Vanhin jonottaja on lisätty " . $data['oldest'] . ".</p><p>";
            $CI->load->library('form_builder', array('submit_value' => 'Hae seuraava'));
            $CI->form_builder->form_attrs = array('method' => 'post', 'action' => current_url());
            $vars['html'] .= $CI->form_builder->render();
            $vars['html'] .= "</p>";
            
            $data['success'] = true;
        }
        
        return $data;
    }
}



