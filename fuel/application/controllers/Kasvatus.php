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
		$this->_kasvattajanimihaku();
	}
    
    public function unelmasuku(){
        $this->load->library('vrl_helper');
        $this->load->library('form_collection');
        $vars['title'] = 'Unelmasuku';
        
        if(!$this->ion_auth->logged_in()){
            $vars['msg'] = "Kirjaudu sisään käyttääksesi unelmasukuhakua.";
            $vars['msg_type'] = 'danger';
        }else {

            $vars['text_view'] = "<p>Anna hevosten VH-tunnukset (isä, emä) ja voit kokeilla, miltä varsan sukutaulu tulisi näyttämään. Sukutaulussa näkyvät vain Virtuaaliseen Ratsastajainliittoon rekisteröidyt hevoset.</p>";
            
            $i_nro = "";
            $e_nro = "";
                
            if($this->input->server('REQUEST_METHOD') == 'POST')
            {
                $i_nro = $this->input->post('i_nro');
                $e_nro = $this->input->post('e_nro');
                
                if($this->vrl_helper->check_vh_syntax($i_nro) && $this->vrl_helper->check_vh_syntax($e_nro)){
                    $vars['suku'] = array();
                    $this->load->library("pedigree_printer");
                    $vars['pedigree_printer'] = & $this->pedigree_printer;
                    $this->load->model('Hevonen_model');
                    $this->Hevonen_model->get_unelmasuku($i_nro, $e_nro, $vars['suku']);
                }
                else {
                    $vars['msg'] = "Virheellinen VH-tunnus";
                    $vars['msg_type'] = 'danger';
                }
                
            }
            $vars['form'] = $this->_get_dreampedigree_form($i_nro, $e_nro);
        }
            $this->fuel->pages->render('jalostus/unelmasuku', $vars);
    }
        

    private function _get_dreampedigree_form($i="", $e=""){
        $this->load->library('form_builder', array('submit_value' => "Hae", 'required_text' => '*Pakollinen kenttä'));	
		$fields['i_nro'] = array('type' => 'text', 'label'=>'Isän VH-tunnus','required'=>TRUE, 'value' => $i, 'class'=>'form-control');
        $fields['e_nro'] = array('type' => 'text', 'label'=>'Emän VH-tunnus','required'=>TRUE, 'value' => $e, 'class'=>'form-control');
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('kasvatus/unelmasuku'));
        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);

    }
	
	
    
    public function kasvattajanimet($type = NULL, $id = NULL, $sivu = NULL, $tapa = NULL, $kohde = NULL)
    {		
		if($type == "omat"){
			$this->_omat_kasvattajanimet();
		}
		
		else if ($type == "nimi"){
			$this->_kasvattajanimi($id, $sivu);
			
		}
		
		else if($type == "rekisteroi"){
			$this->_kasvattajanimirekisterointi();
		}
		
		else if($type == "poista"){
			$this->_kasvattajanimi_poista($id);
		}
		
		else if($type == "muokkaa"){
			$this->_kasvattajanimet_muokkaa($id, $sivu, $tapa, $kohde);
		}
		
		else if ($type == "suuret"){
			$this->_kasvattajanimet_by_activity("DESC");
			
		}
		
		else if ($type == "pienet"){
			$this->_kasvattajanimet_by_activity("ASC");
			
		}
		
		else {
			$this->_kasvattajanimihaku();
		}
    }
	
	
	private function _kasvattajanimi($nimi, $sivu){
		if(empty($timi))
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kasvattajanimi-id puuttuu'));

		
		if(!$this->kasvattajanimi_model->is_name_id_in_use($nimi))
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kasvattajanimi-id:tä ei ole olemassa'));

		$this->load->library('Vrl_helper');

		$fields['sivu'] = $sivu;			
		$fields['nimi'] = $this->kasvattajanimi_model->get_name($nimi);
		$fields['nimi']['rekisteroity'] = $this->vrl_helper->sanitize_registration_date($fields['nimi']['rekisteroity']);
		$fields['owners'] = $this->kasvattajanimi_model->get_names_owners($nimi);
		
		if($this->ion_auth->logged_in()){		
			if($sivu == 'kasvatit'){				
					$fields['foals'] = $this->_nimen_kasvatit($nimi);
				}
				else if($sivu == 'rodut'){				
					$fields['breeds'] = $this->_nimen_rodut($nimi);
				}	
			}
			else {
				$fields['foals'] = "Kirjaudu sisään nähdäksesi tiedot.";
				$fields['breeds'] = "Kirjaudu sisään nähdäksesi tiedot.";
				
			}
		
		$this->fuel->pages->render('kasvattajanimet/kasvattajanimi', $fields);
    }
	
	
	
	
	private function _nimen_rodut($nro)
    {
		$vars['title'] = "";
		
		$vars['msg'] = '';
		
		$vars['text_view'] = "";		
	
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'rotunro', 'key_link' => site_url('virtuaalihevoset/rotu/'));
		$vars['headers'][2] = array('title' => 'Rotu', 'key' => 'rotu');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->kasvattajanimi_model->get_names_breeds($nro));
		
		
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
	
	
	private function _nimen_kasvatit($nro)
    {
		$this->load->model('hevonen_model');
		$vars['title'] = "";	
		$vars['msg'] = '';
		$vars['text_view'] = "";		
	
	
		
			$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
			$vars['headers'][5] = array('checkbox_id' => "nakki[]", 'title' => 'Valitse', 'key' => 'rotunro');

			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->hevonen_model->get_names_foals_by_id($nro));
		
		
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
	
	
	
	
	
	private function _kasvattajanimihaku(){
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		$data['title'] = 'Kasvattajanimihaku';
		
		$data['msg'] = 'Hae kasvattajanimiä rekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$data['text_view'] = $this->load->view('kasvattajanimet/teksti_etusivu', NULL, TRUE);
		
		$data['form'] = $this->_get_name_search_form();
		
		
		
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
	
			if($this->_validate_name_search_form() == true){
				if (!(empty($this->input->post('kasvattajanimi')) && $this->input->post('kasvatusrotu') == "-1"))
					{
						$vars['headers'][1] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi/'));
						$vars['headers'][2] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');
						$vars['headers'][3] = array('title' => 'Rodut', 'key' => 'lyhenne', 'aggregated_by' => 'id');
						$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
						if($this->ion_auth->is_admin()){
							$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
							$vars['headers'][6] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
						
						}
						
						
						$vars['headers'] = json_encode($vars['headers']);
						
						$vars['data'] = json_encode($this->kasvattajanimi_model->search_names($this->input->post('kasvattajanimi'), $this->input->post('kasvatusrotu')));
						
						$data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);

					}
					else {
						$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Haut ilman hakuehtoja eivät ole sallittuja'));
					
					}
			}
		}
		
		$this->fuel->pages->render('misc/haku', $data);

		
	}
	
	private function _kasvattajanimet_by_activity($type = "DESC"){
		
			if ($type == "ASC"){
				$vars['title'] = "Kasvattajanimet joilla on vähiten kasvatteja";
			}
			else {
				$type = "DESC";
				$vars['title'] = 'Aktiivisimmat kasvattajanimet';						
			}
			$vars['headers'][1] = array('title' => 'Kasvatteja', 'key' => 'amount');
			$vars['headers'][2] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi/'));
			$vars['headers'][3] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');
			$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
			if($this->ion_auth->is_admin()){
				$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
				$vars['headers'][6] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
				
			}
				

			$vars['headers'] = json_encode($vars['headers']);
				
			$vars['data'] = json_encode($this->kasvattajanimi_model->get_names_by_foal_count($type));
			
	
			$this->fuel->pages->render('misc/taulukko', $vars);
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
						
			$vars['headers'][1] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');

			$vars['headers'][2] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi/'));
			$vars['headers'][3] = array('title' => 'Rodut', 'key' => 'lyhenne', 'aggregated_by' => 'id');
			$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
			$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			$vars['headers'][6] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/delete.png'));

			$vars['headers'] = json_encode($vars['headers']);
				
			$vars['data'] = json_encode($this->kasvattajanimi_model->get_users_names($this->ion_auth->user()->row()->tunnus));
			
	
			$this->fuel->pages->render('misc/taulukko', $vars);
			

		}
    }
    
	//register a new name
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
				else if($this->kasvattajanimi_model->is_name_in_use($this->input->post('kasvattajanimi'))){
					$vars['msg'] = "Kasvattajanimi " .$this->input->post('kasvattajanimi')." on jo olemassa.";
					$vars['msg_type'] = "danger";
				}
				else
				{
					$vars['msg'] = "Rekisteröinti onnistui!";
					$vars['msg_type'] = "success";
					
					
					$insert_data = array();
					
					$date = new DateTime();
					$date->setTimestamp(time());
					$insert_data['rekisteroity'] = $date->format('Y-m-d H:i:s');
					if ($this->input->post('talli') != -1){
						$insert_data['tnro'] = $this->input->post('talli');
					}
					$insert_data['kasvattajanimi'] = $this->input->post('kasvattajanimi');
					
					//add name
					$this->kasvattajanimi_model->add_name($insert_data, $this->input->post('rotu'), $this->ion_auth->user()->row()->tunnus);

				}
				
				$this->fuel->pages->render('misc/jonorekisterointi', $vars);
			}
			else
				redirect('/', 'refresh');
		}
    }
	
	private function _kasvattajanimi_poista($id){
		$msg = "";
		
		//are there horses under this name?
        $this->load->model('hevonen_model');
		$foal_amount = $this->hevonen_model->count_breedingname_amount($id);
		if ($foal_amount > 0){
			$msg = "Kasvattajanimellä on " . $foal_amount . " kasvattia, joten sitä ei voi poistaa. Poista ensin kasvateilta kasvattajanimi!";
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));

		}

		else if(!$this->_is_editing_allowed($id, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
		
		else {
			$this->kasvattajanimi_model->delete_name($id);
				$msg = "Kasvattajanimi poistettu";
				$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'success', 'msg' => $msg));

		}
		
		
	}
	

	

    
	
    function _kasvattajanimet_muokkaa($nimi, $sivu, $tapa, $id)
    {

		$mode = 'none';
		$msg = "";

		if(!$this->_is_editing_allowed($nimi, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
		else {
			$mode = 'edit';
			if($this->ion_auth->is_admin())
				$mode = 'admin';
				
			$fields['mode'] = $mode;
			$this->load->library('Vrl_helper');

			$fields['sivu'] = $sivu;			
			$fields['nimi'] = $this->kasvattajanimi_model->get_name($nimi);
			$fields['nimi']['rekisteroity'] = $this->vrl_helper->sanitize_registration_date($fields['nimi']['rekisteroity']);
			$fields['owners'] = $this->kasvattajanimi_model->get_names_owners($nimi);

						
	
			
		
			$vars['form'] = "Valitse välilehti, jota haluat muokata.";

			if($sivu == 'omistajat'){
				$this->load->library('ownership');
				if($fields['mode'] == 'admin' || $this->ownership->is_names_main_owner($this->ion_auth->user()->row()->tunnus, $nimi)){				
					$this->ownership->handle_name_ownerships($fields['mode'], $tapa, $this->ion_auth->user()->row()->tunnus, $id, $nimi, $fields);
							
					$fields['form'] = $this->ownership->get_owner_adding_form('kasvatus/kasvattajanimet/muokkaa/'.$nimi.'/');
					$fields['ownership'] = $this->ownership->name_ownerships($nimi, true, 'kasvatus/kasvattajanimet/muokkaa/'.$nimi.'/');
				} else {
					$fields['ownership'] = $this->ownership->name_ownerships($nimi, false, 'kasvatus/kasvattajanimet/muokkaa/'.$nimi.'/');
				}
			}
			else if($sivu == 'rodut'){
				
				if($this->input->server('REQUEST_METHOD') == 'POST')
				{
					if($this->input->post("rodut")){
						$msg = "";
						$type = "danger";
						$ok = $this->kasvattajanimi_model->update_breeds($nimi, $fields['nimi']['kasvattajanimi'], $msg);
						if ($ok){ $type = "success";}
						$fields['info'] = $this->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);
	
					}		
				}
			
				$fields['breeds'] = $this->_nimen_rodut($nimi);
				$hidden = array("nimi" => $nimi);
				$fields['form'] = form_open('kasvatus/kasvattajanimet/muokkaa/' . $nimi . '/rodut/', $hidden) . form_submit('rodut', 'Päivitä rodut');
			}		
			
			$this->fuel->pages->render('kasvattajanimet/kasvattajanimi_muokkaa', $fields);
		}
					
    }

	
       
	
	
	private $allowed_user_groups = array('admin', 'kasvattajanimirekisteri');

    
	private function _is_editing_allowed($id, &$msg){

		//stable nro is missing
		if(empty($id)){
			$msg = "Kasvattajanimi-id puuttuu!";
			return false;
		}
				
		//only logged in can edit
		if(!($this->ion_auth->logged_in())){
            $msg = "Kirjaudu sisään muokataksesi!";
			return false;
		}
		
		//are you admin or editor?
		$this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
		
		//only admin, editor and owner can edit
		if(!($this->ion_auth->is_admin()) && !$this->user_rights->is_allowed() && !($this->kasvattajanimi_model->is_name_owner($this->ion_auth->user()->row()->tunnus, $id))){
			$msg = "Jos et ole ylläpitäjä, voit muokata vain omaa kasvattajanimeäsi.";
			return false;
		}
		
		//does the stable exist?
		if(!$this->kasvattajanimi_model->is_name_id_in_use($id)){
			$msg = "Nimeä ei ole olemassa.";
			return false;
		}
		
		return true;		
		
	}
	
	

	
	
	
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
		$submit['application'] = array("text"=>"Rekisteröi kasvattajanimi", "url"=> site_url('kasvatus/kasvattajanimet/rekisteroi'));
		$submit['edit'] = array("text"=>"Muokkaa", "url"=> site_url('kasvatus/kasvattajanimet/muokkaa') . '/' . $id);
		$submit['admin'] = array("text"=>"Muokkaa", "url"=> site_url('kasvatus/kasvattajanimet/muokkaa') . '/' . $id);
		

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
        $this->form_validation->set_rules('kasvattajanimi', 'Nimi', "min_length[3]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
		$this->form_validation->set_rules('kasvatusrotu', 'Kategoria', 'min_length[1]|max_length[4]');
        return $this->form_validation->run();

    }
}
?>





