<?php
class Yllapito_kalenterit extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'jaos', 'jaos-yp', 'kisakalenteri');
    private $url;
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
       
       $this->load->model('Jaos_model');
              $this->load->library('Kisajarjestelma');

        $this->url = "yllapito/kalenterit/";
        
    }
    
    private function _is_jaos_admin(){
        return $this->user_rights->is_allowed(array('admin', 'jaos', 'jaos-yp'));
    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    public function index(){
        $data = array();
        $data['jaokset'] = $this->Jaos_model->get_jaos_list();
        $data['url'] = $this->url;
        
        foreach ($data['jaokset'] as &$jaos){
            $this->_sort_panel_info_applications('hakemukset_porr', $this->Jaos_model->raceApplicationsMaintenance($jaos['id'], true), $jaos);
           $this->_sort_panel_info_applications('hakemukset_norm', $this->Jaos_model->raceApplicationsMaintenance($jaos['id'], false), $jaos);
           $this->_sort_panel_info_applications('tulokset_porr', $this->Jaos_model->resultApplicationsMaintenance($jaos['id'], true), $jaos);
           $this->_sort_panel_info_applications('tulokset_norm', $this->Jaos_model->resultApplicationsMaintenance($jaos['id'], false), $jaos);
            
        }
        
    
    	$this->fuel->pages->render('yllapito/kisakalenteri/kisakalenterit_etusivu', $data);


    }
    
    private function _sort_panel_info_applications($key, $data, &$jaos){
        $jaos[$key] = $data['kpl'];
        if($jaos[$key] > 0){
            $jaos[$key . '_latest'] = $data['ilmoitettu'];
        }

    }
    
    
    
    
    
    
    #### Ylläpidon funktiot ####

		
	
/*
	
	function countAppl( $rekisteri, $ehto = NULL, $kentta = NULL ) {
	
		if($kentta != NULL) { $hakukentta = $kentta; } else { $hakukentta = "id"; }
	
		$query = "SELECT COUNT($hakukentta) FROM $rekisteri";
		
		if( $ehto != NULL ) {
			$query .= " WHERE $ehto";
		}
		
		$hakemuksia = mysql_query($query);
		$lasketaan = mysql_fetch_row($hakemuksia);
		
		return $lasketaan[0];				
	}	
	
	function lastAppl( $rekisteri, $kentta, $ehto = NULL ) {		
		
		$query = "SELECT UNIX_TIMESTAMP( $kentta ) AS vanhin FROM $rekisteri";
		
		if( $ehto != NULL ) {
			$query .= " WHERE $ehto ";
		}
		
		$query .= " ORDER BY $kentta ASC LIMIT 1";
		
		$hakemuksia = mysql_query($query);
		$lasketaan = mysql_fetch_row($hakemuksia);
		
		
		if( mysql_num_rows($hakemuksia) > 0 ) {	
			return '<span style="font-size: 80%; color: #A3A0A8;"> (vanhin '.date("d.m.Y",$lasketaan[0]).') </span>';	
		} else {
			return '';	
		}
	}
	
	function lastRaceAppl( $tyyppi, $jaos, $porrastettu = 0 ) {	
	
		if ( $tyyppi == 1 ) {
			$from = "kisat_kisakalenteri";
			$leftjoin = "";
		} else { 
			$from = "kisat_tulokset";
			$leftjoin = "LEFT JOIN kisat_kisakalenteri ON kisat_tulokset.kisa_id = kisat_kisakalenteri.kisa_id";
		}
	
		$hakemuksia = mysql_query("
							SELECT UNIX_TIMESTAMP( $from.ilmoitettu ) AS result
							FROM ".$from."
							".$leftjoin."
							WHERE 
								jaos = ".$jaos." AND 
								porrastettu = ".$porrastettu." AND 
								($from.hyvaksytty IS NULL OR $from.hyvaksytty = '0000-00-00 00:00:00') 
							ORDER BY $from.ilmoitettu ASC 
							LIMIT 1
							");
							
		
		$lasketaan = mysql_fetch_row($hakemuksia);
		
		
		if( mysql_num_rows($hakemuksia) > 0 ) {	
			return '<span style="font-size: 80%; color: #A3A0A8;"> (vanhin '.date("d.m.Y",$lasketaan[0]).') </span>';	
		} else {
			return '';	
		}
	}
   
   ///////////////////////////////////////////////////////////////////////////////
    // Kisakalenterin ylläpitofunktiot
    //////////////////////////////////////////////////////////////////////////////
    
    public function competitions_queue_get_next(){
      $this->_get_next('kisat_kisakalenteri');
    }
    
    public function results_queue_get_next(){
      $this->_get_next('kisat_tulokset');
    }
    
    
     private function _get_next($table)
    {
        $data = array('success' => false);
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa jonoitemiä uudestaan käsittelyyn 15 minuuttiin
        
        $this->CI->db->from($table);
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
            $this->CI->db->update($table, $update_data);
            
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

    
*/
    
}
    

?>