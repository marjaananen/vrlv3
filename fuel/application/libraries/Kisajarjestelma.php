<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Kisajarjestelma
{

    public function __construct()
        {
                    $this->CI =& get_instance();
                    $this->CI->load->model('Jaos_model');

        }
        
        
    private  $old_leveled_to_new = '2020-08-30'; // ÄLÄ MUOKKAA
    var $CI;
    
    public function new_leveled_start_time (){
        return $this->old_leveled_to_new;
    }
    
    public function sijoittuu($osallistujia, $jaos_id){
    
        $sijoittuu = 0;
        //NJ:ssä ei sijoituta samalla tavalla.
        if ( $jaos_id == 7 ) { $sijoittuu = 0; }
        else if( $osallistujia >= 1 AND $osallistujia <= 3 ) 	{	$sijoittuu = 1;	} 
        elseif( $osallistujia >= 4 AND $osallistujia <= 8 ) 	{	$sijoittuu = 2;	} 
        elseif( $osallistujia >= 9 AND $osallistujia <= 15 ) 	{	$sijoittuu = 3;	} 
        elseif( $osallistujia >= 16 AND $osallistujia <= 24 )	{	$sijoittuu = 4;	} 
        elseif( $osallistujia >= 25 AND $osallistujia <= 35 ) 	{	$sijoittuu = 5;	} 
        elseif( $osallistujia >= 36 AND $osallistujia <= 48 ) 	{	$sijoittuu = 6;	} 
        elseif( $osallistujia >= 49 AND $osallistujia <= 63 ) 	{	$sijoittuu = 7;	} 
        elseif( $osallistujia >= 64 AND $osallistujia <= 80 ) 	{	$sijoittuu = 8;	} 
        elseif( $osallistujia >= 81 AND $osallistujia <= 99 ) 	{	$sijoittuu = 9;	} 
        elseif( $osallistujia >= 100) 							{	$sijoittuu = 10; }
        else { $sijoittuu = 0; }
        
        return $sijoittuu;
                
    }
    

    //sisältää kaikki vanhat ja uudet arvontatavat, myös käytöstä poistuneet
    public function arvontatavat_options_legacy(){
        $arvontatavat = array (1 => "Lyhyt/pitkä arvonta",
                               2 => "Suhteutettu arvonta",
                               4 => "Tuotos/kysymys",
                               3=> "Porrastettu arvonta",
                               5=> "Tuomarointi (NJ)"); 
         return $arvontatavat;                                                                                                                        //
                                                                                                                                 
    
    }
    
    
        public function arvontatavat_options(){
        $arvontatavat = array (1 => "Lyhyt/pitkä arvonta",
                               2 => "Suhteutettu arvonta",
                               4 => "Tuotos/kysymys",
                               //3=> "Porrastettu arvonta",
                               //5=> "Tuomarointi (NJ)"
                               ); 
         return $arvontatavat;                                                                                                                        //
                                                                                                                                 
    
    }
    
    
    //kisapäivämäärät
    
    public function competition_date_max(){
      $lastDateOfNextMonth =strtotime('last day of next month') ;
      return date('Y-m-d', $lastDateOfNextMonth);
    }
    
    public function competition_date_min($vip_date){
     return date('Y-m-d', strtotime($vip_date.' + 1 day'));
    }
    
    public function competition_vip_date_normal_min(){
        $date = date('Y-m-d');
        return date('Y-m-d', strtotime($date.' + 14 days'));
    }
    
    public function competition_vip_date_direct_min(){
        $date = date('Y-m-d');
        return date('Y-m-d', strtotime($date.' + 7 days'));
    }
    
    public function competition_vip_date_leveled_min(){
        $date = date('Y-m-d');
        return date('Y-m-d', strtotime($date.' + 1 day'));
    }
    
    
    
    //////////////////////////////////////////////////////////////
    // Kisakalenterin lomakkeet
    //////////////////////////////////////////////////////////////
    
    ///////////////////////////////////////////////////////////////////////////////
    // Kisojen anonta
    ///////////////////////////////////////////////////////////////////////////////
    
    function get_competition_application ($mode = "add", $url,  $porrastettu = false, $event = array(), $jaos_id = null){
      
      $this->CI->load->library('form_builder', array('submit_value' => 'Ilmoita kilpailu'));
      $this->CI->load->library("vrl_helper");
      $this->CI->load->model("Tallit_model");     

      
      if(isset($event['kp'])){
         $event['kp'] = $this->CI->vrl_helper->sql_date_to_normal($event['kp']);
      }
      
      if(isset($event['vip'])){
         $event['vip'] = $this->CI->vrl_helper->sql_date_to_normal($event['vip']);
      }
      
   
      //haetaan tallivaihtoehdot ja valintaskripti
      $tunnus = "";

      if($mode == "add"){
         $tunnus = $this->CI->ion_auth->user()->row()->tunnus;
      }else {
         $tunnus = $event['tunnus'];
      }
      $tallilista = $this->CI->Tallit_model->get_users_stables($tunnus);
      $tallit = array();
      foreach ($tallilista as $talli){
         $tallit[$talli['tnro']] = $talli['tnro'];
      }
      $option_script = $this->CI->vrl_helper->get_option_script('jarj_talli', $tallit);

      
      $fields = array();
      if(!$porrastettu){
        $jaos_options = $this->CI->Jaos_model->get_jaos_option_list(true);
        $fields['jaos'] = array('type' => 'select', 'options' => $jaos_options, 'value' => $event['jaos'] ?? -1, 'class'=>'form-control');
      }
      $fields['kp'] = array('type' => 'date', 'first-day' => 1, 'date_format'=>'d.m.Y', 'label'=>'Päivämäärä', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['kp'] ?? "");
      $fields['vip'] = array('type' => 'date', 'first-day' => 1, 'date_format'=>'d.m.Y', 'label'=>'Viimeinen ilmoittautumispäivä', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['vip'] ?? "");
      
      if(!$porrastettu){
        $arvontatapa_options = $this->arvontatavat_options();
        $fields['url'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['url'] ?? "http://");
        $fields['arvontatapa'] = array('type' => 'select', 'options' => $arvontatapa_options, 'value' => $event['arvontatapa'] ?? 1, 'class'=>'form-control', 'required' => TRUE);
      }
      $fields['jarj_talli'] = array('type' => 'text', 'label'=>'Järjestävä talli', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['jarj_talli'] ?? "",
                                    'after_html'=> '<span class="form_comment">Laita tunnus muodossa XXXX0000. Omat tallisi (klikkaa lisätäksesi): ' .
                                    $option_script['list'] . '</span>' . $option_script['script']);

      $fields['info'] = array('type' => 'text', 'class'=>'form-control', 'required' => FALSE, 'value' => $event['info'] ?? "");
      
      if($porrastettu){
        $jaos = $this->CI->Jaos_model->get_jaos($jaos_id);
        $fields['s_hevosia_per_luokka'] = array('label' => 'Ratsukoita/luokka max', 'type' => 'number',
                                                'min' => $jaos['s_hevosia_per_luokka_min'], 'max'=>$jaos['s_hevosia_per_luokka_max'],
                                                'value' => $event['s_hevosia_per_luokka'] ?? $jaos['s_hevosia_per_luokka_max'],
                                                'after_html' => '<span class="form_comment">Ratsastaja voi ilmoittaa yhteen luokkaan 10% tästä lukemasta, eli esim.
                                                3 jos ratsukkomäärä on 30, ja 10, jos ratsukkomäärä on 100.</span>',
                                                'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
       $fields['s_luokkia_per_hevonen'] = array('label' => 'Luokkia/hevonen max', 'type' => 'number',
                                                'min' => $jaos['s_luokkia_per_hevonen_min'], 'max'=>$jaos['s_luokkia_per_hevonen_max'],
                                                'value' => $event['s_luokkia_per_hevonen'] ?? $jaos['s_luokkia_per_hevonen_max'],
                                                'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE); 
        
        
        $class_options = $this->CI->Jaos_model->get_class_options($jaos_id, true, true);
        $fields['luokat'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE,
                                  'before_html' => '<span class="form_comment">Valitse '.$jaos['s_luokkia_per_kisa_min'].'-'.min($jaos['s_luokkia_per_kisa_max'],sizeof($class_options)).' luokkaa.</span>',
                                  'options' => $class_options, 'value'=>$event['luokat'] ?? array(), 'class'=>'form-control', 'wrapper_tag' => 'li');

      }
      
       $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
      return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);

    }
    
    
    ///////////////////////////////////////////////////////////////////////////////
    // Kisakalenterin ylläpitofunktiot
    //////////////////////////////////////////////////////////////////////////////
    
    
    ### funktio mahis, joka laskee voiko ko. hlö pitää jaoksenalaisia kisoja

function mahis ($jaosnimi, $vrl_tunnus) {

	# Tunnukselle kertyneet etuuspisteet
	$etuuspisteet =  mysql_query("SELECT ".$jaosnimi." FROM tunnukset_etuuspisteet WHERE tunnus = ".$vrl_tunnus);
	$ep = mysql_fetch_array($etuuspisteet);

	$ep['pisteet'] = $ep[$jaosnimi];
	
	$haejaosID =  mysql_query("SELECT jaos_id FROM lista_jaokset WHERE lyhenne = '".$jaosnimi."'");
	$jaos_id = mysql_fetch_array($haejaosID) or die( mysql_error() );
	$jaos = $jaos_id['jaos_id'];

	// Jos pisteitä ei ole vielä annettu
	if(empty($ep['pisteet'])) { $ep['pisteet'] = 0; }

	#### KUINKA MONTA KISAA MAHDOLLISUUS JÄRJESTÄÄ ####
		/*
			EP		KUTSUT
			0-1		1 avoin kutsu
			2-4		3 avointa kutsua
			5-9		5 avointa kutsua
			10-24	7 avointa kutsua
			25-49	10 avointa kutsua
			50-99	20 avointa kutsua, kutsut suoraan kalenteriin
			100+	rajattomasti avoimia kutsuja, kutsut suoraan kalenteriin
		*/
		
		$jarjestettavia = 0;
		
		if(	$ep['pisteet'] >= 0 AND $ep['pisteet'] <= 1.99 ) {
			$jarjestettavia = 1;
		} elseif ( $ep['pisteet'] >= 2.00 AND $ep['pisteet'] <= 4.99 ) {
			$jarjestettavia = 3;
		} elseif ( $ep['pisteet'] >= 5.00  AND  $ep['pisteet'] <= 9.99 ) {
			$jarjestettavia = 5;
		} elseif ( $ep['pisteet'] >= 10.00 AND $ep['pisteet'] <= 24.99 ) {
			$jarjestettavia = 7;
		} elseif ( $ep['pisteet'] >= 25.00 AND $ep['pisteet'] <= 49.99 ) {
			$jarjestettavia = 10;
		} elseif ( $ep['pisteet'] >= 50.00 AND $ep['pisteet'] <= 99.99 ) {
			$jarjestettavia = 20;
		} elseif ( $ep['pisteet'] >= 100 ) {
			$jarjestettavia = 0.145; // infinity
		} else {
			$jarjestettavia = 0;
		}
		
		/*
		Katsotaan paljonko avoimia kutsuja kalenterissa
		Miinustetaan avoimet järjestettävistä $jarjestettavia
		
		$jarjestettavia = $jarjestettavia - $avoimet;
		*/
		
		$laske_avoimet =  mysql_query("
			SELECT COUNT(kisa_id) AS avoimia 
			FROM kisat_kisakalenteri 
			WHERE
				jaos = ".$jaos." AND
				vanha = 0 AND
				tunnus = ".$vrl_tunnus." AND
				hyvaksytty != '0000-00-00 00:00:00' AND
				(tulokset = 0 OR tulokset IS NULL)
				"); 
			/* */
			
		$avoimia_kalenterissa = mysql_fetch_array($laske_avoimet);
		if(empty($avoimia_kalenterissa['avoimia'])) { $avoimia_kalenterissa['avoimia'] = 0;}
		
		$laske_tulosjono =  mysql_query("
			SELECT COUNT(tulos_id) as avoimia 
			FROM kisat_tulokset
			LEFT JOIN kisat_kisakalenteri ON kisat_tulokset.kisa_id = kisat_kisakalenteri.kisa_id
			WHERE
				kisat_kisakalenteri.jaos = ".$jaos." AND
				kisat_tulokset.tunnus = ".$vrl_tunnus." AND
				kisat_kisakalenteri.vanha = 0 AND
				kisat_kisakalenteri.hyvaksytty IS NOT NULL AND
				(kisat_tulokset.hyvaksytty IS NULL OR kisat_tulokset.hyvaksytty = '0000-00-00 00:00:00')
			");
			
		$tulosjonossa = mysql_fetch_array($laske_tulosjono);
		if(empty($tulosjonossa['avoimia'])) { $tulosjonossa['avoimia'] = 0;}
		
		$voi_jarjestaa = $jarjestettavia - $avoimia_kalenterissa['avoimia'];
		
		if($voi_jarjestaa > 0 AND $voi_jarjestaa != 0.145 ) {
			$mahis = 'Voi järjestää vielä '.$voi_jarjestaa.' kpl '.$jaosnimi.':n alaisia kisoja. Avoimia kalenterissa: '.$avoimia_kalenterissa['avoimia'].' kpl';
			
		} elseif ($voi_jarjestaa == 0.145) {
			$mahis = 'Voi järjestää rajattomasti kisoja.';
			
		} elseif ($voi_jarjestaa == 0 OR $voi_jarjestaa < 0) {
			$mahis = '<span class="red" style="margin: 0;">Ei voi järjestää '.$jaosnimi.':n alaisia kisoja, koska kalenterissa avoimia kisoja. ';
			
			if($tulosjonossa['avoimia'] > 0) {
				$mahis .= 'Tulosjonossa käsittelemättömiä tuloshakemuksia '.$tulosjonossa['avoimia'].' kpl.';
			}
			
			$mahis .= '</span>';
			
		} else {
			$mahis = '<span class="red">Virhe '.$jaosnimi.':n etuuspisteiden laskussa! Pisteitä '.$ep['pisteet'].' ep.</span>';
		}
		
		return $mahis;
	
}
    
    
    
    
    /*
    
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
    }*/

}