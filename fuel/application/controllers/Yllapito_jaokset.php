<?php
class Yllapito_jaokset extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'jaos', 'jaos-yp');
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
        $this->load->model('Trait_model');
        $this->load->library('Jaos');
        $this->url = "yllapito/jaokset/";
                $this->load->library('Events');

        
    }
    
    private function _is_jaos_admin(){
        return $this->user_rights->is_allowed(array('admin', 'jaos'));
    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    function index(){
        $this->jaokset();
    }
   
    
   
    
    //////////////////////////////////
    //Lajit
    
    
     function lajit($msg = array()){
        
        if(!$this->_is_jaos_admin()){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));

            return;
        }
        
         $data = $msg;
         $data['title'] = 'Muokkaa lajilistaa';
         $data['text_view'] = "<p>Olemassaolevan lajin muokkaaminen vaikuttaa kaikkiin hevosiin, joille ko. painotus on rekisteröity ja kaikkiin jaoksiin joita se koskee! Lajia ei voi poistaa, jos sille on rekisteröity yksikin hevonen tai jaos.</p>";
         

        //start the form
            
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            if ($this->_validate_sport_form('new') == FALSE)
                {
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['form'] = $this->_get_sport_form('new');

                }

            else
                {
                   $data['form'] = $this->_get_sport_form('new');

                    //add 
                   $tid = $this->Sport_model->lisaa_sport($data['msg'], $this->input->post('painotus'), $this->input->post('lyhenne'));
                   
                   if ($tid !== false){
                   
                    $data['msg'] = "Julkaisu onnistui!";
                    $data['msg_type'] = "success";
                    }
                    else {
                        $data['msg_type'] = "danger";

                    }
                }
        }
        
        else  {
            $data['form'] = $this->_get_sport_form('new');
        }
                
        //start the list      
        $data['tulokset'] = $this->_sporttaulukko();
        $this->fuel->pages->render('misc/haku', $data);

    }
    
    
        private function _sporttaulukko(){
                                //start the list
        $this->load->model("Sport_model");
		
						
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'pid');
		$vars['headers'][2] = array('title' => 'Laji', 'key' => 'painotus');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
        $vars['headers'][4] = array('title' => 'Poista', 'key' => 'pid', 'key_link' => site_url($this->url.'laji/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'pid', 'key_link' => site_url($this->url.'laji/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
  
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->Sport_model->get_sport_list());
        
        return  $this->load->view('misc/taulukko', $vars, TRUE);
        
    }
	
    

	
    
    function laji ($tapa = null, $id = null){
        if(!$this->_is_jaos_admin()){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));
        }
        else if($tapa == null || $id == null){           
           $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Käsiteltävää lajia ei ole valittu"));
        }
        $msg = "";
        if($this->_is_editing_sport_allowed($id, $msg)){
            $this->load->model("Sport_model");

            if ($tapa == "poista"){
                if ($this->Sport_model->delete_sport($id) === false){
                    $this->lajit(array('msg_type' => 'danger', 'msg' => "Et voi poistaa lajia jolle on rekisteröity hevosia tai jaoksia"));
    
                }
                
                else {
                    $this->lajit(array('msg_type' => 'success', 'msg' => "Poisto onnistui."));
                }
            }
            
            else if ($tapa == "muokkaa"){
                 $data = array();
                $data['title'] = "Muokkaa lajia";
                $data['data_view'] = "<p>Huom! Muokkaukset koskevat kaikkia hevosia ja jaoksia, joille tämä laji on rekisteröity. Käytäthän muokkausta vain virheiden korjaamiseen (kirjoitusvirheet jne).</p>";
                $data['msg'] = "";
                if($this->input->server('REQUEST_METHOD') == 'POST'){
                    $tid = 0;
                    if ($this->_validate_sport_form('edit') == FALSE)
                        {
                            $data['msg'] = "Lajin muokkaus epäonnistui!";
                            $data['msg_type'] = "danger";
                        }
        
                    else
                        {
    
                            if($this->Sport_model->muokkaa_sport($data['msg'], $id, $this->input->post('painotus'), $this->input->post('lyhenne'))){
                                $data['msg'] = "Muokkaus onnistui!";
                                $data['msg_type'] = "success";

                            }else {
                                $data['msg_type'] = "danger";
                                
                            }
                        }
                }
                
                $data['tulokset'] = $this->_sporttaulukko();
                $data['form'] = $this->_get_sport_form('edit',  $id);
                $this->fuel->pages->render('misc/haku', $data);
    
            }
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
 
        }
     
    
    }
    
    
    
 
    
    private function _get_sport_form($mode, $id = 0, $color = array()){
        if ($mode == "edit"){           
            $color = $this->Sport_model->get_sport_info($id);
            $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'laji/muokkaa/'.$id));
           
        }
        else {

            $this->load->library('form_builder', array('submit_value' => "Lisää", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'lajit'));
        }     
            
        $fields['painotus'] = array('type' => 'text', 'label' => "Laji", 'required' => TRUE, 'value' => $color['painotus'] ?? "", 'class'=>'form-control');
        $fields['lyhenne'] = array('type' => 'text', 'label' => "Lyhenne", 'required' => TRUE, 'value' => $color['lyhenne'] ?? "", 'class'=>'form-control');
        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_sport_form()
    {
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('painotus', 'Lajinimi', "required|min_length[1]|max_length[240]");
        $this->form_validation->set_rules('lyhenne', 'Lyhenne', "required|min_length[1]|max_length[240]");
        return $this->form_validation->run();
    }
    
    private function _is_editing_sport_allowed($id, &$msg){
        
        $newsitem = $this->Sport_model->get_sport_info($id);
        if ($newsitem === null || sizeof($newsitem) == 0){
            $msg = "Lajia jota yrität muokata ei ole olemassa.";
            return false;
        }
        

       
       return true;    
        
    }
    
    //////////////////////////////////
    //Ominaisuudet
    
    
     function ominaisuudet($msg = array()){
        
        if(!$this->_is_jaos_admin()){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));

            return;
        }
        
         $data = $msg;
         $data['title'] = 'Muokkaa ominaisuuslistaa';
         $data['text_view'] = "<p>Olemassaolevan ominaisuuden muokkaaminen vaikuttaa kaikkiin jaoksiin ja kisoihin, joissa ko. ominaisuutta käytetään! Ominaisuutta ei voi poistaa, jos sille on rekisteröity yksikin jaos.</p>";
         

        //start the form
            
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            if ($this->_validate_trait_form('new') == FALSE)
                {
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['form'] = $this->_get_trait_form('new');

                }

            else
                {
                   $data['form'] = $this->_get_trait_form('new');

                    //add 
                   $tid = $this->Trait_model->lisaa_trait($data['msg'], $this->input->post('ominaisuus'));
                   
                   if ($tid !== false){
                   
                    $data['msg'] = "Julkaisu onnistui!";
                    $data['msg_type'] = "success";
                    }
                    else {
                        $data['msg_type'] = "danger";

                    }
                }
        }
        
        else  {
            $data['form'] = $this->_get_trait_form('new');
        }
                
        //start the list      
        $data['tulokset'] = $this->_traittaulukko();
        $this->fuel->pages->render('misc/haku', $data);
        

    }
    
    
        private function _traittaulukko(){
                                //start the list
        $this->load->model("Trait_model");

		$vars['headers'][1] = array('title' => 'id', 'key' => 'id');
        $vars['headers'][2] = array('title' => 'Ominaisuus', 'key' => 'ominaisuus');
        $vars['headers'][3] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($this->url.'ominaisuus/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
  
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->Trait_model->get_trait_list());
        
        return  $this->load->view('misc/taulukko', $vars, TRUE);
        
    }
	
    
    
	
    
    function ominaisuus ($tapa = null, $id = null){
        if(!$this->_is_jaos_admin()){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));
        }
        else if($tapa == null || $id == null){           
           $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Käsiteltävää ominaisuutta ei ole valittu"));
        }
        $msg = "";
        if($this->_is_editing_trait_allowed($id, $msg)){
            $this->load->model("Trait_model");

            if ($tapa == "poista"){
                if ($this->Trait_model->delete_trait($id) === false){
                    $this->ominaisuudet(array('msg_type' => 'danger', 'msg' => "Et voi poistaa ominaisuutta jota joku jaos käyttää."));
    
                }
                
                
                else {
                    $this->ominaisuudet(array('msg_type' => 'success', 'msg' => "Poisto onnistui."));
                }
            }
            
         
        else if ($tapa == "muokkaa"){
                 $data = array();
                $data['title'] = "Muokkaa ominaisuutta";
                $data['data_view'] = "<p>Huom! Muokkaukset koskevat kaikkia hevosia ja jaoksia, joille tämä ominaisuus on asetettu.
                Käytäthän muokkausta vain virheiden korjaamiseen (kirjoitusvirheet jne).</p>";
                $data['msg'] = "";
                if($this->input->server('REQUEST_METHOD') == 'POST'){
                    $tid = 0;
                    if ($this->_validate_trait_form('edit') == FALSE)
                        {
                            $data['msg'] = "Ominaisuuden muokkaus epäonnistui!";
                            $data['msg_type'] = "danger";
                        }
        
                    else
                        {
    
                            if($this->Trait_model->muokkaa_trait($data['msg'], $id, $this->input->post('ominaisuus'))){
                                $data['msg'] = "Muokkaus onnistui!";
                                $data['msg_type'] = "success";

                            }else {
                                $data['msg_type'] = "danger";
                                
                            }
                        }
                }
                
                $data['tulokset'] = $this->_sporttaulukko();
                $data['form'] = $this->_get_sport_form('edit',  $id);
                $this->fuel->pages->render('misc/haku', $data);
    
            }
    
            
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
 
        }
     
    
    }
    
    
    
 
    
    private function _get_trait_form($mode, $id = 0, $color = array()){
        if ($mode == "edit"){           
            $color = $this->Trait_model->get_trait_info($id);
            $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'ominaisuus/muokkaa/'.$id));
           
        }
        else {

            $this->load->library('form_builder', array('submit_value' => "Lisää", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'ominaisuudet'));
        }     
            
        $fields['ominaisuus'] = array('type' => 'text', 'label' => "Ominaisuus", 'required' => TRUE, 'value' => $color['ominaisuus'] ?? "", 'class'=>'form-control');
        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_trait_form()
    {
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('ominaisuus', 'Ominaisuuden nimi', "required|min_length[1]|max_length[240]");
 
        return $this->form_validation->run();
    }
    
    private function _is_editing_trait_allowed($id, &$msg){
        
        $newsitem = $this->Trait_model->get_trait_info($id);
        if ($newsitem === null || sizeof($newsitem) == 0){
            $msg = "Ominaisuutta jota yrität muokata ei ole olemassa.";
            return false;
        }
        

       
       return true;    
        
    }
    
    
    
    /////////////////////////////////////////////////////////////////7
    ///jaoksen hallinta
    
    public function jaokset($msg = null){
        $data['title'] = 'VRL:n alaiset jaokset';
        if (isset($msg)){
            $data['msg'] = $msg['msg'];
            $data['msg_type'] = $msg["msg_type"];
        }
        $data['text_view'] = "";
        $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'jaos/poista/', $this->url.'jaos/muokkaa/', $this->url.'tapahtumat/');
        $this->fuel->pages->render('misc/haku', $data);
    }
    
    public function lisaa_jaos($msg = ""){
        if($this->_is_jaos_admin()){   
        
            $data['msg'] = $msg;
            $data['title'] = 'Lisää jaos';
            $data['text_view'] = "";
   
           //start the form
               
           if($this->input->server('REQUEST_METHOD') == 'POST'){
               $tid = 0;
               $jaos = array();
               $jaos = $this->jaos->read_jaos_input($jaos);
   
               if ($this->jaos->validate_jaos_form('new', true) == FALSE)
                   {
                       $data['msg'] = "Tallennus epäonnistui!";
                       $data['msg_type'] = "danger";
                       $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_jaos", "new", true, $jaos);
                   }
               else if ($this->jaos->validate_jaos("new", true, $jaos, $data['msg']) == false){
                   $data['msg_type'] = "danger";
                   $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_jaos", "new", true, $jaos);
               }
   
               else
                   {
                      $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_jaos", "new", true);
                       //add 
                      $tid = $this->Jaos_model->add_jaos($data['msg'], $jaos);
                      $data['msg_type'] = "danger";
   
                      if ($tid !== false){
                      
                       $data['msg'] = "Jaoksen luonti onnistui onnistui!";
                       $data['msg_type'] = "success";
                       }
                   }
           }
           
           else  {
               $data['form'] = $this->jaos->get_jaos_form($this->url . "lisaa_jaos", "new", true);
           }
                   
           //start the list      
           $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'jaos/poista/', $this->url.'jaos/muokkaa/',$this->url.'tapahtumat/');
           $this->fuel->pages->render('misc/haku', $data);
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));
            return;
        }

    }
    

    
    public function jaos ($tapa, $id, $sivu = 'tiedot', $tapa2 = null, $sub_id = null){
        $data = array();
        $data['jaos'] = $this->Jaos_model->get_jaos($id);

            
        if(sizeof($data['jaos']) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Jaosta jota yritit muokata, ei ole olemassa."));
                return;
        }
        else if($tapa == "poista"){
                $this->_delete_jaos($id);
            
        }
        else if ($tapa == "muokkaa"){
            
            $data['sivu'] = $sivu;
            $data['url'] = $this->url . "jaos/muokkaa/".$id."/";
            $edit_url = $this->url . "jaos/muokkaa/".$id."/".$sivu;
            if(!$this->_is_editing_allowed($id, $data['msg'])){
                $data['msg_type'] = "danger";
                $this->jaokset(array("msg" => $data['msg'], "msg_type"=>$data['msg_type']));
            }
            
            else if($sivu == "tiedot"){
                $this->_handle_tiedot_edit($id, $data, $edit_url);
            }else if($sivu == "online"){
                $this->_handle_toiminnassa_edit($id, $data, $edit_url);
            }else if($sivu == "saannot" && $data['jaos']['nayttelyt'] == 0){
                $this->_handle_saannot_edit($id, $data, $edit_url);
            }else if($sivu == "luokat" && $data['jaos']['nayttelyt'] == 0){
                $this->_handle_luokat_edit($id, $data, $edit_url, $tapa2, $sub_id);
            }else if($sivu == "palkinnot" && $data['jaos']['nayttelyt'] == 1){
                $this->_handle_palkinnot_edit($id, $data, $edit_url, $tapa2, $sub_id);
            }else if($sivu == "ominaisuudet" && $data['jaos']['nayttelyt'] == 0){
                $this->_handle_ominaisuudet_edit($id, $data, $edit_url);
            }else if($sivu == "omistajat"){
                $this->_handle_jaos_owners($id, $tapa2, $sub_id, $data, $data['url']);
            }

           //
        }
            
        else {
            $this->jaokset();
        }
       
        
    }
    
    private function _handle_tiedot_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_jaos_input($jaos);
            if ($this->jaos->validate_jaos_form('edit', $this->_is_jaos_admin()) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
            }
            else if ($this->jaos->validate_jaos("edit", $this->_is_jaos_admin(), $jaos, $data['msg'], false, $id) == false){
                $data['msg_type'] = "danger";
            }
            else  {
                   $tid = $this->Jaos_model->edit_jaos($id, $jaos);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Jaoksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                        $data['jaos'] = $jaos;
                    }
            }
            
        }
        
        $data['form'] = $this->jaos->get_jaos_form($edit_url, "edit", $this->_is_jaos_admin(), $jaos);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    
    private function _handle_saannot_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_rules_input($jaos);
            if ($this->jaos->validate_rules_form('edit', true) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
            }
            else if ($this->jaos->check_rules_form($jaos, $data['msg']) == false){
                $data['msg_type'] = "danger";
            }
            else  {
                   $tid = $this->Jaos_model->edit_jaos($id, $jaos);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Jaoksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                    }
            }
                    
        } 
        $data['form'] = $this->jaos->get_rules_form($edit_url, $jaos);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    private function _handle_toiminnassa_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_toiminnassa_input($jaos);
            if ($this->jaos->validate_toiminnassa_form($id, $jaos, $data['msg']) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui! " . $data['msg'];
                    $data['msg_type'] = "danger";
            }
            else  {
                $edit_data = array("toiminnassa"=>$jaos['toiminnassa'], "s_salli_porrastetut"=>$jaos['s_salli_porrastetut']);
                   $tid = $this->Jaos_model->edit_jaos($id, $edit_data);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Jaoksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                   }
            }
                    
        } 
        $data['form'] = $this->jaos->get_toiminnassa_form($edit_url, $jaos);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    private function _handle_luokat_edit($id, $data, $edit_url, $tapa, $class_id){


        $jaos = $data['jaos'];
        $class = array();
        if($tapa == "lisaa"){            
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->jaos->read_class_input($class);
                $data['form'] = $this->jaos->get_class_form($edit_url."/".$tapa, $class);

                if ($this->jaos->validate_class_form() == FALSE){
                        $data['msg'] = "Tallennus epäonnistui!";
                        $data['msg_type'] = "danger";
                }
                else if ($this->jaos->validate_class("add", $this->_is_jaos_admin(), $class, $data['msg']) == false){
                    $data['msg_type'] = "danger";

                }
                else  {
                       $tid = $this->Jaos_model->add_class($id, $jaos['laji'], $class);
                       $data['msg_type'] = "danger";
    
                       if ($tid !== false){                 
                            $data['msg'] = "Luokan tallennus onnistui!";
                            $data['msg_type'] = "success";
                            $data['jaos'] = $jaos;
                            $data['form'] = null;

                        }
                }
            }else {           
                $data['form'] = $this->jaos->get_class_form($edit_url."/".$tapa, $class);
            }
            
        }
        
        else if($tapa == "muokkaa"){
            $class = $this->Jaos_model->get_class($class_id, $id);
            
            if(sizeof($class) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Luokkaa jota yritit muokata, ei ole olemassa."));
                return;
            }
            
            else if($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->jaos->read_class_input($class);
                $data['form'] = $this->jaos->get_class_form($edit_url."/".$tapa."/".$class_id, $class);
                if ($this->jaos->validate_class_form() == FALSE){
                        $data['msg'] = "Tallennus epäonnistui!";
                        $data['msg_type'] = "danger";
                }
                else if ($this->jaos->validate_class("edit", $this->_is_jaos_admin(), $class, $data['msg']) == false){
                    $data['msg_type'] = "danger";
                }
                else  {
                       $tid = $this->Jaos_model->edit_class($class_id, $class);
                       $data['msg_type'] = "danger";
    
                       if ($tid !== false){                 
                            $data['msg'] = "Luokan tallennus onnistui!";
                            $data['msg_type'] = "success";
                            $data['jaos'] = $jaos;
                            $data['form'] = null;
                        }
                }
            }else {
                $data['form'] = $this->jaos->get_class_form($edit_url."/".$tapa."/".$class_id, $class);
            }
            
        }else if($tapa == "poista"){
            $class = $this->Jaos_model->get_class($class_id, $id);
            
            if(sizeof($class) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Luokkaa jota yritit muokata, ei ole olemassa."));
                return;
            }else if($class['kaytossa'] == 1){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Et voi poistaa käytössä olevaa luokkaa."));
                return;
                
            }else {
                $this->Jaos_model->delete_class($class_id, $id);
                $data['msg'] = "Luokan poisto onnistui!";
                $data['msg_type'] = "success";
            }
        }
        
        $data['list'] = $this->jaos->luokkataulukko($id, $edit_url."/poista/", $edit_url."/muokkaa/");
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    
     private function _handle_palkinnot_edit($id, $data, $edit_url, $tapa, $reward_id){


        $jaos = $data['jaos'];
        $reward = array();
        if($tapa == "lisaa"){            
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->jaos->read_reward_input($reward);
                $data['form'] = $this->jaos->get_reward_form($edit_url."/".$tapa, $reward);

                if ($this->jaos->validate_reward_form() == FALSE){
                        $data['msg'] = "Tallennus epäonnistui!";
                        $data['msg_type'] = "danger";
                }
                else if ($this->jaos->validate_reward("add", $this->_is_jaos_admin(), $reward, $data['msg']) == false){
                    $data['msg_type'] = "danger";

                }
                else  {
                       $tid = $this->Jaos_model->add_reward($id, $reward);
                       $data['msg_type'] = "danger";
    
                       if ($tid !== false){                 
                            $data['msg'] = "Palkinnon tallennus onnistui!";
                            $data['msg_type'] = "success";
                            $data['jaos'] = $jaos;
                            $data['form'] = null;

                        }
                }
            }else {           
                $data['form'] = $this->jaos->get_reward_form($edit_url."/".$tapa, $reward);
            }
            
        }
        
        else if($tapa == "muokkaa"){
            $reward = $this->Jaos_model->get_reward($reward_id, $id);
            
            if(sizeof($reward) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Palkintoa jota yritit muokata, ei ole olemassa."));
                return;
            }
            
            else if($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->jaos->read_reward_input($reward);
                $data['form'] = $this->jaos->get_reward_form($edit_url."/".$tapa."/".$reward_id, $reward);
                if ($this->jaos->validate_reward_form() == FALSE){
                        $data['msg'] = "Tallennus epäonnistui!";
                        $data['msg_type'] = "danger";
                }
                else if ($this->jaos->validate_reward("edit", $this->_is_jaos_admin(), $reward, $data['msg']) == false){
                    $data['msg_type'] = "danger";
                }
                else  {
                       $tid = $this->Jaos_model->edit_reward($reward_id, $reward);
                       $data['msg_type'] = "danger";
    
                       if ($tid !== false){                 
                            $data['msg'] = "Palkinnon tallennus onnistui!";
                            $data['msg_type'] = "success";
                            $data['jaos'] = $jaos;
                            $data['form'] = null;
                        }
                }
            }else {
                $data['form'] = $this->jaos->get_reward_form($edit_url."/".$tapa."/".$reward_id, $reward);
            }
            
        }else if($tapa == "poista"){
            $reward = $this->Jaos_model->get_reward($reward_id, $id);
            
            if(sizeof($reward) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Palkintoa jota yritit muokata, ei ole olemassa."));
                return;
            }else if($reward['kaytossa'] == 1){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Et voi poistaa käytössä olevaa Palkintoa."));
                return;
                
            }else {
                $this->Jaos_model->delete_reward($reward_id, $id);
                $data['msg'] = "Palkinnon poisto onnistui!";
                $data['msg_type'] = "success";
            }
        }
        
        $data['list'] = $this->jaos->palkintotaulukko($id, $edit_url."/poista/", $edit_url."/muokkaa/");
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    private function _handle_ominaisuudet_edit($id, $data, $edit_url){
        $jaos = $data['jaos'];
        $traits = $this->Trait_model->get_trait_array_by_jaos($id);

        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->jaos->read_trait_input($traits);

            if ($this->jaos->validate_trait_form($id, $traits, $jaos, $data['msg']) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui! " . $data['msg'];
                    $data['msg_type'] = "danger";
            }
            else  {
                   $tid = $this->Trait_model->update_jaos_traits($id, $traits, $data['msg']);
                   $data['msg_type'] = "danger";

                   if ($tid !== false){                 
                        $data['msg'] = "Jaoksen muokkaus onnistui!";
                        $data['msg_type'] = "success";
                   }
            }
                    
        }
        $data['form'] = $this->jaos->get_trait_form($edit_url, $traits);
        $this->fuel->pages->render('jaokset/muokkaa', $data);
        
    }
    
    private function _delete_jaos($id){
                if(!$this->_is_jaos_admin()){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus poistaa jaos."));
                return;
            }else {
                $msg = "";
                if($this->jaos->delete_jaos($id, $msg)){
                
                    $data['msg'] = "Jaoksen poisto onnistui!";
                    $data['msg_type'] = "success";
                }
                else {
                    $data['msg'] = "Jaoksen poisto epäonnistui! " . $msg;
                    $data['msg_type'] = "danger";
                }
                
                $this->jaokset(array("msg" => $data['msg'], "msg_type"=>$data['msg_type']));
            }
    }

    
    
    private function _handle_jaos_owners($id, $tapa, $owner, &$data, $edit_url){
        $this->load->library('ownership');
        $mode = "";
            if($this->_is_jaos_admin()){
               $mode = "admin";
        }
 
        if($this->_is_jaos_admin() || $this->ownership->is_jaos_main_owner($this->ion_auth->user()->row()->tunnus, $id)){				
            $this->ownership->handle_jaos_ownerships($mode, $tapa, $this->ion_auth->user()->row()->tunnus, $owner, $id, $data);				
            $data['form'] = $this->ownership->get_owner_adding_form($edit_url, "Ylläpitäjä", "Kalenterityöntekijä");
            $data['list'] = $this->ownership->jaos_ownerships($id, true, $edit_url.'/');
        } else {
            $data['list'] = $this->ownership->jaos_ownerships($id, false, $edit_url.'/');
        }
        
        $this->fuel->pages->render('jaokset/muokkaa', $data);
    }
    
    /////////////////////////////////////
    // Jaoksen tapahtumat
    
    public function tapahtumat($jaos_id = null, $tapa=null, $id=null, $tapa2 = null, $os_id = null){
        $url_begin = $this->url."tapahtumat/";
        if($jaos_id == null){
            $this->events->select_event_organizer($jaos_id, $url_begin);

            
        }else {
                     
            $data = array();
            $data['jaos'] = $this->Jaos_model->get_jaos($jaos_id);
    
            $edit_url = $url_begin . $jaos_id;
            if(sizeof($data['jaos']) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Jaosta jonka tapahtumia yritit hakea, ei ole olemassa."));
                return;
            }
            else if(!$this->_is_editing_allowed($jaos_id, $msg)){
               $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtuman käsittely epäonnistui. " . $msg));
               return;

            }
            else if($tapa == "poista"){
                $this->events->delete_event($id, $data);
                $this->events->print_event_list($data, $edit_url);

            }
            
            else if($tapa == "lisaa"){
                $this->events->add_event($id, $data, $edit_url);
                $this->events->print_event_list($data, $edit_url, false, $data['event_data']);
                
            }    
            else if($tapa == "muokkaa"){
                $this->events->handle_event(false, $id, $data['jaos'], $tapa2, $os_id, $edit_url);
                
            }else {
                $this->events->print_event_list($data, $edit_url);
            }
        }
        
    }
    
    
    private function _edit_event($id, $jaos, $tapa = null, $os_id = null, $edit_url){
        $this->events->handle_event(false, true, $id, $jaos, $tapa, $os_id, $edit_url);
    }
        
    
    private function _is_editing_allowed($id, &$msg){
        
        $data['jaos'] = $this->Jaos_model->get_jaos($id);

        if(sizeof($data['jaos']) == 0){
            $msg = "Jaosta jota yritit muokata, ei ole olemassa.";
            return false;
        }
        //only logged in can edit
		if(!($this->ion_auth->logged_in())){
            $msg = "Kirjaudu sisään muokataksesi!";
			return false;
		}//jos et ole admin, pitää olla ko. jaoksen omistaja
        if(!$this->_is_jaos_admin() && !$this->Jaos_model->is_jaos_owner($this->ion_auth->user()->row()->tunnus, $id, 1)){
             $msg = "Vain admin, jaosvastaava ja jaoksen ylläpitäjä voi muokata jaosta.";
             return false;
        }
        
        return true;
    }
    
    
    
    
    
  
    

    
}
    

?>