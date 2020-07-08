<?php
class Virtuaalihevoset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model("hevonen_model");
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));

    }
    
    private $allowed_user_groups = array('admin', 'hevosrekisteri');

	
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
            if($this->user_rights->is_allowed()){
				$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			}
			
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
	
	
	public function hevosprofiili ($reknro, $sivu = ""){
		$this->load->library("vrl_helper");
		$this->load->library("pedigree_printer");
				
		if(empty($reknro) || !$this->vrl_helper->check_vh_syntax($reknro)){
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Rekisterinumero on virheellinen.'));

		}
		
		$vars = array();
        $vars['sivu'] = $sivu;
		$vars['hevonen'] = $this->hevonen_model->get_hevonen($reknro);
        
        if(sizeof($vars['hevonen']) == 0){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Etsimääsi hevosta ei löydy.'));

        }
        
        $vars['owners'] = $this->hevonen_model->get_horse_owners($reknro);
		$vars['hevonen']['rekisteroity'] = $this->vrl_helper->sanitize_registration_date($vars['hevonen']['rekisteroity']);
        
        if ($sivu == 'varsat'){
            $vars['foals'] = $this->_hevosen_varsat($vars['hevonen']['reknro']);

        } else if ($sivu == 'kilpailut'){
            $this->load->library("Porrastetut");
            $vars['traits'] = $this->porrastetut->get_trait_names_array();
            $vars['horse_traits'] = $this->porrastetut->get_horses_full_traitlist($reknro);
            $vars['horse_levels'] = $this->porrastetut->get_horses_full_level_list($reknro);
            
            $vars['porr_stats'] = $this->load->view('hevoset/porrastetut_stats', $vars, TRUE);
            $vars['porr_levels'] = $this->load->view('hevoset/porrastetut_levels', $vars, TRUE);

            $vars['kilpailut'] = "Perinteisten kilpailujen tiedot puuttuvat.";
        }
        else {
            $vars['suku'] = array();
            $this->hevonen_model->get_suku($reknro, "", $vars['suku']);
            $vars['pedigree_printer'] = & $this->pedigree_printer;
        }       

		
		
		$this->fuel->pages->render('hevoset/profiili', $vars);
		
		
	}
    
    	
	private function _hevosen_varsat($reknro)
    {
		$vars['title'] = "";
		
		$vars['msg'] = '';
		
		$vars['text_view'] = "";		
	
		
			$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
            $vars['headers'][2] = array('title' => 'Syntynyt', 'key' => 'syntymaaika', 'type'=>'date');
			$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][4] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][5] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->hevonen_model->get_horses_foals($reknro));
		
		
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
    
    function omat(){
        
         if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään nähdäksesi hevosesi!'));
        }

		$vars['title'] = "Omat hevoset";
		
		$vars['msg'] = '';
		
		$vars['text_view'] = "";		
	
		
			$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
			$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			
			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->hevonen_model->get_owners_horses($this->ion_auth->user()->row()->tunnus));
		
		
            $this->fuel->pages->render('misc/taulukko', $vars);
    
    }

    function rekisterointi(){
        if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään rekisteröidäksesi hevosen!'));
        }else {
            $vars['title'] = 'Rekisteröi hevonen';
            $this->load->library('vrl_helper');

            if($this->input->server('REQUEST_METHOD') == 'GET')
            {			        
                $vars['form'] = $this->_get_horse_edit_form('new');
                $vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Muista, että hevosen sivuilta tulee olla löydettävissä sana "virtuaalihevonen"! Sinut merkitään hevosen omistajaksi. Voit lisätä hevoselle lisää omistajia rekisteröinnin jälkeen.';
                $this->fuel->pages->render('misc/jonorekisterointi', $vars);
            }
            else if($this->input->server('REQUEST_METHOD') == 'POST'){
                $poni = $this->_fill_horse_info();
                $msg = "";
                if ($this->_validate_horse_form() == FALSE)
				{
					$vars['msg'] = "Rekisteröinti epäonnistui!";
					$vars['msg_type'] = "danger";
                    $vars['form'] = $this->_get_horse_edit_form('new', $poni);

                    $this->fuel->pages->render('misc/jonorekisterointi', $vars);

				}
                else if (!$this->_validate_horse('new', $poni, $msg)){
                    $vars['msg'] = "Rekisteröinti epäonnistui! " . $msg;
					$vars['msg_type'] = "danger";
                    $vars['form'] = $this->_get_horse_edit_form('new', $poni);
                    $this->fuel->pages->render('misc/jonorekisterointi', $vars);
                }
                else {
                    $vh = $this->hevonen_model->add_hevonen($poni, $this->ion_auth->user()->row()->tunnus, $msg);
                    if ($vh === false){
                        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));

                    }
                    else {                        
                        $this->hevosprofiili($vh);
                    }
                }                    
                
            }
        }
			
    }
	
	 function muokkaa($reknro, $sivu='tiedot', $tapa = null, $id = null)
    {
        $this->load->library("vrl_helper");
        $mode = "edit";
        
        if(!$this->_is_editing_allowed($reknro, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if($this->user_rights->is_allowed()){
			$mode = 'admin';
        }

		
		$data = array();
		$data['sivu'] = $sivu;
        $data['hevonen'] = $this->hevonen_model->get_hevonen_edit($reknro);
        $data['hevonen']['reknro'] = $this->vrl_helper->get_vh($data['hevonen']['reknro']);
        if (isset($data['hevonen']['i_nro'])){
            $data['hevonen']['i_nro'] = $this->vrl_helper->get_vh($data['hevonen']['i_nro']);
        }if(isset($data['hevonen']['e_nro'])){
            $data['hevonen']['e_nro'] = $this->vrl_helper->get_vh($data['hevonen']['e_nro']);
        }      

		if($sivu == 'omistajat'){

			$this->load->library('ownership');
			if($mode == 'admin' || $this->ownership->is_horses_main_owner($this->ion_auth->user()->row()->tunnus, $this->vrl_helper->vh_to_number($reknro))){				
				$this->ownership->handle_horse_ownerships($mode, $tapa, $this->ion_auth->user()->row()->tunnus, $id, $this->vrl_helper->vh_to_number($reknro), $data);				
				$data['form'] = $this->ownership->get_owner_adding_form('virtuaalihevoset/muokkaa/'.$reknro.'/');
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, true, 'virtuaalihevoset/muokkaa/'.$reknro.'/');
			} else {
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, false, 'virtuaalihevoset/muokkaa/'.$reknro.'/');
			}
            $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

		}

		
		else if ($sivu == 'lopeta'){
			$data['editor'] = $this->_horse_edit_form();
            
        }
        else if($sivu == 'tiedot'){
           if($this->input->server('REQUEST_METHOD') == 'POST'){
                $poni = $this->_fill_horse_info($mode);
                $msg = "";
                if ($this->_validate_horse_form($mode) == FALSE)
                {
                    $data['msg'] = "Muokkaus epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['editor'] = $this->_get_horse_edit_form($mode, $poni, $data['hevonen']['reknro']);
                    $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

    
                }
                else if (!$this->_validate_horse($mode, $poni, $msg)){
                    $data['msg'] = "Rekisteröinti epäonnistui! " . $msg;
                    $data['msg_type'] = "danger";
                    $data['editor'] = $this->_get_horse_edit_form($mode, $poni, $data['hevonen']['reknro']);
                    $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

                }
                else if (!$this->_validate_edits($mode, $poni, $data['hevonen']['reknro'], $msg)){
                    $data['msg'] = "Rekisteröinti epäonnistui! " . $msg;
                    $data['msg_type'] = "danger";
                    $data['editor'] = $this->_get_horse_edit_form($mode, $poni, $data['hevonen']['reknro']);
                    $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

                }
                else {
                    if (!$this->hevonen_model->edit_hevonen($poni, $data['hevonen']['reknro'], $msg)){
                        $data['msg'] = "Rekisteröinti epäonnistui! " . $msg;
                        $data['msg_type'] = "danger";
                        $data['editor'] = $this->_get_horse_edit_form($mode, $poni, $data['hevonen']['reknro']);
                        $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);
                    }
                    else {                        
                        $this->hevosprofiili($data['hevonen']['reknro']);
                        return;
                    }
                }

            }
            else {
                $data['editor'] = $this->_get_horse_edit_form($mode, $data['hevonen'], $data['hevonen']['reknro']);
                $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);
            }
        }
            
		
    }
	

	

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
        
        if (!$this->vrl_helper->check_vh_syntax(($this->vrl_helper->get_vh($this->vrl_helper->vh_to_number($reknro))))){
            $msg = "Virheellinen VH-tunnus";
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
    
    public function statistiikka($year = null){
		
		$vars['title'] = 'Rekisteröintistatistiikka';
        if (isset($year)){
            
			$vars['title'] .= ": vuosi " . $year;
        				
            $vars['text_view'] = "";
		
            $vars['headers'][1] = array('title' => 'ID', 'key' => 'rotunro', 'key_link' => site_url('virtuaalihevoset/rotu/'));
            $vars['headers'][2] = array('title' => 'Rotu', 'key' => 'rotu');
            $vars['headers'][3] = array('title' => 'Rekisteröityjä hevosia', 'key' => 'amount');
            $vars['headers'] = json_encode($vars['headers']);
                        
            $vars['data'] = json_encode($this->hevonen_model->get_stats_year_list($year));
            $this->fuel->pages->render('misc/taulukko', $vars);
        }
        else {
            $vars['text_view'] = "";
            $vars['headers'][1] = array('title' => 'Vuosi', 'key' => 'year', 'key_link' => site_url('virtuaalihevoset/statistiikka/'));
            $vars['headers'][2] = array('title' => 'Rekisteröityjä hevosia', 'key' => 'amount');
            $vars['headers'] = json_encode($vars['headers']);
                        
            $vars['data'] = json_encode($this->hevonen_model->get_stats_year_list());
    
            $this->fuel->pages->render('misc/taulukko', $vars);
        }
		
		
		
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
        $vars['headers'][4] = array('title' => 'Harvinainen', 'key' => 'harvinainen');
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
    
    
    
    
    
    private function _get_horse_edit_form($type = 'new', $poni = array(), $reknro = null){
		$r_options = $this->hevonen_model->get_breed_option_list();
		$r_options[-1] = "";
		$skp_options = $this->hevonen_model->get_gender_option_list();
		$skp_options[-1] = "";
		$color_options = $this->hevonen_model->get_color_option_list();
		$color_options[-1] = "";
        $skill_options = $this->hevonen_model->get_skill_option_list();
		$skill_options[-1] = "";
        $country_options = $this->hevonen_model->get_country_option_list();
		$country_options[-1] = "";
		
        if(isset($poni['syntymaaika']) &&
           ($this->vrl_helper->validateDate($poni['syntymaaika'], 'Y-m-d') || $this->vrl_helper->validateDate($poni['syntymaaika'], 'Y-m-d H:i:s'))){
            $poni['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($poni['syntymaaika']);
        }
        
        if(isset($poni['kuollut']) && $poni['kuollut'] == 1 &&
           ($this->vrl_helper->validateDate($poni['kuol_pvm'], 'Y-m-d') || $this->vrl_helper->validateDate($poni['kuol_pvm'], 'Y-m-d H:i:s'))){
            $poni['kuol_pvm'] = $this->vrl_helper->sql_date_to_normal($poni['kuol_pvm']);
        }else {unset ($poni['kuol_pvm']);}




		//$fields['reknro'] = array('type' => 'text', 'class'=>'form-control');
        
        if($type == 'admin' || $type == 'new'){
            $fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['nimi'] ?? '', 'after_html' => '<span class="form_comment">Nimen tulee olla hyvien tapojen mukainen, eikä se saa olla jo käytössä samanrotuisella. Sopimattomat nimet sensuroidaan!</span>');
            $fields['skp'] = array('type' => 'select', 'label'=> 'Sukupuoli', 'options' => $skp_options, 'required' => TRUE, 'value'=> $poni['sukupuoli'] ?? -1, 'class'=>'form-control');
            $fields['rotu'] = array('type' => 'select', 'options' => $r_options, 'required' => TRUE, 'value'=> $poni['rotu'] ?? -1, 'class'=>'form-control');
            $fields['syntymaaika'] = array('type' => 'text', 'label'=>'Syntymäaika', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['syntymaaika'] ?? '', 'after_html' => '<span class="form_comment">Muodossa pp.kk.vvvv</span>');

        }
        $fields['url'] = array('type' => 'text', 'label'=> 'Hevosen sivujen osoite', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['url'] ?? 'http://');
        $fields['kuollut'] = array('type' => 'checkbox', 'checked' => $poni['kuollut'] ?? false, 'class'=>'form-control');
        $fields['kuol_pvm'] = array('type' => 'text', 'label'=>'Kuolinpäivä', 'class'=>'form-control', 'value'=> $poni['kuol_pvm'] ?? '', 'after_html' => '<span class="form_comment">Jätä tyhjäksi, jos hevonen ei ole kuollut.</span>'); 
        
        $fields['lisatiedot'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default"><div class="panel-heading">Lisätiedot (ei pakollisia)</div> <div class="panel-body"><div class="form-group">');
        
        $fields['sakakorkeus'] = array('type' => 'text', 'class'=>'form-control', 'value'=> $poni['sakakorkeus'] ?? '', 'after_html' => '<span class="form_comment">Säkäkorkeus numeroina (senttimetreinä)</span>');
        $fields['vari'] = array('type' => 'select', 'options' => $color_options,  'value'=> $poni['vari'] ?? '-1', 'class'=>'form-control','after_html' => '<span class="form_comment">Jos toivomasi väri ei löydy listalta, ole yhteydessä ylläpitoon.</span>');
		$fields['painotus'] = array('type' => 'select', 'options' => $skill_options, 'value' =>  $poni['painotus'] ?? -1, 'class'=>'form-control');
		$fields['syntymamaa'] = array('type' => 'select', 'options' => $country_options, 'value' => $poni['syntymamaa'] ?? -1, 'class'=>'form-control');
        $fields['kotitalli'] = array('type' => 'text', 'class'=>'form-control', 'value'=> $poni['kotitalli'] ?? '', 'after_html' => '<span class="form_comment">Tallin tunnus VRL:n rekisterissä.</span>');

                
               
        $fields['sukutiedot'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default"><div class="panel-heading">Suku- ja kasvattajatiedot (vain suvullisille)</div> <div class="panel-body"><div class="form-group">');
        
        $fields['kasvattajanimi'] = array('type' => 'text', 'class'=>'form-control', 'value'=> $poni['kasvattajanimi'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Jätä tyhjäksi, jos kyseessä evm-hevonen.</span>');
        $fields['kasvattaja_talli'] = array('type' => 'text', 'class'=>'form-control', 'value'=> $poni['kasvattaja_talli'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Kasvattaneen tallin tunnus VRL:ssä. Jätä tyhjäksi, jos kyseessä evm-hevonen.</span>');
        $fields['kasvattaja_tunnus'] = array('type' => 'text', 'class'=>'form-control', 'value'=> $poni['kasvattaja_tunnus'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Kasvattajan VRL-tunnus. Jätä tyhjäksi, jos kyseessä evm-hevonen.</span>');
        $fields['i_nro'] = array('type' => 'text', 'label'=> 'Isän rekisterinumero','class'=>'form-control', 'value'=> $poni['i_nro'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Isän rekisterinumero.</span>');
        $fields['e_nro'] = array('type' => 'text', 'label'=> 'Emän rekisterinumero', 'class'=>'form-control', 'value'=> $poni['e_nro'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Emän rekisterinumero. </span>');
        
        $fields['end'] = array('type'=>'hidden', 'after_html' => '</div>');

        
        //uusi tai admin
        $submit = array();
        $submit['new'] = array('action' => site_url('/virtuaalihevoset/rekisterointi'), 'submit_value'=>'Rekisteröi');
        $submit['edit'] = array('action' => site_url('/virtuaalihevoset/muokkaa/'.$reknro), 'submit_value'=>'Muokkaa');
        $submit['admin'] = array('action' => site_url('/virtuaalihevoset/muokkaa/'.$reknro), 'submit_value'=>'Muokkaa');

                               
		$this->load->library('form_builder', array('submit_value' => $submit[$type]['submit_value']));
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => $submit[$type]['action']);
		        
		return $this->form_builder->render_template('_layouts/basic_form_template', $fields);

	}
    
    private function _validate_horse_form($type = 'new'){
        $this->load->library('form_validation');
        
        if($type == 'new' || $type == 'admin'){
            $this->form_validation->set_rules('skp', 'Sukupuoli', 'min_length[1]|max_length[2]|numeric|required');
            $this->form_validation->set_rules('rotu', 'Rotu', 'min_length[1]|max_length[4]|numeric|required');
            $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]|max_length[80]|required");
            $this->form_validation->set_rules('syntymaaika', 'Syntymäaika', 'min_length[10]|max_length[10]|required');
        }
      
		$this->form_validation->set_rules('url', 'Url', 'min_length[1]|max_length[360]');
        $this->form_validation->set_rules('kuollut', 'Kuolintieto', 'min_length[1]|max_length[1]|numeric');
        $this->form_validation->set_rules('kuol_pvm', 'Kuolinpäivä', 'min_length[10]|max_length[10]');
        $this->form_validation->set_rules('sakakorkeus', 'Säkäkorkeus', 'min_length[2]|max_length[3]|numeric');
        $this->form_validation->set_rules('painotus', 'Painotus', 'min_length[1]|max_length[3]|numeric');
		$this->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
        $this->form_validation->set_rules('syntymamaa', 'Syntymämaa', 'min_length[1]|max_length[4]|numeric');

		$this->form_validation->set_rules('kasvattajanimi', 'Kasvattajanimi', 'min_length[1]|max_length[25]');
        $this->form_validation->set_rules('kasvattaja_talli', 'Kasvattajatalli', 'min_length[4]|max_length[8]');
        $this->form_validation->set_rules('kasvattaja_tunnus', 'Kasvattajan VRL-tunnus', 'min_length[5]|max_length[10]');

        return $this->form_validation->run();        
    }
    private function _fill_horse_info($type = 'new'){
        $poni = array();
        
        if($type == 'new' || $type == 'admin'){
            $poni['nimi'] = $this->input->post('nimi');
            $poni['sukupuoli'] = $this->input->post('skp');
            $poni['syntymaaika'] = $this->input->post('syntymaaika');
        }
        $poni['url'] = $this->input->post('url');
        $poni['kuollut'] = $this->input->post('kuollut');
        if ($poni['kuollut'] == null){
            $poni['kuollut'] = false;
        }
        if($poni['kuollut']){
         $poni['kuol_pvm'] = $this->input->post('kuol_pvm');
        }
        if ($this->input->post('sakakorkeus')){
            $poni['sakakorkeus'] = $this->input->post('sakakorkeus');
        }
        if ($this->input->post('rotu') != -1 && ($type == 'new' || $type == 'admin')){
            $poni['rotu'] = $this->input->post('rotu');
        }
        if ($this->input->post('vari') != -1){
            $poni['vari'] = $this->input->post('vari');
        }
        if ($this->input->post('painotus') != -1){
            $poni['painotus'] = $this->input->post('painotus');
        }
        if ($this->input->post('syntymamaa') != -1){
            $poni['syntymamaa'] = $this->input->post('syntymamaa');
        }
        if($this->input->post('kotitalli')){
            $poni['kotitalli'] = $this->input->post('kotitalli');
        }
        if($this->input->post('kasvattajanimi')){
            $poni['kasvattajanimi'] = $this->input->post('kasvattajanimi');
        }
        if($this->input->post('kasvattaja_talli')){
            $poni['kasvattaja_talli'] = $this->input->post('kasvattaja_talli');
        }
        if ($this->input->post('kasvattaja_tunnus')){
            $poni['kasvattaja_tunnus'] = $this->input->post('kasvattaja_tunnus');
        }
        if($this->input->post('i_nro')){
            $poni['i_nro'] = $this->input->post('i_nro');
        }
        if($this->input->post('e_nro')){
            $poni['e_nro'] = $this->input->post('e_nro');
        }
        return $poni;
    }
    private function _validate_horse($type, $poni, &$msg){
        $msg = "";
        $this->load->model('Listat_model');
        $this->load->model('tallit_model');
        $genders = $this->hevonen_model->get_gender_option_list();
        if($type == 'new' || $type == 'admin'){ 
            
            if (!isset($poni['rotu']) || !$this->Listat_model->breed_exists($poni['rotu'])){
                $msg = "Rotu on virheellinen.";
                return false;
            }
            
            else if (!isset($genders[$poni['sukupuoli']])){
                    $msg = "Sukupuoli on virheellinen";
                    return false;
            }
            else if (!$this->vrl_helper->validateDate($poni['syntymaaika'])){     
                 $msg = "Syntymäaika on virheellinen";
                    return false;
            }
            else if($this->hevonen_model->onko_nimi($poni['nimi'], $poni['rotu']) && $type == 'new'){
                $msg = "Saman niminen ja rotuinen hevonen on jo rekisterissä.";
                return false;
            }
        }

        
        if (isset($poni['painotus']) && !($this->Listat_model->skill_exists($poni['painotus']))){
            $msg = "Painotusta ei ole olemassa.";
            return false;
        }
        else if (isset($poni['vari']) && !($this->Listat_model->colour_exists($poni['vari']))){
            $msg = "Väriä ei ole olemassa.";
            return false;
        }
        else if (isset($poni['syntymamaa']) && !($this->Listat_model->country_exists($poni['syntymamaa']))){
            $msg = "Maakoodia ei ole olemassa.";
            return false;
        }
        else if (isset($poni['kuollut']) && $poni['kuollut'] && $this->vrl_helper->validateDate($poni['kuol_pvm'])){     
            $msg = "Kuolinaika on virheellinen";
            return false;
        }
        else if ($type == 'new' && !$this->_check_parents($poni, $msg)){
            return false;
        }
        else if (isset($poni['kasvattajanimi']) && !empty($poni['kasvattajanimi']) && !(strpos($poni['nimi'], $poni['kasvattajanimi']) !== false)){
            $msg = "Ilmoittamasi kasvattajanimi ei ole hevosen nimessä. ";
            return false;
        }
        else if (isset($poni['kotitalli']) && !empty($poni['kotitalli']) && !$this->tallit_model->is_tnro_in_use($poni['kotitalli'])){
            $msg = "Kotitallin tunnus on virheellinen.";
            return false;
        }
        else if (isset($poni['kasvattaja_talli']) && !empty($poni['kasvattaja_talli']) && !$this->tallit_model->is_tnro_in_use($poni['kasvattaja_talli'])){
            $msg = "Kasvattajan tallitunnus on virheellinen.";
            return false;
        }
        else if (isset($poni['kasvattaja_tunnus']) && !empty($poni['kasvattaja_tunnus']) && !(
                 $this->vrl_helper->check_vrl_syntax($poni['kasvattaja_tunnus'])
                 && $this->tunnukset_model->onko_tunnus($this->vrl_helper->vrl_to_number($poni['kasvattaja_tunnus'])))){
            $msg = "Kasvattajan VRL-tunnus on virheellinen.";
            return false;
        }
                        
        return true;
    }
    
    private function _check_parents($poni, &$msg){
        if (isset($poni['i_nro'])
                 && !($this->vrl_helper->check_vh_syntax($poni['i_nro'])
                                            && ($this->hevonen_model->onko_tunnus_sukupuoli($this->vrl_helper->vh_to_number($poni['i_nro']), 2)
                                                                                            || $this->hevonen_model->onko_tunnus_sukupuoli($this->vrl_helper->vh_to_number($poni['i_nro']), 3)))){
            $msg = "Isän tunnus on virheellinen tai se on väärää sukupuolta.";
            return false;
        }
        else if (isset($poni['e_nro']) && !empty($poni['e_nro'])
                 &&!($this->vrl_helper->check_vh_syntax($poni['e_nro'])
                                                                     && $this->hevonen_model->onko_tunnus_sukupuoli($this->vrl_helper->vh_to_number($poni['e_nro']), 1))){
            $msg = "Emän tunnus on virheellinen tai se on väärää sukupuolta.";
            return false;

        }
        return true;
    }
    
    private function _validate_edits ($type, &$new, $tunnus, &$msg) {
        $old = $this->hevonen_model->get_hevonen_edit($tunnus);
        $foals = $this->hevonen_model->get_horses_foals($tunnus);
        //jos on varsoja, tamman sukupuolta ei saa vaihtaa, ja orin ja ruunankin saa vaihtaa vain toisikseen
        if (isset($new['rotu']) && sizeof($foals) > 0 && $new['rotu'] != $old['rotu']){
            $msg = "Hevosella on jälkeläisiä, joten sen rotua ei voi muokata.";
            return false;
        }

        else if(((isset($new['nimi']) && ($new['nimi'] != $old['nimi']))
                 || (isset($new['rotu']) && ($new['rotu'] != $old['rotu'])))
                && $this->hevonen_model->onko_nimi($new['nimi']?? $old['nimi'], $new['rotu'] ?? $old['rotu'])){
                $msg = "Saman niminen ja rotuinen hevonen on jo rekisterissä.";
                return false;
        }
        //jos on varsoja, tamman sukupuolta ei saa vaihtaa, ja orin ja ruunankin saa vaihtaa vain toisikseen
        else if (isset($new['sukupuoli']) && sizeof($foals) > 0 && $new['sukupuoli'] != $old['sukupuoli']){
            if ($old['sukupuoli'] == 1){
                $msg = "Tammalla on jälkeläisiä, joten sen sukupuolta ei voi muokata";
                return false;

            }
            else if ($new['sukupuoli'] == 1){
                $msg = "Orilla/ruunalla on jälkeläisiä, joten sitä ei voi muuttaa tammaksi.";
                return false;

            }
        }
        //jos on jälkeläisiä, vanhempia ei voi vaihtaa (mutta puuttuvan voi lisätä)
        else if ($type != 'admin' && sizeof($foals) > 0 && isset($new['i_nro']) && isset($old['i_nro']) && !empty($old['i_nro'])){
            $msg = "Hevosella on jälkeläisiä, joten ainoastaan rekisterityöntekijät voivat muokata sukua.";
            return false;
        }
        else if ($type != 'admin' && sizeof($foals) > 0 && isset($new['e_nro']) && isset($old['e_nro']) && !empty($old['e_nro'])){
            $msg = "Hevosella on jälkeläisiä, joten ainoastaan rekisterityöntekijät voivat muokata sukua.";
            return false;
        }
        
        if (isset($new['e_nro']) && $new['e_nro'] == $old['e_nro'] ){
            unset($new['e_nro']);
        } if (isset($new['i_nro']) && $new['i_nro'] == $old['i_nro'] ){
            unset($new['i_nro']);
        }
        
        if(isset($new['kuollut']) && $new['kuollut'] && !$old['kuollut'] ){
            $hevonen['kuol_merkkasi'] = $this->ion_auth->user()->row()->tunnus();
        }
        
        return true;
        
        
            
            
    }
	
	
}
?>





