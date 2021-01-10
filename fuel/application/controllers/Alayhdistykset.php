<?php
class Alayhdistykset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Jaos_model");
        $this->load->library("Events");
      

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
        
        
		$vars['data'] = json_encode($this->events->get_event_list_by_type($type));
                
        $vars['sivu'] = $type;
        $vars['lista'] =  $this->load->view('misc/taulukko', $vars, TRUE);
        
        $vars['title'] = "Tapahtuma-arkisto";
        $vars['text_view'] =  $this->load->view('puljut/tapahtumat_text', null, TRUE);
        $this->fuel->pages->render('puljut/tapahtumat', $vars);
    }
    
    function tapahtuma($id = null){
        $this->events->tapahtuma($id);
    }
    
   

    

	
   
}
?>






