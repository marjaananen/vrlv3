<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

//KÄYTTÖOHJEET - MITEN LUODAAN UUSI JONO

//Kirjasto olettaa, että jonotaulu *_jonossa on jo olemassa tietokannassa ja jokin muu kontrolleri hoitaa lisäykset jonoon.
//Katso mallia esim. tallien rekisteröinnistä. Hyödynnä rekisteröintiin Form_collectionia.

//Tätä kirjastoa käytetään siis vain ylläpidon puolella. Ylläpito-kontrolleriin pitää lisätä:
//  -etusivufunktio
//  -jonofunktio
//  -käsittelyfunktio

//Esim. talleille nämä ovat: tallirekisteri_etusivu (yhdistettynä kategorioihin), tallijono, kasittele_talli.
//Uutta jonoa luotaessa on nopeinta kopioida nämä funktiot uutta jonoa varten muokattavaksi, erot ovat pieniä.

//...



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
            if($key != '__extra_param')
                $html .= "<p>" . $key . ": " . $value . "</p>";
        }
        
        $html .= '</div>';
        
        if($title != 'Tallianomus')
            $html .= '<p><form method="post" action="' . current_url() . '_kasittele/hyvaksy/' . $id . '"><input type="submit" value="Hyväksy"></form>';
        else
            $html .= '<p><form method="post" action="' . current_url() . '_kasittele/hyvaksy/' . $id . '">Tallilyhenteen kirjainosa (2-4 merkkiä): <input type="text" value="' . $raw_data['__extra_param'] . '" name="tnro_alpha"><input type="submit" value="Hyväksy"></form>';
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
            
            $date = new DateTime();
            $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa hakemusta uudestaan käsittelyyn 15 minuuttiin
            
            $this->CI->db->from($this->db_table);
            $this->CI->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
            $query = $this->CI->db->get();
            $data['queue_locked_num'] = $data['queue_length'] - $query->num_rows();
            
            $data['html'] = "<p>Jonon pituus on " . $data['queue_length'] . ", joista " . $data['queue_locked_num'] . " on lukittuna. Vanhin jonottaja on lisätty " . $data['oldest'] . ".</p>";
        }
        else
        {
            $data['html'] = "<p>Jono on tyhjä.</p>";
        }
        
        return $data;
    }
    
    //jos samassa funktiossa pitää käsitellä useaa jonoa, jonoja voi vaihtaa tällä
    public function set_db_table($table)
    {
        $this->db_table = $table;
    }
}



