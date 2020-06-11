<?php
class Yllapito_hevosrekisteri extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'hevosrekisteri');
    private $url;
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
        $this->load->model('Color_model');
        $this->url = "yllapito/hevosrekisteri/";
        

    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    function index(){
$this->pipari();
    }
   
    
    function varit($msg = array()){
        
         $data = $msg;
         $data['title'] = 'Muokkaa värilistaa';
         $data['text_view'] = "<p>Olemassaolevan värin muokkaaminen vaikuttaa kaikkiin hevosiin, joille ko. väri on rekisteröity! Väriä ei voi poistaa, jos sille on rekisteröity yksikin hevonen.</p>";
         

        //start the form
            
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            if ($this->_validate_vari_form('new') == FALSE)
                {
                    $data['msg'] = "Julkaisu epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['form'] = $this->_get_vari_form('new');

                }

            else
                {
                   $data['form'] = $this->_get_vari_form('new');

                    //add 
                   $tid = $this->Color_model->lisaa_vari($data['msg'], $this->input->post('vari'), $this->input->post('lyhenne'), $this->input->post('pvari'), $this->input->post('geenit'));
                   
                   if ($tid !== false){
                   
                    $data['msg'] = "Julkaisu onnistui! Katso uusi väri <a href=\"".site_url('virtuaalihevoset/vari/'.$tid) ."\">täältä</a>.";
                    $data['msg_type'] = "success";
                    }
                    else {
                        $data['msg_type'] = "danger";

                    }
                }
        }
        
        else  {
            $data['form'] = $this->_get_vari_form('new');
        }
                
        //start the list      
        $data['tulokset'] = $this->_varitaulukko();
        $this->fuel->pages->render('misc/haku', $data);

    }
    
    
        private function _varitaulukko(){
                                //start the list
        $this->load->model("color_model");
		
						
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'vid', 'key_link' => site_url('virtuaalihevoset/vari/'));
		$vars['headers'][2] = array('title' => 'Väri', 'key' => 'vari');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
        $vars['headers'][4] = array('title' => 'Poista', 'key' => 'vid', 'key_link' => site_url($this->url.'vari/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'vid', 'key_link' => site_url($this->url.'vari/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
  
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->color_model->get_colour_list());
        
        return  $this->load->view('misc/taulukko', $vars, TRUE);
        
    }
	
    
    
	
    
    function vari ($tapa = null, $id = null){
        if($tapa == null || $id == null){           
           $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Käsiteltävää väriä ei ole valittu"));
        }
        $msg = "";
        if($this->_is_editing_allowed($id, $msg)){
            $this->load->model("Color_model");

            if ($tapa == "poista"){
                if ($this->Color_model->delete_vari($id) === false){
                    $this->varit(array('msg_type' => 'danger', 'msg' => "Et voi poistaa väriä jolle on rekisteröity hevosia"));
    
                }
                
                else {
                    $this->varit(array('msg_type' => 'success', 'msg' => "Poisto onnistui."));
                }
            }
            
            else if ($tapa == "muokkaa"){
                 $data = array();
                $data['title'] = "Muokkaa väriä";
                $data['data_view'] = "<p>Huom! Muokkaukset koskevat kaikkia hevosia, joille tämä väri on rekisteröity. Käytäthän muokkausta vain virheiden korjaamiseen (kirjoitusvirheet jne) tai virheellisten geenien korjaamiseen.</p>";
                $data['msg'] = "";
                if($this->input->server('REQUEST_METHOD') == 'POST'){
                    $tid = 0;
                    if ($this->_validate_vari_form('edit') == FALSE)
                        {
                            $data['msg'] = "Värin muokkaus epäonnistui!";
                            $data['msg_type'] = "danger";
                        }
        
                    else
                        {
    
                            if($this->Color_model->muokkaa_vari($data['msg'], $id, $this->input->post('vari'), $this->input->post('lyhenne'),
                                                                $this->input->post('pvari'), $this->input->post('geenit'))){
                                $data['msg'] = "Muokkaus onnistui! Katso väri <a href=\"". site_url('virtuaalihevoset/vari/'.$id) ."\">täältä</a>.";
                                $data['msg_type'] = "success";
                                $this->varit($data);
                            }else {
                                $data['msg_type'] = "danger";                            
                            }
                        }
                }
                
                
                $data['form'] = $this->_get_vari_form('edit',  $id);
                $this->fuel->pages->render('misc/haku', $data);
    
            }
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
 
        }
     
    
    }
    
    
    
 
    
    private function _get_vari_form($mode, $id = 0, $color = array()){
        if ($mode == "edit"){           
            $color = $this->Color_model->get_colour_info($id);
            $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'vari/muokkaa/'.$id));
           
        }
        else {

            $this->load->library('form_builder', array('submit_value' => "Lisää", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'varit'));
        }     
            
        $fields['vari'] = array('type' => 'text', 'label' => "Värinimi", 'required' => TRUE, 'value' => $color['vari'] ?? "", 'class'=>'form-control');
        $fields['lyhenne'] = array('type' => 'text', 'label' => "Lyhenne", 'required' => TRUE, 'value' => $color['lyhenne'] ?? "", 'class'=>'form-control');
        $fields['pvari'] = array('type' => 'select', 'label'=> 'Pohjaväri', 'options' => $this->Color_model->get_base_list(), 'required' => FALSE, 'value'=> $color['pvari'] ?? "emtpohja", 'class'=>'form-control');

        $geenit = array_merge($this->Color_model->get_special_list(), $this->Color_model->get_kirj_list());
        $geenit['gen_savy'] = "Sävy";
        $values = array();
        foreach($geenit as $geeni=>$nimi){
            if (isset($color[$geeni]) && $color[$geeni] == 1){
                $values[] = $geeni;
            }
        }
        $fields['geenit'] = array('type' => 'multi', 'label'=>"Geenit", 'mode' => 'checkbox', 'required' => FALSE, 'options' => $geenit, 'value'=>$values, 'class'=>'form-control', 'wrapper_tag' => 'li');

        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_vari_form()
    {
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('vari', 'Otsikko', "required|min_length[1]|max_length[240]");
        $this->form_validation->set_rules('lyhenne', 'Lyhenne', "required|min_length[1]|max_length[240]");
        return $this->form_validation->run();
    }
    
    
  
    
    private function _is_editing_allowed($vid, &$msg){
        
        $newsitem = $this->Color_model->get_colour_info($vid);
        if ($newsitem === null || sizeof($newsitem) == 0){
            $msg = "Väriä jota yrität muokata ei ole olemassa.";
            return false;
        }
        

       
       return true;
        
        
        
    }
    
}
    

?>