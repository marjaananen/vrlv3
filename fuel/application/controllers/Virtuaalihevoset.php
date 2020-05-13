<?php
class Virtuaalihevoset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model("hevonen_model");

    }
	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->haku();
	}
	
	public function haku()
    {
		$this->load->model('hevonen_model');
		$this->load->library('form_validation');
		$this->load->library('vrl_helper');
		$data['title'] = 'Hevosrekisteri';
		
		$data['msg'] = 'Hae hevosia rekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$data['text_view'] = $this->load->view('hevoset/etusivu_teksti', NULL, TRUE);
		
		$data['form'] = $this->get_horse_search_form();
		
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
			
			$vars['headers'] = json_encode($vars['headers']);
			
			if($this->validate_horse_search_form() == true)
			{
				$reknro = 0;
				if ($this->vrl_helper->check_vh_syntax($this->input->post('reknro'))){
					$reknro = $this->vrl_helper->vh_to_number($this->input->post('reknro'));
				}

				$vars['data'] = json_encode($this->hevonen_model->search_horse($reknro, $this->input->post('nimi'), $this->input->post('rotu'), $this->input->post('skp'),
																			   $this->input->post('kuollut'), $this->input->post('vari'), $this->input->post('syntynyt_v')));
			}
			
			$data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);

		}
		
		$this->fuel->pages->render('misc/haku', $data);
    }
	
	
	public function hevosprofiili ($reknro){
		$this->load->library("vrl_helper");
		$this->load->library("pedigree_printer");
				
		if(empty($reknro) || !$this->vrl_helper->check_vh_syntax($reknro)){
			ECHO "EI";
		}
		
		$vars = array();
		$vars['hevonen'] = null;
		
		$vars['hevonen'] = $this->hevonen_model->get_hevonen($reknro);
		$vars['hevonen']['rekisteroity'] = $this->vrl_helper->sanitize_registration_date($vars['hevonen']['rekisteroity']);
		$vars['suku'] = array();
		$vars['owners'] = $this->hevonen_model->get_horse_owners($reknro);
		$this->hevonen_model->get_suku($reknro, "", $vars['suku']);
		$vars['pedigree_printer'] = & $this->pedigree_printer;
		
		
		$this->fuel->pages->render('hevoset/profiili', $vars);
		

		
	}
	
	
	 function muokkaa($reknro, $sivu='tiedot', $tapa = null, $id = null)
    {
		$mode = 'edit';
		$msg = "";
		
		$data = array();
		$data['sivu'] = $sivu;
		
		$this->load->library("vrl_helper");
				
		if(empty($reknro) || !$this->vrl_helper->check_vh_syntax($reknro)){
			ECHO "EI";
		}

		if(!$this->_is_editing_allowed($reknro, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
			
	
		if($this->ion_auth->is_admin())
			$mode = 'admin';
			
		$data['hevonen'] = $this->hevonen_model->get_hevonen($reknro);
		

		if($sivu == 'omistajat'){
			$this->load->library('ownership');
			if($mode == 'admin' || $this->ownership->is_horses_main_owner($this->ion_auth->user()->row()->tunnus, $this->vrl_helper->vh_to_number($reknro))){				
				$this->ownership->handle_horse_ownerships($mode, $tapa, $this->ion_auth->user()->row()->tunnus, $id, $this->vrl_helper->vh_to_number($reknro), $data);				
				$data['form'] = $this->ownership->get_owner_adding_form('virtuaalihevoset/hevonen/muokkaa/'.$reknro.'/');
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, true, 'virtuaalihevoset/hevonen/muokkaa/'.$reknro.'/');
			} else {
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, false, 'virtuaalihevoset/hevonen/muokkaa/'.$reknro.'/');
			}
		}

		
		else if ($sivu == 'lopeta'){
			$data['editor'] = "TODO";
		}
		
		else if($sivu == 'tiedot'){
					
			$data['editor'] = "TODO";

	

					

	
			}
			
			
			
			$this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);
				
				
					
    }
	
	private $allowed_user_groups = array('admin', 'hevosrekisteri');

	

	private function _is_editing_allowed($reknro, &$msg){

		//stable nro is missing
		if(empty($reknro)){
			$msg = "Rekisterinumero puuttuu!";
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
		if(!($this->ion_auth->is_admin()) && !$this->user_rights->is_allowed() && !($this->hevonen_model->is_horse_owner($this->ion_auth->user()->row()->tunnus, $reknro))){
			$msg = "Jos et ole ylläpitäjä, voit muokata vain omia hevosiasi.";
			return false;
		}
		
		//does the stable exist?
		$this->load->library("vrl_helper");
		if(!$this->hevonen_model->onko_tunnus($this->vrl_helper->vh_to_number($reknro))){
			$msg = "Hevosta ei ole olemassa.";
			return false;
		}
		
		return true;		
		
	}
	
	
	
	///statistiikkasivut
	
	
	public function syntymamaat(){
		
		$vars['title'] = 'Syntymämaat ja lyhenteet';
				
		$vars['text_view'] = "";
		
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id', 'key_link' => site_url('virtuaalihevoset/maa/'));
		$vars['headers'][2] = array('title' => 'Maa', 'key' => 'maa');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyh');
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->hevonen_model->get_country_list());

		$this->fuel->pages->render('misc/taulukko', $vars);
		
		
		
	}
	
	public function maa($id){
		$vars = array();
		$vars['title'] = "Syntymämaan statistiikka";
		$vars['genders'] =  $this->hevonen_model->get_stats_country($id);
		$this->fuel->pages->render('hevoset/stats', $vars);
		
		
	}
	
	public function rodut(){
		
		$vars['title'] = 'Rodut ja lyhenteet';
				
		$vars['text_view'] = "";
		
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'rotunro', 'key_link' => site_url('virtuaalihevoset/rotu/'));
		$vars['headers'][2] = array('title' => 'Rotu', 'key' => 'rotu');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->hevonen_model->get_breed_list());

		$this->fuel->pages->render('misc/taulukko', $vars);
		
		
		
	}
	
	public function rotu($id){
		$vars = array();
		$vars['title'] = "Rodun statsit";
		$vars['genders'] =  $this->hevonen_model->get_stats_breed($id);
		$this->fuel->pages->render('hevoset/stats', $vars);
		
		
	}
	

	
	
	
	public function varit(){
		$this->load->model("color_model");
		
		$vars['title'] = 'Värilista';
						
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'vid', 'key_link' => site_url('virtuaalihevoset/vari/'));
		$vars['headers'][2] = array('title' => 'Väri', 'key' => 'vari');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->color_model->get_colour_list());

		$this->fuel->pages->render('misc/taulukko', $vars);
		
		
		
	}
	
	public function vari($id){
		$this->load->model("color_model");

		$vars['genders'] =  $this->hevonen_model->get_stats_colour($id);
		$vars['colour'] = $this->color_model->get_colour_info($id);		
		$vars['title'] = "Väri: " . $vars['colour']['vari'] . " (". $vars['colour']['lyhenne'] . ")";
		
		$vars['gene_lists'] = $this->color_model->get_genes_list($id);	
		
		
		$vars['other_data'] = array();
		$vars['other_data'][] = $vars['text_view'] = $this->load->view('hevoset/geenit', $vars, TRUE);
		
		$this->fuel->pages->render('hevoset/stats', $vars);



	}
	
	
	public function get_horse_search_form(){
		$r_options = $this->hevonen_model->get_breed_option_list();
		$r_options[-1] = "Ei väliä";
		$skp_options = $this->hevonen_model->get_gender_option_list();
		$skp_options[-1] = "Ei väliä";
		$color_options = $this->hevonen_model->get_color_option_list();
		$color_options[-1] = "Ei väliä";
		
		$this->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$fields['reknro'] = array('type' => 'text', 'class'=>'form-control');
		$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
		$fields['rotu'] = array('type' => 'select', 'options' => $r_options, 'value' => '-1', 'class'=>'form-control');
		$fields['skp'] = array('type' => 'select', 'options' => $skp_options, 'value' => '-1', 'class'=>'form-control');
		$fields['kuollut'] = array('type' => 'checkbox', 'checked' => false, 'class'=>'form-control');
		$fields['vari'] = array('type' => 'select', 'options' => $color_options, 'value' => '-1', 'class'=>'form-control');
		$fields['syntynyt_v'] = array('type' => 'text', 'label'=>'Syntymävuosi', 'class'=>'form-control');
		
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/virtuaalihevoset/haku'));
		        
		return $this->form_builder->render_template('_layouts/basic_form_template', $fields);

	}
	
	
	
	public function validate_horse_search_form(){
        $this->load->library('form_validation');
        
		$this->form_validation->set_rules('skp', 'Sukupuoli', 'min_length[1]|max_length[2]|numeric');
		$this->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('rotu', 'Rotu', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('syntynyt_v', 'Syntymävuosi', 'min_length[4]|max_length[4]|numeric');
        $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]");
        return $this->form_validation->run();

    }
	
	
}
?>





