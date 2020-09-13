<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Kisajarjestelma
{

    public function __construct()
        {
                    $this->CI =& get_instance();
                    $this->CI->load->model('Jaos_model');
                    $this->CI->load->library('Vrl_helper');

        }
        
        
    ////////////////////////////////////////////////////
    // SETTINGS
    ///////////////////////////////////////////////////
        
        
    private  $old_leveled_to_new = '2020-08-30'; // ÄLÄ MUOKKAA
    var $CI;
    
    public function new_leveled_start_time (){
        return $this->old_leveled_to_new;
    }

    
    public function nayttelyjaos($jaos_id){
        //todo tarkempi selvitys
         if ( $jaos_id == 7 ) { return true;}
         else { return false;}
    }
    
    public function sijoittuu($osallistujia, $jaos_id){
    
        $sijoittuu = 0;
        //NJ:ssä ei sijoituta samalla tavalla.
        if ( $this->nayttelyjaos($jaos_id )) { $sijoittuu = 0; }
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
    
    public function sallitutKisamaarat($etuuspisteet, $jaos_id){
        //nj:ssä ei sijoituta samalla tavalla
         if ( $this->nayttelyjaos($jaos_id) ) { $jarjestettavia = 1; }
        
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
		if(	$etuuspisteet <= 1.99 ) {
			$jarjestettavia = 1;
		} elseif ( $etuuspisteet >= 2.00 AND $etuuspisteet <= 4.99 ) {
			$jarjestettavia = 3;
		} elseif ( $etuuspisteet >= 5.00  AND  $etuuspisteet <= 9.99 ) {
			$jarjestettavia = 5;
		} elseif ( $etuuspisteet >= 10.00 AND $etuuspisteet <= 24.99 ) {
			$jarjestettavia = 7;
		} elseif ( $etuuspisteet >= 25.00 AND $etuuspisteet <= 49.99 ) {
			$jarjestettavia = 10;
		} elseif ( $etuuspisteet >= 50.00 AND $etuuspisteet <= 99.99 ) {
			$jarjestettavia = 20;
		} elseif ( $etuuspisteet >= 100 ) {
			$jarjestettavia = 100; 
		}
        return $jarjestettavia;
		
    }
    
    public function directlyCalender($etuuspisteet, $jaos_id){
        if ( $this->nayttelyjaos($jaos_id) ) { return false; }
        else if ( $etuuspisteet >= 50.00) {return true; }
        else { return false;}
    

    }
    
    //Maksimiosallistujamäärä
    public function max_hevosia_per_luokka_per_ratsastaja($max_hevosia_per_luokka){
        return min(ceil($max_hevosia_per_luokka*0.1),10);
        
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
    
    public function competition_date_days_from_vip(){
        return 1;
    }
    
    public function competition_date_max(){
      $lastDateOfNextMonth =strtotime('last day of next month') ;
      return date('Y-m-d', $lastDateOfNextMonth);
    }
    
    public function competition_date_min($vip_date){
     return date('Y-m-d', strtotime($vip_date.' + '.$this->competition_date_days_from_vip().' day'));
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

      //jos tähän on muutettu SQL-date, korjataan tavalliseksi
      if(isset($event['kp']) && $this->CI->vrl_helper->validateDate($event['kp'], 'Y-m-d')){
         $event['kp'] = $this->CI->vrl_helper->sql_date_to_normal($event['kp']);
      }
      
      if(isset($event['vip']) && $this->CI->vrl_helper->validateDate($event['vip'], 'Y-m-d')){
         $event['vip'] = $this->CI->vrl_helper->sql_date_to_normal($event['vip']);
      }
      //TODO_ Takaaja
   
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
      
      $viptext = "";
      $comptext = "Vähintään ". $this->competition_date_days_from_vip() . " päivää viimeisen ilmoittautumispäivän jälkeen. Korkeintaan " . date('d.m.Y', strtotime($this->competition_date_max())) .".";
      
      if($porrastettu){
        $viptext = "Tänään ilmoitetulla kilpailulla aikaisintaan ".date('d.m.Y', strtotime($this->competition_vip_date_leveled_min())).".";
      }else {
            $viptext = "Tänään ilmoitetulla kilpailulla aikaisintaan ".date('d.m.Y', strtotime($this->competition_vip_date_normal_min()))
            ." (poikkeuksena suoraan kalenteriin menevillä kilpailuilla aikaisintaan ". date('d.m.Y', strtotime($this->competition_vip_date_direct_min())) . ")";
      }
      
      $fields['kp'] = array('type' => 'date', 'first-day' => 1, 'date_format'=>'d.m.Y', 'label'=>'Päivämäärä', 'class'=>'form-control', 'required' => TRUE,
                            'value' => $event['kp'] ?? "", 'after_html'=> '<span class="form_comment">'.$comptext.'</span>');
      $fields['vip'] = array('type' => 'date', 'first-day' => 1, 'date_format'=>'d.m.Y', 'label'=>'Viimeinen ilmoittautumispäivä', 'class'=>'form-control',
                             'required' => TRUE, 'value' => $event['vip'] ?? "", 'after_html'=> '<span class="form_comment">'.$viptext.'</span>');
      
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
        $fields['s_hevosia_per_luokka'] = array('label' => 'Ratsukoita/luokka max', 'type' => 'number', 'required'=>TRUE,
                                                'min' => $jaos['s_hevosia_per_luokka_min'], 'max'=>$jaos['s_hevosia_per_luokka_max'],
                                                'value' => $event['s_hevosia_per_luokka'] ?? $jaos['s_hevosia_per_luokka_max'],
                                                'after_html' => '<span class="form_comment">Ratsastaja voi ilmoittaa yhteen luokkaan 10% tästä lukemasta, eli esim.
                                                3 jos ratsukkomäärä on 30, ja 10, jos ratsukkomäärä on 100.</span>',
                                                'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
       $fields['s_luokkia_per_hevonen'] = array('label' => 'Luokkia/hevonen max', 'type' => 'number', 'required'=>TRUE,
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
    
    public function validate_competition_application($porrastettu = false){
    
      $this->CI->load->library('form_validation');
      
      $this->CI->form_validation->set_rules('kp', 'Kisapäivä', 'min_length[10]|max_length[10]|required');
      $this->CI->form_validation->set_rules('vip', 'Viimeinen ilmoittautumispäivä', 'min_length[10]|max_length[10]|required');
      $this->CI->form_validation->set_rules('jarj_talli', 'Talli', 'min_length[6]|max_length[10]|required');
      $this->CI->form_validation->set_rules('info', 'Info', 'min_length[5]|max_length[300]');

      
       if(!$porrastettu){
            $this->CI->form_validation->set_rules('jaos', 'Jaos', 'min_length[1]|max_length[3]|numeric|required');
            $this->CI->form_validation->set_rules('arvontatapa', 'Arvontatapa', 'min_length[1]|max_length[3]|numeric|required');
            $this->CI->form_validation->set_rules('url', 'Kutsun osoite', 'min_length[15]|max_length[300]|required');


       }
       
       if($porrastettu){
            $this->CI->form_validation->set_rules('s_hevosia_per_luokka', 'Hevosia per luokka', 'min_length[1]|max_length[3]|numeric|required');
            $this->CI->form_validation->set_rules('s_luokkia_per_hevonen', 'Luokkia per hevonen', 'min_length[1]|max_length[3]|numeric|required');
            $this->CI->form_validation->set_rules('luokat[]','Luokat', 'required');


       }
    
       $ok = $this->CI->form_validation->run();
        return $ok;
    
    }
    
    public function parse_competition_application(){
        $application = array();
        if ($this->CI->input->post('kp')){
            $application['kp'] = $this->CI->input->post('kp');
        }
        if ($this->CI->input->post('vip')){
            $application['vip'] = $this->CI->input->post('vip');
        }
        if ($this->CI->input->post('jarj_talli')){
            $application['jarj_talli'] = $this->CI->input->post('jarj_talli');
        }
        if ($this->CI->input->post('info')){
            $application['info'] = $this->CI->input->post('info');
        }
        if ($this->CI->input->post('jaos')){
            $application['jaos'] = $this->CI->input->post('jaos');
        }
        if ($this->CI->input->post('url')){
            $application['url'] = $this->CI->input->post('url');
        }
        if ($this->CI->input->post('arvontatapa')){
            $application['arvontatapa'] = $this->CI->input->post('arvontatapa');
        }
        if ($this->CI->input->post('s_luokkia_per_hevonen')){
            $application['s_luokkia_per_hevonen'] = $this->CI->input->post('s_luokkia_per_hevonen');
        }
        if ($this->CI->input->post('s_hevosia_per_luokka')){
            $application['s_hevosia_per_luokka'] = $this->CI->input->post('s_hevosia_per_luokka');
        }
        if ($this->CI->input->post('luokat')){
            $application['luokat'] = $this->CI->input->post('luokat');
        }
        if ($this->CI->input->post('takaaja')){
            $application['takaaja'] = $this->CI->input->post('takaaja');
        }
        
        return $application;
        
        
    }
    
    
public function check_competition_info($mode = "add", &$kisa, &$msg, $direct = false, $nollattu = false){
    if($kisa['porrastettu']){
        $kisa['arvontatapa'] = 3;
    }
    if($this->nayttelyjaos($kisa['jaos'])){
        $kisa['arvontatapa'] = 5;
    }

    //jos kisa ei ole porrastettu tai näyttely, ja lisäävän käyttäjän pisteet on nollattu, pitää olla oikea takaaja.
    if (!$kisa['porrastettu'] && !$this->nayttelyjaos($kisa['jaos']) && $mode == "add" && $nollattu
             && !(isset($kisa['takaaja']) && $kisa['takaaja'] != $kisa['tunnus'] && $this->vrl_helper->check_vrl_syntax($kisa['takaaja'])
                 && $this->tunnukset_model->onko_tunnus($this->vrl_helper->vrl_to_number($kisa['takaaja'])))){
        $msg = "Tarvitset kilpailullesi takaajan. Sen tulee olla olemassaoleva VRL-tunnus";
        return false;
                

    }  //jos takaaja on jostain syystä annettu vaikkei olisi pakko, tarkastetaan silti 
    if(isset($kisa['takaaja']) && !($kisa['takaaja'] != $kisa['tunnus'] && $this->vrl_helper->check_vrl_syntax($kisa['takaaja'])
                 && $this->tunnukset_model->onko_tunnus($this->vrl_helper->vrl_to_number($kisa['takaaja'])))){
        $msg  = "Annoit virheellisen takaajan tunnuksen.";
        return false;
    }
    
    
    //tarkastetaan päivämäärät
    if (!($this->CI->vrl_helper->validateDate($kisa['kp'])
        && $this->CI->vrl_helper->validateDate($kisa['vip']))){
        
        $msg = "Virheellinen kilpailupäivä tai viimeinen ilmoittautumispäivä.";
        return false;
        
    }
    
    else{
        
        $vip_date = date('Y-m-d', strtotime($kisa['vip']));
        $comp_date = date('Y-m-d', strtotime($kisa['kp']));
        
        if($kisa['porrastettu']){
            //porrastetut menevät aina suoraan kalenteriin
            if ( $vip_date < $this->competition_vip_date_leveled_min()){
            $msg = 'Porrastettujen kilpailujen viimeinen ilmoittautumispäivä on liian lähellä nykyhetkeä.';
            return false;
            }
        }else if(!$this->nayttelyjaos($kisa['jaos'])) {
            //perinteiset menevät kalenteriin omalla tavallaan
            if($direct && $vip_date < $this->competition_vip_date_direct_min()){
                $msg = 'Suoraan kalenteriin menevän perinteisen kilpailun viimeinen ilmoittautumispäivä on liian lähellä nykyhetkeä.';
                return false;
            }else if(!$direct && $vip_date < $this->competition_vip_date_normal_min()){
                $msg = 'Hakemusjonoon menevän perinteisen kilpailun viimeinen ilmoittautumispäivä on liian lähellä nykyhetkeä.';
                return false;
            }
            
        } else {
            //TODO: Miten näyttelyjaos tarkastetaan?
        }
        
        if($comp_date < $this->competition_date_min($vip_date)){
            $msg = 'Kisapäivän pitää olla vasta viimeisen ilmoittautumispäivän jälkeen.';
            return false;
        }
        
        if( !($comp_date == $this->competition_date_max() || $comp_date < $this->competition_date_max())){
            $msg = 'Kisapäivä saa olla korkeintaan seuraavan kuun lopussa ('. $comp_date . ' < ' . $this->competition_date_max() . ')';
            return false;
        }
    
    }
    
        if (!(($kisa['porrastettu'] && $kisa['arvontatapa'] == 3)
            || ($this->nayttelyjaos($kisa['jaos']) && $kisa['arvontatapa'] == 5)
            || array_key_exists($kisa['arvontatapa'] ,$this->arvontatavat_options()))){
            $msg = "Virheellinen arvontatapa";
            return false;
        }
        
        $this->CI->load->model('Tallit_model');
        if(!$this->CI->Tallit_model->is_tnro_in_use($kisa['jarj_talli'])){
            $msg = 'Järjestävää tallia ei ole olemassa.';
            return false;
        }
        
        if(!$this->CI->Kisakeskus_model->check_date_for_competition ($kisa['tunnus'], $kisa['jarj_talli'], $this->CI->vrl_helper->normal_date_to_sql($kisa['kp']), $kisa['jaos'])){
            $msg = 'Et voi järjestää samalla tallilla useampia kuin yhdet saman jaoksen kilpailut/päivä';
            return false;
        }
        
        
        $jaos = $this->CI->Jaos_model->get_jaos($kisa['jaos']);
        if(sizeof($jaos) == 0 || $jaos['toiminnassa'] == false){
            $msg = 'Virheellinen tai ei toiminnassa oleva jaos.';
            return false;
        }else {
            $kisa['laji'] = $jaos['laji'];

        }
        
        if($kisa['porrastettu']){
            if($jaos['s_salli_porrastetut'] == false){
                $msg = "Yrität järjestää porrastettuja kilpailuja jaoksella, jolla ei ole porrastetut sallittuja.";
                return false;
            }
            if(($kisa['s_luokkia_per_hevonen'] > $jaos['s_luokkia_per_hevonen_max'])
               || ($kisa['s_luokkia_per_hevonen'] < $jaos['s_luokkia_per_hevonen_min']) ){
                $msg = "Hevosen luokkaosallistumisrajoitus ei vastaa jaoksen sääntöjä " . $jaos['s_luokkia_per_hevonen_min'] . "-" . $jaos['s_luokkia_per_hevonen_max'] . " luokkaa/hevonen.";
                return false;
            }
            if(($kisa['s_hevosia_per_luokka'] > $jaos['s_hevosia_per_luokka_max'])
               || ($kisa['s_hevosia_per_luokka'] < $jaos['s_hevosia_per_luokka_min']) ){
                $msg = "Luokan maksimiosallistujamäärä ei vastaa jaoksen sääntöjä " . $jaos['s_hevosia_per_luokka_min'] . "-" . $jaos['s_hevosia_per_luokka_max'] . " ratsukkoa/luokka.";
                return false;
            }
            if((sizeof($kisa['luokat']) > $jaos['s_luokkia_per_kisa_max'])
               || (sizeof($kisa['luokat']) < $jaos['s_luokkia_per_kisa_min'])){
                $msg = "Luokkamäärä ei vastaa jaoksen sääntöjä ". $jaos['s_luokkia_per_kisa_min'] . "-".$jaos['s_luokkia_per_kisa_max'] . " luokkaa/kilpailu.";
                return false;
            }
            
            $sallitut_luokat = $this->CI->Jaos_model->get_class_options($jaos['id'], true, true);
            
            foreach ($kisa['luokat'] as $luokka){
                if(!array_key_exists($luokka, $sallitut_luokat)){
                    $msg = "Virheellinen luokka (".$luokka.").";
                    return false;
                    break;
                
                }
            }
        }
            
    return true;    
    }
    
    public function add_new_competition($kutsu, &$msg, $direct){
        $this->CI->load->model('Kisakeskus_model');
        $luokat = array();
        if(isset($kutsu['luokat'])){
            $luokat = $kutsu['luokat'];
            unset($kutsu['luokat']);
        }
        return $this->CI->Kisakeskus_model->insertNewCompetition($kutsu, $luokat, $direct, $msg);
    }
    

    
    ///////////////////////////////////////////////////////////////////////////////
    // Kisakalenterin ylläpito
    //////////////////////////////////////////////////////////////////////////////


    /*
function allowedCompetitions ($jaos, $vrl_tunnus, $etuuspisteet = null) {

	# Tunnukselle kertyneet etuuspisteet
	
    if(!isset($etuuspisteet)){
        $etuuspisteet =  $this->CI->Jaos_model->GetEtuuspisteet($jaos, $vrl_tunnus);
    }
	$ep = 0;
    if(sizeof($etuuspisteet) > 0 && !empty($etuuspisteet['pisteet'])){
        $ep = $etuuspisteet['pisteet'];
    }
	
    # Miten monta kisaa on mahdollisuus järjestää
    $jarjestettavia = $this->sallitutKisamaarat($ep, $jaos);
		
    
    Katsotaan paljonko avoimia kutsuja kalenterissa
    Miinustetaan avoimet järjestettävistä $jarjestettavia
    
    $jarjestettavia = $jarjestettavia - $avoimet;
    
		
		$avoimet = $this->CI->Jaos_model->usersOpenCompetitions($jaos, $vrl_tunnus);
		
		
		return $jarjestettavia - $avoimet;
		

			
}
    */

}