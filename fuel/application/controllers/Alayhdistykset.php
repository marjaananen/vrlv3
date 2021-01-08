<?php
class Alayhdistykset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Jaos_model");
      

    }
    
    function index ()
    {
        
        $vars = array();
    
        
        $this->fuel->pages->render('puljut/index', $vars);
    }
    
    function kantakirjat(){
        
        $vars = array();
        $vars['puljut'] = $this->Jaos_model->get_puljut_full(1, true);
        $vars['puljut_offline'] = $this->Jaos_model->get_puljut_full(1, false);
        $vars['title'] = "VRL:n alaiset kantakirjat";
        $this->fuel->pages->render('puljut/puljulista', $vars);


    }
    
    function rotuyhdistykset(){
        
        $vars = array();
        $vars['puljut'] = $this->Jaos_model->get_puljut_full(2, true);
        $vars['puljut_offline'] = $this->Jaos_model->get_puljut_full(2, false);
        $vars['title'] = "VRL:n alaiset rotuyhdistykset";
        $this->fuel->pages->render('puljut/puljulista', $vars);


    }
    
    function laatuarvostelut(){
        
        $vars = array();
        $vars['puljut'] = $this->Jaos_model->get_puljut_full(3, true);
        $vars['puljut_offline'] = $this->Jaos_model->get_puljut_full(3, false);
        $vars['title'] = "VRL:n alaiset laatuarvostelut";
        $this->fuel->pages->render('puljut/puljulista', $vars);


    }
    
    function kilpailujaokset ()
    {
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full();     
        $this->fuel->pages->render('jaokset/jaoslista', $vars);
    }
    
    function tapahtumat($type = "jaokset" ){
        $vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Päivämäärä', 'key' => 'pv', 'type'=>'date');
        $vars['headers'][3] = array('title' => 'Järjestäjä', 'key' => 'lyhenne');

		$vars['headers'][4] = array('title' => 'Otsikko', 'key' => 'otsikko');
        		$vars['headers'][5] = array('title' => 'Tulokset', 'key' => 'id', 'key_link' => site_url('alayhdistykset/tapahtuma/'));

     
		$vars['headers'] = json_encode($vars['headers']);
        
        $search_type = null;
        $kisajaos = false;
        
        if($type == "jaokset"){
            $kisajaos = true;
					
        }else if($type == "kantakirjat"){
            $search_type = 1;
        }else if($type == "laatuarvostelut"){
            $search_type = 3;
        }else if($type == "rotuyhdistykset"){
            $search_tyoe = 2;
        }
		$vars['data'] = json_encode($this->Jaos_model->get_event_list_by_type($kisajaos, $search_type));
                
        $vars['sivu'] = $type;
        $vars['lista'] =  $this->load->view('misc/taulukko', $vars, TRUE);
        
        $this->fuel->pages->render('puljut/tapahtumat', $vars);
    }
    
    function tapahtuma($id = null){
        $data = array();
        $this->load->library("jaos");
        $data['tapahtumatyyppi'] ="Muu arvostelutilaisuus";
        $data['tapahtuma'] = $this->Jaos_model->get_event($id);

        if(sizeof($data['tapahtuma']) >0){
            if(isset($data['tapahtuma']['jaos_id'])){
                $data['jaos'] = $this->Jaos_model->get_jaos($data['tapahtuma']['jaos_id']);
                $data['tapahtumatyyppi'] = "Kilpailujaoksen laatuarvostelutilaisuus";
            }else {
                $data['jaos'] = $this->Jaos_model->get_pulju($data['tapahtuma']['pulju_id']);
                
                if($data['jaos']['tyyppi'] == 1){
                    $data['tapahtumatyyppi'] = "Kantakirjaustilaisuus";
                }else if($data['jaos']['tyyppi'] == 3){
                    $data['tapahtumatyyppi'] = "Laatuarvostelutilaisuus";
                }
                
            }
             $data['palkitut'] = $this->jaos->tapahtumaosallistujat($id, null);
            
            $this->fuel->pages->render('puljut/tapahtuma', $data);
                
        }else {
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Valitsemaasi tapahtumaa ei löydy."));

        }
    }
    
   

    

	
   
}
?>






