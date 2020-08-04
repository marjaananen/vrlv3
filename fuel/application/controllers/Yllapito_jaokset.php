<?php
class Yllapito_jaokset extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'jaosvastaava', 'jaosyllapito');
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
        
    }
    
    private function _is_jaos_admin(){
        return $this->user_rights->is_allowed(array('admin', 'jaosvastaava'));
    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    function index(){
$this->pipari();
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
            
         
                
                $data['tulokset'] = $this->_traittaulukko();
                $data['form'] = $this->_get_trait_form('edit',  $id);
                $this->fuel->pages->render('misc/haku', $data);
    
            
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
        if(!$this->_is_jaos_admin()){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain VRL:n ylläpidolla ja jaosvastaavalla on oikeus muokata näitä tietoja."));
            return;
        }
        
         $data['msg'] = $msg;
         $data['title'] = 'Lisää jaos';
         $data['text_view'] = "";

        //start the form
            
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            $jaos = $this->jaos->read_jaos_input();

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
        $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'jaos/poista/', $this->url.'jaos/muokkaa/');
        $this->fuel->pages->render('misc/haku', $data);

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
            }else if($sivu == "saannot"){
                $this->_handle_saannot_edit($id, $data, $edit_url);
            }else if($sivu == "luokat"){
                $this->_handle_luokat_edit($id, $data, $edit_url, $tapa2, $sub_id);
            }else if($sivu == "ominaisuudet"){
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
            if ($this->jaos->validate_jaos_form('edit', true) == FALSE){
                    $data['msg'] = "Tallennus epäonnistui!";
                    $data['msg_type'] = "danger";
            }
            else if ($this->jaos->validate_jaos("edit", $this->_is_jaos_admin(), $jaos, $data['msg'], $id) == false){
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
                   $tid = $this->Jaos_model->edit_jaos($id, $jaos);
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
            //jos jaosta ei ole annettu, valitaan se
            if($this->input->post('jaos')){
                $jaos_id = $this->input->post('jaos');
                redirect($url_begin.$jaos_id, 'refresh');
            }else {       
                $this->load->library('form_builder', array('submit_value' => 'Hae'));
                $jaos_options = $this->Jaos_model->get_jaos_option_list();
        
                $fields = array();
                $fields['jaos'] = array('type' => 'select', 'options' => $jaos_options, 'value' => 0, 'class'=>'form-control');
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url_begin));
                $data['form'] =  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
                
                $data['title'] = "Jaosten tapahtumat";
                $data['text_view'] = $this->load->view('jaokset/text_tapahtumat', null, TRUE);
                $data['list'] = "";
                $this->fuel->pages->render('jaokset/tapahtumat', $data);
            }
            
        }else {
                     
            $data = array();
            $data['jaos'] = $this->Jaos_model->get_jaos($jaos_id);
            $msg = "";
    
            $edit_url = $url_begin . $jaos_id;
                
            if(sizeof($data['jaos']) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Jaosta jonka tapahtumia yritit hakea, ei ole olemassa."));
                return;
            }
            else if(!$this->_is_editing_allowed($jaos_id, $msg)){
               $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtuman käsittely epäonnistui. " . $msg));

            }
            else if($tapa == "poista"){
                $this->_delete_event($id, $jaos_id);
                
            }
            
            else if($tapa == "lisaa"){
                $this->_add_event($id, $data['jaos'], $tapa, $os_id, $edit_url);
                
            }    
            else if($tapa == "muokkaa"){
                $this->_edit_event($id, $data['jaos'], $tapa2, $os_id, $edit_url);
                
            }else {
                $data['form'] = $this->jaos->get_event_form($edit_url."/lisaa");
                $data['title'] = "Jaoksen ". $data['jaos']['lyhenne'] . " tapahtumat";
                $data['text_view'] = $this->load->view('jaokset/text_tapahtumat', null, TRUE);
                $data['list'] = $this->jaos->tapahtumataulukko($jaos_id, $edit_url."/poista/", $edit_url."/muokkaa/");
                $this->fuel->pages->render('jaokset/tapahtumat', $data);
            }
        }
        
    }
    
    private function _add_event($id, $jaos, $tapa, $os_id, $edit_url){
        $event_data = array();
        $this->load->library("vrl_helper");
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $event_data['pv'] = $this->input->post('pv');
            $event_data['otsikko'] = $this->input->post('otsikko');
            $event_data['osallistujat'] = $this->input->post('osallistujat');

            if($this->input->post('otsikko') && $this->input->post('pv')
               && strlen($this->input->post('otsikko')) > 5
               && $this->vrl_helper->validateDate($this->input->post('pv'))){
                $osallistujat = array();
                $osallistujat_ok = $this->_parse_event_horses($this->input->post('osallistujat'), $data, $osallistujat);
                if($osallistujat_ok){
                    $tid = $this->jaos->add_event($this->ion_auth->user()->row()->tunnus, $jaos['id'], $this->input->post('pv'), $this->input->post('otsikko'), $osallistujat['ok']);
                    IF($tid == false){
                        $data['msg'] = "Virhe lisäyksessä! Sisältö ei mennyt tietokantaan asti. Ole yhteydessä ylläpitoon!";
                        $data['msg_type'] = 'danger';
                    }else {
                    
                        redirect($edit_url."/muokkaa/".$tid, 'refresh');
                        return;
                    }
                }
                
                //parse_event_horses funkkari lisää virheilmot $data funkkariin, ei tarvitse käsitellä erikseen
            }
            else {
                $data['msg'] = "Virhe lisäyksessä! Otsikko tai päivämäärä puuttuu tai on liian lyhyt/virheellinen.";
                $data['msg_type'] = 'danger';
            }
                
                                
        }else {
            $event_data = array();
        }
        $data['form'] = $this->jaos->get_event_form($edit_url."/".$tapa, $event_data);
        $data['title'] = "Jaoksen ". $data['jaos']['lyhenne'] . " tapahtumat";
        $data['text_view'] = $this->load->view('jaokset/text_tapahtumat', null, TRUE);
        $data['list'] = $this->jaos->tapahtumataulukko($jaos_id, $edit_url."/poista/", $edit_url."/muokkaa/");
        $this->fuel->pages->render('jaokset/tapahtumat', $data);
    }
    
    private function _delete_event($id, $jaos_id){
        $tapahtuma = $this->Jaos_model->get_event($id, $jaos_id);
        if (sizeof($tapahtuma)>0){
            $osallistujat = $this->Jaos_model->get_event_horses($id);
            if(sizeof($osallistujat)>0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtumassa on palkittuja hevosia. Poista ensin palkitut hevoset listalta."));
               
            }else {
                $this->Jaos_model->delete_event($id, $jaos_id);
                $this->tapahtumat($jaos_id);
            }
            
        }else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtumaa jota yrität poistaa ei ole olemassa."));

        }        
        
    }
    
    private function _edit_event($id, $jaos, $tapa = null, $os_id = null, $edit_url){
        $this->load->library('vrl_helper');
    
        $tapahtuma = $this->Jaos_model->get_event($id, $jaos['id']);
        if (sizeof($tapahtuma)>0){
            //poistetaan palkittu 
            if (isset($tapa) && $tapa == "poista"){
                $this->Jaos_model->delete_event_horse($os_id, $id);
                $this->tapahtumat($jaos['id'], "muokkaa", $id);
            //muokataan tapahtumaa
            } else {
                 if($this->input->server('REQUEST_METHOD') == 'POST'){
                    //tapahtuman tiedot
                    if($this->input->post('otsikko') && $this->input->post('pv')
                       && strlen($this->input->post('otsikko')) > 5
                       && $this->vrl_helper->validateDate($this->input->post('pv'))){
                        
                            $this->jaos->edit_event($id, $jaos['id'], $this->input->post('otsikko'),$this->input->post('pv'));
                            
                            //lisätäänkö osallistujia?
                            if($this->input->post('osallistujat')){
                                $osallistujat = array();
                                $osallistujat_ok = $this->_parse_event_horses($this->input->post('osallistujat'), $data, $osallistujat);
                                if($osallistujat_ok){
                                    $this->jaos->add_participants($id, $osallistujat);
                                }
                            }
                        }
                        
                    else {
                        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtuman tiedot virheelliset."));

                    }
                }
                $data['jaos'] = $jaos;
                $data['palkitut'] = $this->jaos->tapahtumaosallistujat($id, $edit_url."/muokkaa/".$id."/poista/");
                $data['tapahtuma'] = $this->Jaos_model->get_event($id);
                $data['form'] = $this->jaos->get_event_form($edit_url."/muokkaa/".$id, $data['tapahtuma'], true);

                $this->fuel->pages->render('jaokset/tapahtuma', $data);
                
            }
            

            
        }else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Tapahtumaa jota yrität muokata ei ole olemassa."));

        }        
        
    }
    
    private function _parse_event_horses($osallistujalista_input, &$data, &$luetut_rivit){
        $this->load->library("Vrl_helper");
                $this->load->model("Hevonen_model");

        $osallistujalista = trim($osallistujalista_input);
		$osallistujat = explode("\n", $osallistujalista);
		$osallistujat = array_filter($osallistujat, 'trim');
        $kasitellyt_kopukat = array();
        $luetut_rivit = array();
		$virhe = array();		
		//Jokainen rivi/luokka käydään läpi
		foreach ($osallistujat as $rivi_input){
            $luettu = array();
            $rivi = explode(";", $rivi_input);
            if(sizeof($rivi) != 4){
                $virhe[] = "Virheellinen rivi, tarkistathan että riviltä löytyy kolme ; merkkiä! " . $rivi_input;

            }
            else {
                if(isset($rivi[0]) && $this->vrl_helper->check_vh_syntax($rivi[0]) && $this->Hevonen_model->onko_tunnus($this->vrl_helper->vh_to_number($rivi[0]))){
                    $luettu['vh'] = $this->vrl_helper->vh_to_number(trim($rivi[0]));
                    if(isset($kasitellyt_kopukat[$rivi[0]])){
                        $virhe[] = "Sama VH-tunnus toista kertaa rivillä: " . $rivi_input;

                    }
                    
                    else if(isset($rivi[2]) && strlen($rivi[2]) > 1){
                        $luettu['palkinto'] = $rivi[2];
                                        
                    
                        if(isset($rivi[1])){
                            $luettu['tulos'] = $rivi[1];
                        }
                        
                        if(isset($rivi[3])){
                            $luettu['kommentti'] = htmlspecialchars($rivi[3]);
                        }
                        
                        $luetut_rivit[] = $luettu;
                        $kasitellyt_kopukat[$rivi[0]] = "ok";
                        }
                    
                    else {
                        $virhe[] = "Virheellinen palkinto rivillä: " . $rivi_input;
                    }
  
                }else {
                    $virhe[] = "Virheellinen VH-tunnus rivillä: " . $rivi_input;
                }
            }
            
        }
        
        if(sizeof($virhe)>0){
            $data['msg'] = "Virhe lisäyksessä!";
            $data['msg_details'] = $virhe;
            $data['msg_type'] = 'danger';
            return false;
        }
        else {
            return true;
        }
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
        if(!$this->_is_jaos_admin() && !$this->jaos_model->is_jaos_owner($this->ion_auth->user()->row()->tunnus, $id)){
             $msg = "Vain admin, jaosvastaava ja jaoksen ylläpitäjä voi muokata jaosta.";
             return false;
        }
        
        return true;
    }
    
    
    
    
    
  
    

    
}
    

?>