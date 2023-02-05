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
        $this->load->model('Hevonen_model');
        $this->load->model('Color_model');
        $this->load->model('Breed_model');
            $this->load->library("vrl_helper");

        $this->url = "yllapito/hevosrekisteri/";
        

    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    function index()
    {
		$vars['title'] = "Hevosrekisteri";
		
		$vars['msg'] = '';
		
		$vars['text_view'] = "<p>Viimeisimmät rekisteröidyt hevoset max. kuukauden ajalta.</p>";		
	
            $vars['headers'][1] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type'=>'date');
			$vars['headers'][2] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][4] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][5] = array('title' => 'Skp', 'key' => 'sukupuoli');
			$vars['headers'][6] = array('title' => 'Rekisteröi', 'key' => 'hyvaksyi', 'prepend_text'=>'VRL-', 'key_link' => site_url('/tunnus/'));
            $vars['headers'][4] = array('title' => 'Poista', 'key' => 'reknro', 'key_link' => site_url($this->url.'poista/'), 'image' => site_url('assets/images/icons/delete.png'));
            $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'reknro', 'key_link' => site_url($this->url.'muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
  
			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->Hevonen_model->get_horses_newest());
		
		
		$this->fuel->pages->render('misc/taulukko', $vars);
    
    }
    
    function polveutumistarkastus($reknro = null){
        $allowed = array("admin", "alayhdistys-yp", "alayhdistys-w");
        $data = array();
        if(!$this->user_rights->is_allowed($allowed)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Vain rotuyhdistysten ylläpitäjillä ja työntekijöillä on oikeus käsitellä polveutumistietoja."));

        }else {
            if(isset($reknro)){
                $horse = $this->Hevonen_model->get_hevonen($this->vrl_helper->vh_to_number($reknro));
                if(isset($horse) && isset($horse['rotunro'])){
                    if($this->Breed_model->is_breed_handling_allowed($horse['rotunro'], $this->ion_auth->user()->row()->tunnus)){
                        IF($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('reknro') == null){
                            $edit_data = array();
                            if($horse['polv_tark'] == 1 && $this->input->post('polv_tark') == 0){
                                $edit_data['polv_tark'] = 0;
                            }else if($this->input->post('polv_tark') == 1
                                     && $this->input->post('polv_pros') != null
                                     && strlen($this->input->post('polv_pros')) > 0
                                     && strlen(trim($this->input->post('polv_pros'))) < 12
                                   && is_numeric($this->input->post('polv_pros'))
                                   && $this->input->post('polv_pros') > 0
                                   && $this->input->post('polv_pros') <= 100){
                                $edit_data['polv_tark'] = 1;
                                $edit_data['polv_tark_vrl'] = $this->ion_auth->user()->row()->tunnus;
                                $edit_data['polv_tark_date'] = date("Y-m-d");
                                $edit_data['polv_pros'] = $this->input->post('polv_pros');
                                
                            }else {
                                $data = array('msg_type' => 'danger', 'msg' => "Virheellinen syöte!");

                            }
                            
                            if(sizeof($edit_data)> 0){
                                $data = array('msg_type' => 'success', 'msg' => "Muokkaus onnistui!");
                                $this->db->where('reknro', $this->vrl_helper->vh_to_number($reknro));
                                $this->db->update('vrlv3_hevosrekisteri', $edit_data);
                                
                                $horse = $this->Hevonen_model->get_hevonen($this->vrl_helper->vh_to_number($reknro));

                            }
                            

                        }
                        
                        
                        
                        $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
                        $fields['polv_tark'] = array('label'=>'Polveutuminen hyväksytty', 'type' => 'checkbox', 'checked' => $horse['polv_tark'] ?? false, 'class'=>'form-control');
                        
                        if(isset($horse['polv_tark']) && $horse['polv_tark'] == 1){
                            $fields['section_example'] = array('type' => 'section', 'tag' => 'h3',
                                                               'value' => 'Polveutuminen tarkastettu ' . $this->vrl_helper->sql_date_to_normal($horse['polv_tark_date']) .'.
                                                               <br />Prosentti: '.floatval($horse['polv_pros']) . '%, Tarkastaja: VRL-'.$horse['polv_tark_vrl'].'.',
                                                               'after_html'=>'<span class="form_comment">Jos haluat jostain syystä muokata polveutumishyväksyntää,
                                                               nollaa polveutumistiedot poistamalla yo. ruksi ja lähettämällä lomake. Hyväksyttyä polveutumista
                                                               ei pitäisi jälkikäteen muokata muusta syystä kuin virheen vuoksi. Mikäli polveutumissäännöt muuttuvat,
                                                               aiemmin hyväksytyt hevoset ja niiden jälkeläiset ovat silti kelpuutettuja rodun jalostukseen!</span>');

                        }else {             
                            $fields['polv_pros'] = array('label' => 'Polveutumisprosentti', 'type' => 'text',
                                                         'after_html' => '<span class="form_comment">Korkeintaan kahdeksan desimaalin tarkkuudella. Käytä desimaalierotimena pistettä (.)</span>',
                                                         'value' => $horse['polv_pros'] ?? "", 'class'=>'form-control');
                        }
                        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url.'polveutumistarkastus/'.$reknro));
                        $data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
                        $data['title'] = 'Tarkasta hevosen ' . $horse['h_nimi'] .
                        ' (<a href="'.site_url().'virtuaalihevoset/hevonen/'. $reknro .'">'.$reknro.'</a>) polveutuminen';
                        
                        $this->fuel->pages->render('misc/haku', $data);
                                                
                        
                    }else {
                        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Sinulla ei ole oikeutta rodun ".$horse['h_rotunimi']." polveutumistarkastukseen. Ota yhteyttä rodun rotuyhdistykseen!"));

                    }
                    
                }else {
                    $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Etsimääsi hevosta ".$reknro." ei löytynyt."));

                }
          
            } else IF ($this->input->server('REQUEST_METHOD') == 'POST'){
                if($this->vrl_helper->check_vh_syntax($this->input->post('reknro'))){
                    $this->polveutumistarkastus($this->input->post('reknro'));
                }else {
                    $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Virheellinen VH-tunnus."));

                }
            }else {
                    $this->load->library('form_builder', array('submit_value' => "Hae", 'required_text' => '*Pakollinen kenttä'));
                    $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url.'polveutumistarkastus'));           
                    $fields['reknro'] = array('type' => 'text', 'label' => "Rekisterumero", 'required' => TRUE, 'class'=>'form-control');
               
                    
                    $data['form'] =  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
                    $data['title'] = "Hae hevonen polveutumistarkastusta varten";
                     $this->fuel->pages->render('misc/haku', $data);
                    
            }
            
        }       

    }
   
    
    function varit($msg = array(), $return = false){
        
         $data = $msg;
         $data['title'] = 'Muokkaa värilistaa';
         $data['text_view'] = "<p>Olemassaolevan värin muokkaaminen vaikuttaa kaikkiin hevosiin, joille ko. väri on rekisteröity! Väriä ei voi poistaa, jos sille on rekisteröity yksikin hevonen.</p>";
         

        //start the form
            
        if(!$return && $this->input->server('REQUEST_METHOD') == 'POST'){
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
        $msg = "";
        if($tapa == null || $id == null){           
           $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Käsiteltävää väriä ei ole valittu"));
        }
        else if(!$this->_is_editing_color_allowed($id, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
        }
        else {
            $this->load->model("Color_model");

            if ($tapa == "poista"){
                if ($this->Color_model->delete_vari($id) === false){
                    $this->varit(array('msg_type' => 'danger', 'msg' => "Et voi poistaa väriä jolle on rekisteröity hevosia"), true);
    
                }
                
                else {
                    $this->varit(array('msg_type' => 'success', 'msg' => "Poisto onnistui."), true);
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
                            $data['form'] = $this->_get_vari_form('edit',  $id);
                            $this->fuel->pages->render('misc/haku', $data);
                        }
        
                    else
                        {
                            if($this->Color_model->muokkaa_vari($data['msg'], $id, $this->input->post('vari'), $this->input->post('lyhenne'),
                                                                $this->input->post('pvari'), $this->input->post('geenit'))){
                                $data['msg'] = "Muokkaus onnistui! Katso väri <a href=\"". site_url('virtuaalihevoset/vari/'.$id) ."\">täältä</a>.";
                                $data['msg_type'] = "success";
                                $this->varit($data, true);
                            }else {
                                $data['msg_type'] = "danger";
                                $data['form'] = $this->_get_vari_form('edit',  $id);
                                $this->fuel->pages->render('misc/haku', $data);

                            }

                        }
                }else {
                    $data['form'] = $this->_get_vari_form('edit',  $id);
                    $this->fuel->pages->render('misc/haku', $data);

                    
                }
                
                

    
            }
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
    
      private function _is_editing_color_allowed($vid, &$msg){
        
        $newsitem = $this->Color_model->get_colour_info($vid);
        if ($newsitem === null || sizeof($newsitem) == 0){
            $msg = "Väriä jota yrität muokata ei ole olemassa.";
            return false;
        }
        

       
       return true;
        
        
        
    }  
    
    
    //////////////////////////////////
    //RODUT
    
    
     function rodut($msg = array(), $return = false){
        
         $data = $msg;
         $data['title'] = 'Muokkaa rotulistaa';
         $data['text_view'] = "<p>Olemassaolevan rodun muokkaaminen vaikuttaa kaikkiin hevosiin, joille ko. ritu on rekisteröity! Rotua ei voi poistaa, jos sille on rekisteröity yksikin hevonen.</p>";
         

        //start the form
            
        if(!$return && $this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            if ($this->_validate_breed_form('new') == FALSE)
                {
                    $data['msg'] = "Tallennus epäonnistui epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['form'] = $this->_get_breed_form('new');

                }

            else
                {
                   $data['form'] = $this->_get_breed_form('new');

                    //add 
                   $tid = $this->Breed_model->lisaa_rotu($data['msg'], $this->input->post('rotu'), $this->input->post('lyhenne'), $this->input->post('roturyhma'), $this->input->post('harvinainen'));
                   
                   if ($tid !== false){
                   
                    $data['msg'] = "Julkaisu onnistui! Katso uusi rotu <a href=\"".site_url('virtuaalihevoset/rotu/'.$tid) ."\">täältä</a>.";
                    $data['msg_type'] = "success";
                    }
                    else {
                        $data['msg_type'] = "danger";

                    }
                }
        }
        
        else  {
            $data['form'] = $this->_get_breed_form('new');
        }
                
        //start the list      
        $data['tulokset'] = $this->_rotutaulukko();
        $this->fuel->pages->render('misc/haku', $data);

    }
    
    
        private function _rotutaulukko(){
                                //start the list
        $this->load->model("Breed_model");
		
						
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'rotunro', 'key_link' => site_url('virtuaalihevoset/rotu/'));
		$vars['headers'][2] = array('title' => 'Rotu', 'key' => 'rotu');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
        $vars['headers'][4] = array('title' => 'Roturyhmä', 'key' => 'roturyhma');
        $vars['headers'][5] = array('title' => 'Harvinainen', 'key' => 'harvinainen');
        $vars['headers'][6] = array('title' => 'Poista', 'key' => 'rotunro', 'key_link' => site_url($this->url.'rotu/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][7] = array('title' => 'Editoi', 'key' => 'rotunro', 'key_link' => site_url($this->url.'rotu/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
  
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->Breed_model->get_breed_list());
        
        return  $this->load->view('misc/taulukko', $vars, TRUE);
        
    }
	
    
    
	
    
    function rotu ($tapa = null, $id = null){
        $msg = "";
        if($tapa == null || $id == null){           
           $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Käsiteltävää rotua ei ole valittu"));
        }
        else if(!$this->_is_editing_breed_allowed($id, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
        } else {
            $this->load->model("Breed_model");

            if ($tapa == "poista"){
                if ($this->Breed_model->delete_rotu($id) === false){
                    $this->rodut(array('msg_type' => 'danger', 'msg' => "Et voi poistaa rotua jolle on rekisteröity hevosia"), true);
    
                }
                
                else {
                    $this->rodut(array('msg_type' => 'success', 'msg' => "Poisto onnistui."), true);
                }
            }
            
            else if ($tapa == "muokkaa"){
                 $data = array();
                $data['title'] = "Muokkaa rotua";
                $data['data_view'] = "<p>Huom! Muokkaukset koskevat kaikkia hevosia, joille tämä rotu on rekisteröity. Käytäthän muokkausta vain virheiden korjaamiseen (kirjoitusvirheet jne).</p>";
                $data['msg'] = "";
                if($this->input->server('REQUEST_METHOD') == 'POST'){
                    $tid = 0;
                    if ($this->_validate_breed_form('edit') == FALSE)
                        {
                            $data['msg'] = "Rodun muokkaus epäonnistui!";
                            $data['msg_type'] = "danger";
                        }
        
                    else
                        {
    
                            if($this->Breed_model->muokkaa_rotu($data['msg'], $id, $this->input->post('rotu'), $this->input->post('lyhenne'),
                                                                $this->input->post('roturyhma'), $this->input->post('harvinainen'))){
                                $data['msg'] = "Muokkaus onnistui! Katso rotu <a href=\"". site_url('virtuaalihevoset/rotu/'.$id) ."\">täältä</a>.";
                                $data['msg_type'] = "success";
                                $this->rodut($data, true);


                            }else {
                                $data['msg_type'] = "danger";
                                $data['form'] = $this->_get_breed_form('edit',  $id);
                                $this->fuel->pages->render('misc/haku', $data);
                            }
                        }
                }else {
                
                
                $data['form'] = $this->_get_breed_form('edit',  $id);
                $this->fuel->pages->render('misc/haku', $data);
                }
    
            }
        } 
     
    
    }
    
    
    
 
    
    private function _get_breed_form($mode, $id = 0, $color = array()){
        if ($mode == "edit"){           
            $color = $this->Breed_model->get_breed_info($id);
            $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'rotu/muokkaa/'.$id));
           
        }
        else {

            $this->load->library('form_builder', array('submit_value' => "Lisää", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'rodut'));
        }     
            
        $fields['rotu'] = array('type' => 'text', 'label' => "Rotu", 'required' => TRUE, 'value' => $color['rotu'] ?? "", 'class'=>'form-control');
        $fields['lyhenne'] = array('type' => 'text', 'label' => "Lyhenne", 'required' => TRUE, 'value' => $color['lyhenne'] ?? "", 'class'=>'form-control');
        $fields['roturyhma'] = array('type' => 'select', 'label'=> 'Roturyhmä', 'options' => $this->Breed_model->get_breed_group_option_list(), 'required' => FALSE, 'value'=> $color['roturyhma'] ?? "0", 'class'=>'form-control');
        $fields['harvinainen'] = array('type' => 'enum', 'label'=> 'Harvinainen?','mode' => 'radios', 'options' => array(0=>"Ei", 1=>"Kyllä"), 'value'=> $color['harvinainen']?? 0);

        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_breed_form()
    {
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('rotu', 'Otsikko', "required|min_length[1]|max_length[240]");
        $this->form_validation->set_rules('lyhenne', 'Lyhenne', "required|min_length[1]|max_length[240]");
        return $this->form_validation->run();
    }
    
    private function _is_editing_breed_allowed($id, &$msg){
        
        $newsitem = $this->Breed_model->get_breed_info($id);
        if ($newsitem === null || sizeof($newsitem) == 0){
            $msg = "Rotua jota yrität muokata ei ole olemassa.";
            return false;
        }
        

       
       return true;
        
        
        
    }  
  
    

    
}
    

?>