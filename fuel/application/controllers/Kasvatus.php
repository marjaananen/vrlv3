<?php
class Kasvatus extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model('kasvattajanimi_model');
    }
	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->kasvattajanimihaku();
	}
	
	
    
    public function kasvattajanimet($type = NULL)
    {
		if($type == "omat"){
			$this->_omat_kasvattajanimet();
		}
		
		else if($type == "rekisteroi"){
			$this->_kasvattajanimirekisterointi();
		}
		
		else {
			$this->_kasvattajanimihaku();
		}

    }
	
	
	
	private function _kasvattajanimihaku(){
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		$vars['title'] = 'Kasvattajanimihaku';
		
		$vars['msg'] = 'Hae kasvattajanimiä rekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$vars['text_view'] = $this->load->view('kasvattajanimet/teksti_etusivu', NULL, TRUE);
		
		$vars['form'] = $this->_get_name_search_form();
		
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
	
			if($this->_validate_name_search_form() == true && !(empty($this->input->post('kasvattajanimi')) && $this->input->post('kasvatusrotu') == "-1"))
			{
				$vars['headers'][1] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi'));
				$vars['headers'][2] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');
				$vars['headers'][3] = array('title' => 'Rodut', 'key' => 'lyhenne', 'aggregated_by' => 'id');
				$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
				if($this->ion_auth->is_admin()){
					$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'tnro', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
					$vars['headers'][6] = array('title' => 'Poista', 'key' => 'tnro', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/edit.png'));
				
				}
				
				$vars['headers'] = json_encode($vars['headers']);
				
				$vars['data'] = json_encode($this->kasvattajanimi_model->search_names($this->input->post('kasvattajanimi'), $this->input->post('kasvatusrotu')));
			}
		}
		
		$this->fuel->pages->render('misc/haku', $vars);
		
		
	}
	
    private function _omat_kasvattajanimet()
    {
		if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään tarkastellaksesi tietojasi!'));
        }
		else {
			
			$vars['title'] = 'Omat kasvattajanimet';
					
			$vars['text_view'] = $this->load->view('kasvattajanimet/teksti_omat', NULL, TRUE);
						
		
			$vars['headers'][1] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi'));
			$vars['headers'][2] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');
			$vars['headers'][3] = array('title' => 'Rodut', 'key' => 'lyhenne', 'aggregated_by' => 'id');
			$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
			$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'tnro', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			$vars['headers'][6] = array('title' => 'Poista', 'key' => 'tnro', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/delete.png'));

			$vars['headers'] = json_encode($vars['headers']);
				
			$vars['data'] = json_encode($this->kasvattajanimi_model->get_users_names($this->ion_auth->user()->row()->tunnus));
			
	
			$this->fuel->pages->render('misc/haku', $vars);
			

		}
    }
    
	//register a new stable
    private function _kasvattajanimirekisterointi()
    {
		if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään rekisteröidäksesi kasvattajanimen!'));
        }
		else {
			$this->load->library('form_validation');
			$this->load->library('form_collection');
			$vars['title'] = 'Rekisteröi kasvattajanimi';
				
			if($this->input->server('REQUEST_METHOD') == 'GET')
			{
				$vars['form'] = $this->_get_name_form('application'); //pyydetään lomake hakemusmoodissa
				$vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Sinut merkitään kasvattajanimen omistajaksi. Voit lisätä nimelle lisää omistajia ja rotuja rekisteröinnin jälkeen.';				
				$this->fuel->pages->render('misc/jonorekisterointi', $vars);
			}
			else if($this->input->server('REQUEST_METHOD') == 'POST')
			{
			
				if ($this->_validate_name_form('application') == FALSE)
				{
					$vars['msg'] = "Rekisteröinti epäonnistui!";
					$vars['msg_type'] = "danger";
				}
				else
				{
					$vars['msg'] = "Rekisteröinti onnistui!";
					$vars['msg_type'] = "success";
					
					
					$insert_data = array();
					
					$date = new DateTime();
					$date->setTimestamp(time());
					
					$tnro_alpha = $this->input->post('lyhehd');
					
					
					
					$insert_data['nimi'] = $this->input->post('nimi');
					$insert_data['url'] =  $this->input->post('osoite');
					$insert_data['kuvaus'] = $this->input->post('kuvaus');
					$insert_data['perustettu'] = $date->format('Y-m-d H:i:s');
					$insert_data['tnro'] = strtoupper($tnro_alpha) . rand(1000, 9999);
					
					while($this->tallit_model->is_tnro_in_use($insert_data['tnro']))
					{
						$insert_data['tnro'] = strtoupper($tnro_alpha) . rand(1000, 9999);
					}
					
					//add stables, categories and owner
					$this->tallit_model->add_stable($insert_data, $this->input->post('kategoria'), $this->ion_auth->user()->row()->tunnus);

				}
				
				$this->fuel->pages->render('misc/jonorekisterointi', $vars);
			}
			else
				redirect('/', 'refresh');
		}
    }
    
	/*
    function muokkaa($tnro)
    {
		$mode = 'edit';
		$msg = "";

		if(!$this->_is_editing_allowed($tnro, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
			
	
		if($this->ion_auth->is_admin())
			$mode = 'admin';
		
			
		$vars['title'] = 'Muokkaa tallin tietoja';
		
		$this->load->library('form_validation');
		
			
		if($this->tallit_model->is_stable_active($tnro))
			$vars['append'] = "<p><a href='" . site_url('tallit/lopeta') . "/" . $tnro . "'>Lopeta talli</a></p>";
			
		if($this->input->server('REQUEST_METHOD') == 'GET')
			{
				$vars['form'] = $this->_get_stable_form($mode, $tnro); //pyydetään lomake muokkausmoodissa
				
				if($vars['form'] == ""){
					$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Virhe lomakkeen tulostamisessa. Ota yhteys ylläpitoon!'));	
				}
				else {
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);
				}
			}
		else if($this->input->server('REQUEST_METHOD') == 'POST')
			{
				
				if($this->_validate_stable_form($mode) == FALSE || count($this->input->post('kategoria')) == 0)
				{
					$vars['msg'] = "Muokkaus epäonnistui!";
					$vars['msg_type'] = "danger";
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);	

				}
				else
				{
					$vars['msg'] = "Muokkaus onnistui!";
					$vars['msg_type'] = "success";
					
					$this->tallit_model->edit_stable($this->input->post('nimi'), $this->input->post('kuvaus'), $this->input->post('osoite'), $tnro);
					$this->tallit_model->mass_edit_categories($tnro, $this->input->post('kategoria'));
					
					$vars['form'] = $this->_get_stable_form($mode, $tnro);
						
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);
				}
	
			}
			else
				$this->fuel->pages->render('misc/naytaviesti');
					
    }

       
	
	
	private $allowed_user_groups = array('admin', 'tallirekisteri');

    
	private function _is_editing_allowed($tnro, &$msg){

		//stable nro is missing
		if(empty($tnro)){
			$msg = "Tallitunnus puuttuu!";
			return false;
		}
				
		//only logged in can edit
		if(!($this->ion_auth->logged_in())){
            $msg = "Kirjaudu sisään muokataksesi tallia!";
			return false;
		}
		
		//are you admin or editor?
		$this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
		
		//only admin, editor and owner can edit
		if(!($this->ion_auth->is_admin()) && !$this->user_rights->is_allowed() && !($this->tallit_model->is_stable_owner($this->ion_auth->user()->row()->tunnus, $tnro))){
			$msg = "Jos et ole ylläpitäjä, voit muokata vain omaa talliasi";
			return false;
		}
		
		//does the stable exist?
		if(!$this->tallit_model->is_tnro_in_use($tnro)){
			$msg = "Tallia ei ole olemassa.";
			return false;
		}
		
		return true;		
		
	}
	
	
	*/
	private function _get_name_form($mode, $id=-1)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return "";
        
        $this->load->model('kasvattajanimi_model');
		$this->load->model('hevonen_model');
		$this->load->model('tallit_model');


		
		
		$name = array();
		$name['kasvattajanimi'] = "";
		$name['talli'] = "-1";
		$name['rotu'] = "-1";
		
				
		//fill it if needed
		if($mode == 'edit' || $mode == 'admin')
        {
            $name = $this->kasvattajanimi_model->get_name($id);					
		}
		
		if ($mode == "application" || $mode == "admin"){
			 $fields['kasvattajanimi'] = array('type' => 'text', 'required' => TRUE, 'value' => $name['kasvattajanimi'], 'class'=>'form-control');
		}
		else {
			$fields['kasvattajanimi'] = array('type' => 'readonly', 'required' => TRUE, 'value' => $name['kasvattajanimi'], 'class'=>'form-control');
		}
		
		if ($mode == "application"){
			$breed_options = $this->hevonen_model->get_breed_option_list();
			$breed_options[-1] = "Valitse rotu";
			$fields['rotu'] = array('type' => 'select', 'required' => TRUE, 'options' => $breed_options, 'value'=>$name['rotu'], 'class'=>'form-control', 'wrapper_tag' => 'li');
		}
		

		$stable_options = array();
		
		if ($mode == "application" || $mode == "edit"){
			$stables = $this->tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
			foreach ($stables as $stable) {
                $stable_options[$stable['tnro']] = $stable['nimi'];
            }
			
		}
		
		else if (isset($name['talli']) && $name['talli'] != "-1") {
			$stable = $this->tallit_model->get_stable($name['talli']);
			$stable_options[$name['talli']] = $stable['nimi'];
			
		}
		
		$stable_options[-1] = "Ei tallia";
		$fields['talli'] = array('type' => 'select', 'required' => FALSE, 'options' => $stable_options, 'value'=>$name['talli'], 'class'=>'form-control', 'wrapper_tag' => 'li');

		
		
		//submit buttons
		$submit = array();
		$submit['application'] = array("text"=>"Rekisteröi kasvattajanimi", "url"=> site_url('kasvattajanimet/rekisteroi'));
		$submit['edit'] = array("text"=>"Muokkaa", "url"=> site_url('kasvattajanimet/muokkaa') . '/' . $id);
		$submit['admin'] = array("text"=>"Muokkaa", "url"=> site_url('kasvattajanimet/muokkaa') . '/' . $id);
		

		//start the form
		$this->load->library('form_builder', array('submit_value' => $submit[$mode]['text'], 'required_text' => '*Pakollinen kenttä'));

		$this->form_builder->form_attrs = array('method' => 'post', 'action' => $submit[$mode]["url"]);


        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_name_form($mode)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return false;
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('kasvattajanimi', 'Kasvattajanimi', "required|min_length[1]|max_length[128]");
        $this->form_validation->set_rules('rotu', 'Rotu', "required|min_length[2]|max_length[4]");
        $this->form_validation->set_rules('talli', 'Talli', "min_length[0]|max_length[8]");
        
        return $this->form_validation->run();
    }
    
    
    private function _get_name_search_form(){
		$this->load->model('hevonen_model');
        $options = $this->hevonen_model->get_breed_option_list();
        $this->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$options[-1] = 'Mikä tahansa';
		
		$fields['kasvattajanimi'] = array('type' => 'text', 'class'=>'form-control');
		$fields['kasvatusrotu'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');
	
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('kasvatus/kasvattajanimet'));
		return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_name_search_form(){
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('kasvattajanimi', 'Nimi', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
		$this->form_validation->set_rules('kasvatusrotu', 'Kategoria', 'min_length[1]|max_length[4]');
        return $this->form_validation->run();

    }
}
?>





