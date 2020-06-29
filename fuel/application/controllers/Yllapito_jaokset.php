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
    /////////////////////////////////////////////////////////////////7
    ///jaoksen hallinta
    
    public function jaokset($msg = null){
        $data['title'] = 'VRL:n alaiset jaokset';
        if (isset($msg)){
            $data['msg'] = $msg['msg'];
            $data['msg_type'] = $msg["msg_type"];
        }
        $data['text_view'] = "";
        $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'jaos/poista/', $this->url.'jaos/muokkaa');
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
        $data['tulokset'] = $this->jaos->jaostaulukko($this->url.'jaos/poista/', $this->url.'jaos/muokkaa');
        $this->fuel->pages->render('misc/haku', $data);

    }
    
    public function jaos ($tapa, $id){
        $data = array();
        if($tapa == "poista"){
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
        } else if ($tapa == "muokkaa"){
            
        } else {
            $this->jaokset();
        }
        
        
    }
    
    
    
    
    
  
    

    
}
    

?>