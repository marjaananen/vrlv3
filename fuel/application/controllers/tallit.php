<?php
class Tallit extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model('tallit_model');
    }
	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->haku();
	}
	
	public function uusimmat (){
		$vars['title'] = 'Uusimmat tallit';
				
		$vars['text_view'] = $this->load->view('tallit/teksti_uusimmat', NULL, TRUE);
		
		$vars['headers'][1] = array('title' => 'Perustettu', 'key' => 'perustettu', 'type' => 'date');
		$vars['headers'][2] = array('title' => 'Tallinumero', 'key' => 'tnro', 'key_link' => site_url('tallit/talli/'));
		$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
		if($this->ion_auth->is_admin()){
			$vars['headers'][4] = array('title' => 'Editoi', 'key' => 'tnro', 'key_link' => site_url('tallit/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
		}
		
		$vars['headers'] = json_encode($vars['headers']);
		
		$stables = $this->tallit_model->search_stables_newest();		
		$vars['data'] = json_encode($stables);

		$this->fuel->pages->render('misc/haku', $vars);
	}
	

	
	public function paivitetyt (){

		$vars['title'] = 'Viimeksi päivitetyt tallit';
				
		$vars['text_view'] = $this->load->view('tallit/teksti_updated', NULL, TRUE);
		
		$vars['headers'][1] = array('title' => 'Päivitetty', 'key' => 'aika', 'type' => 'date');
		$vars['headers'][2] = array('title' => 'Tallinumero', 'key' => 'tnro', 'key_link' => site_url('tallit/talli/'));
		$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
		if($this->ion_auth->is_admin()){
			$vars['headers'][4] = array('title' => 'Editoi', 'key' => 'tnro', 'key_link' => site_url('tallit/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
		}
		
		$vars['headers'] = json_encode($vars['headers']);
		
		$stables = $this->tallit_model->search_stables_updated();		
		$vars['data'] = json_encode($stables);

		$this->fuel->pages->render('misc/haku', $vars);
    
	}
	
	
	
	public function talliprofiili($tnro="")
    {
		
		if(empty($tnro))
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Tallitunnus puuttuu'));

		
		if(!$this->tallit_model->is_tnro_in_use($tnro))
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Tallitunnusta ei ole olemassa'));

			
		$vars['stable'] = $this->tallit_model->get_stable($tnro);
		$vars['categories'] = $this->tallit_model->get_stables_categories($tnro);
		$vars['owners'] = $this->tallit_model->get_stables_owners($tnro);		
		
		$this->fuel->pages->render('tallit/profiili', $vars);
    }
	
	
    
    function haku()
    {
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		$vars['title'] = 'Tallihaku';
		
		$vars['msg'] = 'Hae talleja tallirekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$vars['text_view'] = $this->load->view('tallit/teksti_etusivu', NULL, TRUE);
		
		$vars['form'] = $this->_get_stable_search_form();
		
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
	
			if($this->_validate_stable_search_form() == true && !(empty($this->input->post('nimi')) && empty($this->input->post('tallinumero')) && $this->input->post('kategoria') == "-1"))
			{
				$vars['headers'][1] = array('title' => 'Tallinumero', 'key' => 'tnro', 'key_link' => site_url('tallit/talli/'));
				$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
				$vars['headers'][3] = array('title' => 'Kategoria', 'key' => 'katelyh', 'aggregated_by' => 'tnro');
				$vars['headers'][4] = array('title' => 'Perustettu', 'key' => 'perustettu', 'type' => 'date');
				if($this->ion_auth->is_admin()){
					$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'tnro', 'key_link' => site_url('tallit/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
				}
				
				$vars['headers'] = json_encode($vars['headers']);
				
				$vars['data'] = json_encode($this->tallit_model->search_stables($this->input->post('nimi'), $this->input->post('kategoria'), $this->input->post('tallinumero')));
			}
		}
		
		$this->fuel->pages->render('misc/haku', $vars);
    }
	
	
    //user's own stables
    function omat()
    {
		if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään tarkastellaksesi tallejasi!'));
        }
		else {
			
			$vars['stables'] = $this->tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
			
			$this->fuel->pages->render('/tallit/omat', $vars);
		}
    }
    
	//register a new stable
    function rekisterointi()
    {
		if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään rekisteröidäksesi tallin!'));
        }
		else {
			$this->load->library('form_validation');
			$this->load->library('form_collection');
			$vars['title'] = 'Rekisteröi talli';
				
			if($this->input->server('REQUEST_METHOD') == 'GET')
			{
				$vars['form'] = $this->_get_stable_form('application'); //pyydetään lomake hakemusmoodissa
				$vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Muista, että tallin kaikilta pääsivuilta tulee olla löydettävissä sana "virtuaalitalli"! Sinut merkitään tallin omistajaksi. Voit lisätä tallille lisää omistajia rekisteröinnin jälkeen.';
				
				$this->fuel->pages->render('misc/jonorekisterointi', $vars);
			}
			else if($this->input->server('REQUEST_METHOD') == 'POST')
			{
			
				if ($this->_validate_stable_form('application') == FALSE)
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
    
    function lopeta($tnro)
    {
		if(!$this->_is_editing_allowed($tnro, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}			
			        
        $this->tallit_model->mark_stable_inactive($tnro);
            
        $this->fuel->pages->render('misc/naytaviesti', array('msg' => 'Tallisi on merkattu lopettaneeksi.'));
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
	
	
	
	private function _get_stable_form($mode, $tnro=-1)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return "";
        
        $this->load->model('tallit_model');
		
		//set up empty stable array
		$stable = array();
		$stable['nimi'] ="";
		$stable['kategoria'] = array();
		$stable['kuvaus'] = "";
		$stable['url'] = "http://";
		$stable['lyhehd'] = "";
		$stable['tallinumero'] = "";
		
		//fill it if needed
		if($mode == 'edit' || $mode == 'admin')
        {
            $stable = $this->tallit_model->get_stable($tnro);
					
			$cats = $this->tallit_model->get_stables_categories($tnro);
            foreach ($cats as $cat) {
                $stable['kategoria'] = $cat['kategoria'];
            }
		}
		
		//submit buttons
		$submit = array();
		$submit['application'] = array("text"=>"Rekisteröi talli", "url"=> site_url('tallit/rekisteroiNTI'));
		$submit['edit'] = array("text"=>"Muokkaa", "url"=> site_url('tallit/muokkaa') . '/' . $tnro);
		$submit['admin'] = array("text"=>"Muokkaa", "url"=> site_url('tallit/muokkaa') . '/' . $tnro);
		

		//start the form
		$this->load->library('form_builder', array('submit_value' => $submit[$mode]['text'], 'required_text' => '*Pakollinen kenttä'));
		
		$fields['nimi'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['nimi'], 'class'=>'form-control');
        $fields['kuvaus'] = array('type' => 'textarea', 'value' => $stable['kuvaus'], 'cols' => 40, 'rows' => 3, 'class'=>'form-control');
        $fields['osoite'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['url'], 'class'=>'form-control');
		$fields['kategoria'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE, 'options' => $this->tallit_model->get_category_option_list(), 'value'=>$stable['kategoria'], 'class'=>'form-control', 'wrapper_tag' => 'li');


        //make edits depending on the mode
        if($mode == 'application')
        {
            $fields['lyhehd'] = array('type' => 'text', 'label' => 'Lyhenne ehdotus', 'after_html' => '<span class="form_comment">2-4 merkin lyhenne tallillesi. Tästä muodostuu tallitunnus.</span>', 'class'=>'form-control');          
        }
           
		if($mode == 'admin')
		{
			$fields['tallinumero'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['tnro'], 'class'=>'form-control');
		}

		$this->form_builder->form_attrs = array('method' => 'post', 'action' => $submit[$mode]["url"]);


        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_stable_form($mode)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return false;
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nimi', 'Nimi', "required|min_length[1]|max_length[128]");
        $this->form_validation->set_rules('kuvaus', 'Kuvaus', "max_length[1024]");
        $this->form_validation->set_rules('osoite', 'Osoite', "required|min_length[4]|max_length[1024]|regex_match[/^[A-Za-z0-9_\-.:,; \/*~#&'@()]*$/]");

        if($mode == 'application')
        {
            $this->form_validation->set_rules('lyhehd', 'Lyhenne ehdotus', "required|min_length[2]|max_length[4]");
        }
        
        if($mode == 'admin')
        {
            $this->form_validation->set_rules('tallinumero', 'Tallinumero', "required|min_length[6]|max_length[8]");
            //ei täydellinen check!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        
        return $this->form_validation->run();
    }
    
    
    private function _get_stable_search_form(){
        $options = $this->tallit_model->get_category_option_list();
        $this->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$options[-1] = 'Mikä tahansa';
		
		$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
		$fields['kategoria'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');
		$fields['tallinumero'] = array('type' => 'text', 'class'=>'form-control');
	
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/tallit/haku'));
		return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_stable_search_form(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
		$this->form_validation->set_rules('kategoria', 'Kategoria', 'min_length[1]|max_length[2]');
		$this->form_validation->set_rules('tallinumero', 'Tallinumero', "min_length[6]|max_length[8]|regex_match[/^[A-Z0-9]*$/]");
        return $this->form_validation->run();

    }
}
?>





