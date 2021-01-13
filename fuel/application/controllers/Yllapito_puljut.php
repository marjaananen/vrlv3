<?php
class Yllapito_puljut extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'alayhdistys', 'alayhdistys-yp');
    private $url;
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
        $this->load->model('Sport_model');
        $this->load->model('Jaos_model');
        $this->load->model('Breed_model');
        $this->load->library('Jaos');
        $this->load->library('Events');

        $this->url = "yllapito/alayhdistykset/";
        
    }
    
    private function _is_pulju_admin(){
        return $this->user_rights->is_allowed(array('admin', 'alayhdistys'));
    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    function index(){
        $this->yhdistykset();
    }
   

    
     
    
    
    /////////////////////////////////////////////////////////////////7
    ///jaoksen hallinta
    
    public function yhdistykset($msg = null){
        $data['title'] = 'VRL:n alaiset alayhdistykset';
        if (isset($msg)){
            $data['msg'] = $msg['msg'];
            $data['msg_type'] = $msg["msg_type"];
        }
        $data['text_view'] = "";
        $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'yhdistys/poista/', $this->url.'yhdistys/muokkaa/', $this->url.'tapahtumat/', true);
        $this->fuel->pages->render('misc/haku', $data);
    }
    
    

    
    public function lisaa_yhdistys($msg = ""){
        if($this->_is_pulju_admin()){   
        
            $data['msg'] = $msg;
            $data['title'] = 'Lisää yhdistys';
            $data['text_view'] = "";
   
           //start the form
               
           if($this->input->server('REQUEST_METHOD') == 'POST'){
               $tid = 0;
               $jaos = array();
               $jaos = $this->jaos->read_jaos_input($jaos, true);
   
               if ($this->jaos->validate_jaos_form('new', true, true) == FALSE)
                   {
                       $data['msg'] = "Tallennus epäonnistui!";
                       $data['msg_type'] = "danger";
                       $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_yhdistys", "new", true, $jaos, true);
                   }
               else if ($this->jaos->validate_jaos("new", true, $jaos, $data['msg']) == false){
                   $data['msg_type'] = "danger";
                   $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_yhdistys", "new", true, $jaos, true);
               }
   
               else
                   {
                      $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_yhdistys", "new", true, array(), true);
                       //add 
                      $tid = $this->Jaos_model->add_pulju($data['msg'], $jaos);
                      $data['msg_type'] = "danger";
   
                      if ($tid !== false){
                      
                       $data['msg'] = "Yhdistyksen luonti onnistui!";
                       $data['msg_type'] = "success";
                       }
                   }
           }
           
           else  {
               $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_yhdistys", "new", true, array(), true);
           }
                   
           //start the list      
           $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'yhdistys/poista/', $this->url.'yhdistys/muokkaa/',$this->url.'tapahtumat/',true);
           $this->fuel->pages->render('misc/haku', $data);
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja yhdistysvastaavalla on oikeus muokata näitä tietoja."));
            return;
        }

    }
    

    
    public function yhdistys ($tapa, $id, $sivu = 'tiedot', $tapa2 = null, $sub_id = null){
        $data = array();
        $data['jaos'] = $this->Jaos_model->get_pulju($id);

            
        if(sizeof($data['jaos']) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Yhdistystä jota yritit muokata, ei ole olemassa."));
                return;
        }
        else if($tapa == "poista"){
                $this->_delete_pulju($id);
            
        }
        else if ($tapa == "muokkaa"){
            $data['jaos']['pulju'] = true;
            $data['sivu'] = $sivu;
            $data['url'] = $this->url . "yhdistys/muokkaa/".$id."/";
            $edit_url = $this->url . "yhdistys/muokkaa/".$id."/".$sivu;
            if(!$this->_is_editing_allowed($id, $data['msg'])){
                $data['msg_type'] = "danger";
                $this->yhdistykset(array("msg" => $data['msg'], "msg_type"=>$data['msg_type']));
            }
            
            else if($sivu == "tiedot"){
                $this->_handle_tiedot_edit($id, $data, $edit_url);
            }else if($sivu == "online"){
                $this->_handle_toiminnassa_edit($id, $data, $edit_url);
            }else if($sivu == "rodut"){
                $this->_handle_rodut_edit($id, $data, $edit_url);
            }else if($sivu == "omistajat"){
                $this->_handle_pulju_owners($id, $tapa2, $sub_id, $data, $data['url']);
            }

           //
        }
            
        else {
            $this->yhdistykset();
        }
       
        
    }
    
    private function _handle_tiedot_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_jaos_input($jaos, true);
            if ($this->jaos->validate_jaos_form('edit', $this->_is_pulju_admin(), true) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
            }
            else if ($this->jaos->validate_jaos("edit", $this->_is_pulju_admin(), $jaos, $data['msg'], true, $id) == false){
                $data['msg_type'] = "danger";
            }
            else  {
                   $tid = $this->Jaos_model->edit_pulju($id, $jaos);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Yhdistyksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                        $data['jaos'] = $jaos;
                    }
            }
            
        }
        $data['form'] = $this->jaos->get_jaos_form($edit_url, "edit", $this->_is_pulju_admin(), $jaos, true);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    
    private function _handle_toiminnassa_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_toiminnassa_input($jaos, true);
            if ($this->jaos->validate_toiminnassa_form($id, $jaos, $data['msg'], true) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui! " . $data['msg'];
                    $data['msg_type'] = "danger";
            }
            else  {
                   $edit_data = array("toiminnassa"=>$jaos['toiminnassa']);
                   $tid = $this->Jaos_model->edit_pulju($id, $edit_data);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Yhdistyksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                   }
            }
                    
        } 
        $data['form'] = $this->jaos->get_toiminnassa_form($edit_url, $jaos, true);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    
    private function _handle_rodut_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        $breeds = array();
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_breed_input($breeds);

            if ($this->jaos->validate_breed_form($id, $breeds, $jaos, $data['msg'], true) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui! " . $data['msg'];
                    $data['msg_type'] = "danger";
            }
            else  {
                   $tid = $this->Breed_model->update_pulju_breeds($id, $breeds, $data['msg']);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Yhdistyksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                   }
            }
                    
        }
        $data['form'] = $this->jaos->get_breed_form($edit_url, $this->Breed_model->get_breed_array_by_pulju($id), true);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    private function _delete_pulju($id){
                if(!$this->_is_pulju_admin()){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja yhdistysvastaavalla on oikeus poistaa jaos."));
                return;
            }else {
                $msg = "";
                if($this->jaos->delete_pulju($id, $msg)){
                
                    $data['msg'] = "Yhdistyksen poisto onnistui!";
                    $data['msg_type'] = "success";
                }
                else {
                    $data['msg'] = "Yhdistyksen poisto epäonnistui! " . $msg;
                    $data['msg_type'] = "danger";
                }
                
                $this->yhdistykset(array("msg" => $data['msg'], "msg_type"=>$data['msg_type']));
            }
    }

    
    
    private function _handle_pulju_owners($id, $tapa, $owner, &$data, $edit_url){
        $this->load->library('ownership');
        $mode = "";
            if($this->_is_pulju_admin()){
               $mode = "admin";
        }
 
        if($this->_is_pulju_admin() || $this->ownership->is_pulju_main_owner($this->ion_auth->user()->row()->tunnus, $id)){				
            $this->ownership->handle_pulju_ownerships($mode, $tapa, $this->ion_auth->user()->row()->tunnus, $owner, $id, $data);				
            $data['form'] = $this->ownership->get_owner_adding_form($edit_url, "Ylläpitäjä", "Työntekijä");
            $data['list'] = $this->ownership->pulju_ownerships($id, true, $edit_url.'/');
        } else {
            $data['list'] = $this->ownership->pulju_ownerships($id, false, $edit_url.'/');
        }
        
        $this->fuel->pages->render('jaokset/muokkaa', $data);
    }
    
    /////////////////////////////////////
    // Jaoksen tapahtumat
    
public function tapahtumat($jaos_id = null, $tapa=null, $id=null, $tapa2 = null, $os_id = null){
        $url_begin = $this->url."tapahtumat/";
        if($jaos_id == null){
            $this->events->select_event_organizer($jaos_id, $url_begin, true);

            
        }else {
                     
            $data = array();
            $data['jaos'] = $this->Jaos_model->get_pulju($jaos_id);
    
            $edit_url = $url_begin . $jaos_id;
            if(sizeof($data['jaos']) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Yhdistystä jonka tapahtumia yritit hakea, ei ole olemassa."));
                return;
            }
            else if(!$this->_is_editing_allowed($jaos_id, $msg)){
               $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtuman käsittely epäonnistui. " . $msg));

            }
            else if($tapa == "poista"){
                $this->events->delete_event($id, $data, true);
                $this->events->print_event_list($data, $edit_url, true);

            }
            
            else if($tapa == "lisaa"){
                $this->events->add_event($id, $data, $edit_url, true);
                $this->events->print_event_list($data, $edit_url, true, $data['event_data']);
                
            }    
            else if($tapa == "muokkaa"){
                $this->_edit_event($id, $data['jaos'], $tapa2, $os_id, $edit_url);
                
            }else {
                $this->events->print_event_list($data, $edit_url, true);
            }
        }
        
    }
    
    
    private function _edit_event($id, $jaos, $tapa = null, $os_id = null, $edit_url){
        $this->load->library('vrl_helper');
    
        $tapahtuma = $this->Jaos_model->get_event($id, null, $jaos['id']);
        if (sizeof($tapahtuma)>0){
            //poistetaan palkittu 
            if (isset($tapa) && $tapa == "poista"){
                $this->events->delete_event_horse($os_id, $id);
                $this->tapahtumat($jaos['id'], "muokkaa", $id);
            //muokataan tapahtumaa
            } else {
                 $this->events->edit_event($id, $jaos, true);
                }
                $this->events->tapahtuma($id, true, $edit_url);            
            
            

            
        }else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtumaa jota yrität muokata ei ole olemassa."));

        }        
        
    }
        

    
    
    
    
    
    
    private function _is_editing_allowed($id, &$msg){
        
        $data['jaos'] = $this->Jaos_model->get_pulju($id);

        if(sizeof($data['jaos']) == 0){
            $msg = "Yhdistystä jota yritit muokata, ei ole olemassa.";
            return false;
        }
        //only logged in can edit
		if(!($this->ion_auth->logged_in())){
            $msg = "Kirjaudu sisään muokataksesi!";
			return false;
		}//jos et ole admin, pitää olla ko. jaoksen omistaja
        if(!$this->_is_pulju_admin() && !$this->Jaos_model->is_pulju_owner($this->ion_auth->user()->row()->tunnus, $id, 1)){
             $msg = "Vain admin, alayhdistysvastaava ja yhdistyksen ylläpitäjä voi muokata jaosta.";
             return false;
        }
        
        return true;
    }
    
    
    
    
    
  
    

    
}
    

?>