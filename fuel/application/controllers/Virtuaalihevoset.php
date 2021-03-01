<?php
class Virtuaalihevoset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model("hevonen_model");
        $this->load->model("breed_model");
        $this->load->library("vrl_helper");

        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));

    }
    
    private $allowed_user_groups = array('admin', 'hevosrekisteri');
    private $vuodet = array("3", "4", "5", "6", "7", "8");

	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->haku();
	}
    
    public function hevosrekisteri(){
        $this->haku();
    }
    
    
	
	public function haku()
    {
		$this->load->model('hevonen_model');
		$this->load->library('form_validation');
		$this->load->library('vrl_helper');
        
        $hakudata = array();
		$data['title'] = 'Hevosrekisteri';
		
		$data['msg'] = 'Hae hevosia rekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$data['text_view'] = $this->load->view('hevoset/etusivu_teksti', NULL, TRUE);
		
		
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
            
            $hakudata['reknro'] = $this->input->post('reknro');
            $hakudata['nimi'] = $this->input->post('nimi');
            $hakudata['rotu'] = $this->input->post('rotu');
            $hakudata['sukupuoli'] = $this->input->post('sukupuoli');
            $hakudata['kuollut'] = $this->input->post('kuollut');
            $hakudata['vari'] = $this->input->post('vari');
            $hakudata['syntynyt_v'] = $this->input->post('syntynyt_v');
			
			if($this->validate_horse_search_form() == true)
			{
				$reknro = 0;
				if ($this->vrl_helper->check_vh_syntax($this->input->post('reknro'))){
					$reknro = $this->vrl_helper->vh_to_number($this->input->post('reknro'));
				}

				$vars['data'] = json_encode($this->hevonen_model->search_horse($reknro, $this->input->post('nimi'), $this->input->post('rotu'),
                                                                               $this->input->post('sukupuoli'),
																			   $this->input->post('kuollut'), $this->input->post('vari'),
                                                                               $this->input->post('syntynyt_v')));
			}
			
			$data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);

		}
		$data['form'] = $this->get_horse_search_form($hakudata);

		$this->fuel->pages->render('misc/haku', $data);
    }
    
    //////////////////////////////////////////////////////////////////////////////////
    // Profiili
    ///////////////////////////////////////////////////////////////////////////////////
	
	
	public function hevosprofiili ($reknro, $sivu = ""){
		$this->load->library("vrl_helper");
		$this->load->library("pedigree_printer");
        $this->load->model("jaos_model");
				
		if(empty($reknro) || !$this->vrl_helper->check_vh_syntax($reknro)){
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Rekisterinumero on virheellinen.'));

		}
		
		$vars = array();
        $vars['sivu'] = $sivu;
		$vars['hevonen'] = $this->hevonen_model->get_hevonen($reknro);
        $vars['edit_tools'] = $this->_is_editing_allowed($reknro, $msg);
        
        if(sizeof($vars['hevonen']) == 0){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Etsimääsi hevosta ei löydy.'));

        }
        
        $vars['owners'] = $this->hevonen_model->get_horse_owners($reknro);
        $vars['palkinnot'] = $this->jaos_model->get_event_horse_prizes($this->vrl_helper->vh_to_number($reknro));
        $vars['show_palkinnot'] = $this->jaos_model->get_show_horse_prizes($this->vrl_helper->vh_to_number($reknro));

		$vars['hevonen']['rekisteroity'] = $this->vrl_helper->sanitize_registration_date($vars['hevonen']['rekisteroity']);
        
        if ($sivu == 'varsat'){
            $vars['foals'] = $this->_hevosen_varsat($vars['hevonen']['reknro']);

        } else if ($sivu == 'porrastetut'){
            $this->load->library("Porrastetut");
            $vars['traits'] = $this->porrastetut->get_trait_names_array();
            $vars['horse_traits'] = $this->porrastetut->get_horses_full_traitlist($reknro);
            $vars['horse_levels'] = $this->porrastetut->get_horses_full_level_list($reknro);
            
            $vars['porr_stats'] = $this->load->view('hevoset/porrastetut_stats', $vars, TRUE);
            $vars['porr_levels'] = $this->load->view('hevoset/porrastetut_levels', $vars, TRUE);

            $vars['kilpailut'] = "Perinteisten kilpailujen tiedot puuttuvat.";
        } else if ($sivu == 'kilpailut'){
            $this->load->model("Jaos_model");
            $jaokset = $this->Jaos_model->get_jaokset_all();
            foreach ($jaokset as $jaos){
                $vars['jaokset'][$jaos['id']] = $jaos;
            }
            $vars['kisatiedot'] = $this->hevonen_model->get_horse_sport_info_by_jaos($reknro);
            $vars['kilpailut'] = $this->load->view('hevoset/kilpailut_stats', $vars, TRUE);
            
        }
        else {
            $vars['suku'] = array();
            $this->hevonen_model->get_suku($reknro, "", $vars['suku']);
            $vars['pedigree_printer'] = & $this->pedigree_printer;
        
        }
        
        $vars['vrl_helper'] = & $this->vrl_helper;

		
		
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
    
    
    /////////////////////////////////////////////////////////////////////////////////////7
    // Omat + massatuho
    //////////////////////////////////////////////////////////////////////////////////////
    
    var $jaokset;
    
    function omat(){
        if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään tarkastellaksesi omia hevosiasi!'));
        } else {
            $haku = array();
            $settings = array();
            $data = array();
            $tunnus = $this->ion_auth->user()->row()->tunnus;
            
            //luetaan hakukentät
            $this->_massatuho_read_search_input($haku, $settings);
       
       //     var_dump($this->input->post());
            
            //suorita operaatiot
            $data['msg'] = "";
            $data['msg_type'] = 'danger';
            if($this->_massatuho_suorita($tunnus, $data['msg'])){
               $data['msg_type'] = 'success';
            }

            
            //haetaan hevoset            
            $horses =  $this->hevonen_model->get_owners_horses($tunnus, true);
            $results =  $this->_massatuho_search($tunnus, $haku, $settings['sarakkeet'], $settings['massatuho']);
            
            
            //luodaan formi
            $this->load->library('form_builder', array('submit_value'=>'Hae'));    
            
            $fields = array();
            $this->_massatuho_setup_hakulomake($horses, $fields, array_merge($haku, $settings));
            
            if($settings['massatuho']){
                $this-> _massatuho_setup_operations_form($tunnus, $fields);
                $fields['hakutulokset_tulokset'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Hakutulokset',
                                                         'before_html'=>'</div></div></div>', 'after_html'=>$results);
            }else {
                
                
                //kasataan hakutulokset
                $fields['hakutulokset_tulokset'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Hakutulokset',
                                                              'after_html'=>$results);
            }
            
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/virtuaalihevoset/omat'));
            $this->form_builder->css_class = 'form-inline';
              
                    
            $data['form'] =  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
            $data['title'] = 'Kaikki omat hevoset';                        
            $vars['text_view'] = "";
            
             $sivu['sivu'] = 'omat';
            $sivu['data'] =  $this->load->view('misc/haku', $data, TRUE);
            
            $this->fuel->pages->render('hevoset/omat_hevoset', $sivu);

        }
        
    }
    
    function vastarekisteroidyt(){
        if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään tarkastellaksesi omia hevosiasi!'));
        }else {
            $vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
			$vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
			$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/muokkaa/'),
                                            'image' => site_url('assets/images/icons/edit.png'));
            $vars['headers'][6] = array('title' => 'Poista', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/poista/'),
                                            'image' => site_url('assets/images/icons/delete.png'));

            
            $vars['headers'] = json_encode($vars['headers']);                    
			$vars['data'] = json_encode($this->hevonen_model->get_just_registered($this->ion_auth->user()->row()->tunnus));
			$data['title'] = "Viimeisimmät rekisteröidyt";
            $data['text_view'] = "Tässä näkyy kuluneen 24h sisällä rekisteröidyt hevosesi. Alle vuorokauden rekisterissä olleita hevosia voi poistaa, mikäli niillä ei ole esim. jälkeläisiä.";
			$data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);
            
            
            $sivu['sivu'] = 'uudet';
            $sivu['data'] =  $this->load->view('misc/haku', $data, TRUE);
            
            $this->fuel->pages->render('hevoset/omat_hevoset', $sivu);


		}
		
    }
    
    private function _massatuho_setup_hakulomake($horses, &$fields, $haku = array()){
                $kotitalli_options = array();
                $painotus_options = array();
                $rotu_options = array();
                $kuollut_options = array();
                $leveled_options = array();
                $this->_massatuho_options($horses, $kotitalli_options, $rotu_options, $painotus_options, $kuollut_options, $leveled_options);
                $skp_options = $this->hevonen_model->get_gender_option_list();
                $skp_options[-1] = "Ei väliä";
                $kotitalli_options[-1] = "Ei väliä";
                $painotus_options[-1] = "Ei väliä";
                $rotu_options[-1] = "Ei väliä";
                $kuollut_options[-1] = "Ei väliä";
                $leveled_options[-1] = "Ei väliä"; 
                
                $fields['Hakukriteerit'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Hae hevosia', 'after_html' => '<div id="omatHevoset">');
                $fields['rotu'] = array('type' => 'select', 'options' => $rotu_options, 'value' => $haku['rotu'] ?? '-1', 'class'=>'form-control');
                $fields['painotus'] = array('type' => 'select', 'options' => $painotus_options, 'value' => $haku['painotus'] ?? '-1', 'class'=>'form-control');
                $fields['kotitalli'] = array('type' => 'select', 'options' => $kotitalli_options, 'value' => $haku['kotitalli'] ?? '-1', 'class'=>'form-control');
                $fields['sukupuoli'] = array('type' => 'select', 'options' => $skp_options, 'value' => $haku['sukupuoli'] ?? '-1', 'class'=>'form-control');
                $fields['porr_kilpailee'] = array('label'=>"Kilpailee porrastetuissa", 'type' => 'select', 'options'=>$leveled_options, 'value'=>$haku['porr_kilpailee'] ?? -1, 'class'=>'form-control');
                $fields['kuollut'] = array('type' => 'select', 'options'=>$kuollut_options, 'value'=>$haku['kuollut'] ?? 0, 'class'=>'form-control');
                $fields['sarakkeet'] = array('label'=>'Hakutuloksissa näytettävät sarakkeet', 'type' => 'enum', 'mode' => 'radios', 'required' => TRUE,
                                             'options' => array(1=>"perustiedot", 2=>"porrastettujen maksimit") , 'value'=>$haku['sarakkeet'] ?? 1, 'class'=>'form-check-input');
                $fields['massatuho'] = array('label'=>'Näytä massakäsittelytoiminnot', 'type' => 'checkbox', 'checked' => $haku['massatuho'] ?? false, 'class'=>'form-control','after_html' => '</div>');
                $fields['hae'] = array('type' => 'submit', 'value' => 'Hae');


    }
    private function _massatuho_setup_operations_form($tunnus, &$fields){
        $fields['operaatiot'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Massaoperaatiot valituille hevosille',
                                              'before_html' => '</div></div><div class="panel panel-default">
                                              <div class="panel-heading">Massatuhoase</div> <div class="panel-body"><div class="form-inline">');
        $fields['lopeta'] = array('type' => 'submit', 'value' => 'Lopeta valitut');
        $fields['porr_kylla'] = array('type' => 'submit', 'value' => 'Kilpailevat porrastetuissa');
        $fields['porr_ei'] = array('type' => 'submit', 'value' => 'Eivät kilpaile porrastetuissa');
        if(!isset($this->jaokset)){
            $this->load->model('Jaos_model');
            $this->jaokset = $this->Jaos_model->get_jaos_porr_list();
        }
        $fields['operaatiot2'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Maksimitasot', 'after_html'=>'<span class="form_comment">Nämä vaikuttavat hevosen näkymiseen kilpailulistalla. -1 estää hevosta näkymästä ko. lajin listalla. Kun hevonen ylittää tässä esitetyn maksimitasonsa,
                                                       se ei näy enää kyseisen lajin listalla. Sillä voi silti kilpailla normaalien sääntöjen puitteissa. Jätä tyhjäksi jos et halua muokata kyseistä arvoa.</span>'
);

        foreach($this->jaokset as $jaos){
            $fields[$jaos['id']] = array('label'=> 'Maksimitaso: ' . $jaos['lyhenne'], 'type' => 'select', 'options'=>array(-1=>'-1', 99=> "", 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'value'=>99, 'class'=>'form-control');
        }
        $fields['tasot'] = array('type' => 'submit', 'value' => 'Muokkaa maksimitasoja');
        
        $fields['operaatiot3'] = array('type' => 'section', 'tag' => 'h3', 'label' => 'Muokkaa', 'before_html'=>'</div><div class="form">');
        
        
        $this->load->model("Tallit_model");
        $this->load->library("vrl_helper");
        
        $skill_options = $this->hevonen_model->get_skill_option_list();
        $skill_options[-1] = "";
        $fields['aseta_painotus'] = array('label'=>'Painotus', 'type' => 'select', 'options' => $skill_options, 'value' =>  -1, 'class'=>'form-control',
                                          'after_html'=>'<span class="form_comment">Jätä tyhjäksi jos et halua muokata painotusta.</span>');
        
        
        $tallilista  = $this->Tallit_model->get_users_stables($tunnus, false, true);
        
        $tallit = array();
        foreach ($tallilista as $talli){
           $tallit[$talli['tnro']] = $talli['tnro'];
        }

        $option_script = $this->vrl_helper->get_option_script('aseta_kotitalli', $tallit);
        

        $fields['aseta_kotitalli'] = array('label'=>'Kotitalli', 'type' => 'text', 'class'=>'form-control', 
                                     'after_html'=> '<span class="form_comment">Jätä tyhjäksi jos et halua muokata kotitallia. Laita tunnus muodossa XXXX0000. Omat tallisi (klikkaa lisätäksesi): ' .
                                    $option_script['list'] . '</span>' . $option_script['script']);
        

        $fields['aseta'] = array('type' => 'submit', 'value' => 'Muokkaa tietoja');



        
    }
    private function _massatuho_read_search_input(&$haku = array(), &$settings = array()){
        $haku = array();
        $settings = array();
        $this->_massatuho_clean_input('rotu', $haku, -1);
        $this->_massatuho_clean_input('painotus', $haku, -1);
        $this->_massatuho_clean_input('kotitalli', $haku, -1);
        $this->_massatuho_clean_input('sukupuoli', $haku, -1);
        $this->_massatuho_clean_input('porr_kilpailee', $haku, -1);
        
        if($this->input->post('kuollut')){
            $haku['kuollut'] = 1;
        }else {
            $haku['kuollut'] = 0;
        }
        
        if(!isset($haku['kuollut'])){
            $haku['kuollut'] = 0;
        }
        
        $settings['massatuho']=0;
        $settings['sarakkeet'] = 1;
            $this->_massatuho_clean_input('sarakkeet', $settings, -1);
            if($this->input->post()){
                if($this->input->post('massatuho')){
                $settings['massatuho'] = $this->input->post('massatuho');
                }
                $settings['sarakkeet'] = $this->input->post('sarakkeet');
            }

    }
    
    private function _massatuho_search($user, $haku, $sarakkeet, $massatuho = false){
        $where = array();
        $leveled_list = false;
        $basic_list = false;
        
        foreach ($haku as $key=>$value){
            $where['h.'.$key] = $value;
        }
        
        if($sarakkeet == 2){
            $leveled_list = true;
        }else {
            $basic_list = true;
        }
        
        $this->db->from('vrlv3_hevosrekisteri as h');

        
        if($basic_list){
            $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli,
                          IFNULL(kotitalli,'') as kotitalli, r.rotunro, syntymaaika, kuollut, h.porr_kilpailee, h.sakakorkeus, h.painotus as painotusid,
                          t.nimi as tallinimi, IFNULL(p.painotus, '') AS painotus, r.rotunro");
            $this->db->join("vrlv3_tallirekisteri as t", "t.tnro = h.kotitalli", 'left outer');

        }else if($leveled_list){
            $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, IF(sukupuoli='1', 't', IF(sukupuoli='2', 'o', 'r')) as sukupuoli, h.sakakorkeus,
                              IFNULL(p.lyhenne, '') as painotus, k.jaos, k.taso_max, h.porr_kilpailee");
            $this->db->join("vrlv3_hevosrekisteri_kisatiedot as k", "k.reknro = h.reknro", "left");

        }
        $this->db->join('vrlv3_hevosrekisteri_omistajat as o', 'h.reknro = o.reknro');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_painotus as p", "h.painotus = p.pid", 'left outer');

        $this->db->where($where);
        $this->db->where('o.omistaja', $user);
        $query = $this->db->get();
        
        
        $horses = array();
        if ($query->num_rows() > 0)
        {
            $horses = $query->result_array();
        
        }
        
        $nro = 0;
        if($massatuho){
            $vars['headers'][1] = array('title' => '', 'key' => 'reknro', 'checkbox_id'=>'hevo');
            $nro = 1;
        }
        
		$vars['headers'][$nro + 1] = array('title' => 'VH', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
        $vars['headers'][$nro + 2] = array('title' => 'Nimi', 'key' => 'nimi');
        $vars['headers'][$nro + 3] = array('title' => 'Rotu', 'key' => 'rotu');
        
        $nro = $nro + 3;
        
        if($basic_list){
            $vars['headers'][$nro + 1] = array('title' => 'Skp', 'key' => 'sukupuoli');
            $vars['headers'][$nro + 2] = array('title' => 'Kotitalli', 'key' => 'kotitalli', 'key_link' => site_url('tallit/talli/'));
            $vars['headers'][$nro + 3] = array('title' => 'Kuollut', 'key' => 'kuollut', 'type'=>'bool');
            $nro = $nro+3;

        }else if($leveled_list){
            $vars['headers'][$nro + 1] = array('title' => 'Pai- notus', 'key' => 'painotus');
            $vars['headers'][$nro + 2] = array('title' => 'Kisaa porr.', 'key' => 'porr_kilpailee', 'type'=>'bool');
            $nro = $nro + 2;
            
            if(!isset($this->jaokset)){
                $this->load->model('Jaos_model');
                $this->jaokset = $this->Jaos_model->get_jaos_porr_list();
                
            }
            $counter = 1;
            foreach($this->jaokset as $jaos){
                $vars['headers'][$nro + $counter] = array('title' => $jaos['lyhenne'], 'key' => $jaos['lyhenne']);
                $counter = $counter + 1;
            }
            $nro = $nro + $counter -1;
            $horses = $this->_massatuho_sort_leveled_list($horses, $this->jaokset);
            
        }
        
        if(!$massatuho){
            $vars['headers'][$nro + 1] = array('title' => 'Edit', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
        }
                
                
        $vars['headers'] = json_encode($vars['headers']);                    
        $vars['data'] = json_encode($horses);
        
        return $this->load->view('misc/taulukko', $vars, TRUE);
            
    }
    
    private function _massatuho_clean_input($input, &$data, $novalue = -1){
        if($this->input->post($input) && $this->input->post($input) != $novalue){
            $data[$input] = $this->input->post($input);
        }
    }
    
    private function _massatuho_options($horses, &$stables, &$breeds, &$sports, &$dead, &$leveled){
        $stables = array();
        $breeds = array();
        $sports = array();
        
        $yesno = array (0=> "Ei", 1=> "Kyllä");
        $dead = $yesno;
        $leveled = $yesno;
        
        foreach ($horses as $horse){
            if (isset($horse['kotitalli']) && !empty($horse['kotitalli'])){
                $stables[$horse['kotitalli']] = $horse['tallinimi'] . " (".$horse['kotitalli'].")";
            }
            
            if (isset($horse['painotusid']) && !empty($horse['painotusid'])){
                $sports[$horse['painotusid']] = $horse['painotus'];
            }
            
            if (isset($horse['rotunro']) && !empty($horse['rotunro'])){
                $breeds[$horse['rotunro']] = $horse['rotu'];
            }
        }
        
    }
    
    private function _massatuho_sort_leveled_list($horses, $jaokset){
        $jaos_list = array();
        foreach ($jaokset as $jaos){
            $jaos_list[$jaos['id']] = $jaos;
        }
        
        $horses_list = array();
        
        foreach($horses as $horse){
            if(!isset($horses_list[$horse['reknro']])){
                $horses_list[$horse['reknro']] = $horse;
            }
            if(isset($jaos_list[$horse['jaos']])){
                $horses_list[$horse['reknro']][$jaos_list[$horse['jaos']]['lyhenne']] = $horse['taso_max'] ?? 10;
            }
        }
        
        $horses_temp = array();
        
        foreach ($horses_list as $horse){
            foreach ($jaokset as $jaos){
                if(!isset($horse[$jaos['lyhenne']])){
                    $horse[$jaos['lyhenne']] = 10;
                }
            }
            $horses_temp[] = $horse;
        }
        
        return $horses_temp;
        
        
    }
    
    private function _massatuho_suorita($user, &$msg){
        if($this->input->post() && ($this->input->post('lopeta') !== null
                                    || $this->input->post('porr_kylla') !== null
                                    || $this->input->post('porr_ei') !== null
                                    || $this->input->post('aseta') !== null
                                    || $this->input->post('tasot') !== null)){
            if($this->input->post('hevo') && sizeof($this->input->post('hevo')) > 0){
                $this->db->from('vrlv3_hevosrekisteri_omistajat as o');
                $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = o.omistaja');
    
                $this->db->where('o.omistaja', $user);
                $this->db->where_in('o.reknro', $this->input->post('hevo'));
                
                $query = $this->db->get();
                
                $edited_list = array();
                if ($query->num_rows() > 0)
                {
                    $this->load->library("vrl_helper");
                    $horses = array();
                    
                    foreach($query->result_array() as $horse){
                        $horses[] = $horse['reknro'];
                        $edited_list[] = $this->vrl_helper->get_vh($horse['reknro']);
                    }
    
                    $edit_data = array();
                    if($this->input->post('lopeta') !== null){
                        $edit_data['kuollut'] = 1;
                        $edit_data['kuol_merkkasi'] = $user;
                        $edit_data['kuol_pvm'] = date("Y-m-d");
                    } else if($this->input->post('porr_kylla')){
                        $edit_data['porr_kilpailee'] = 1;
                    }else if($this->input->post('porr_ei')){
                        $edit_data['porr_kilpailee'] = 0;
                    }else if($this->input->post('aseta')){
                        $painotus = $this->input->post('aseta_painotus');
                        $kotitalli = $this->input->post('aseta_kotitalli');
                        $this->load->model('Tallit_model');
                        if(isset($kotitalli) && strlen($kotitalli) > 4 && $this->Tallit_model->is_tnro_in_use($kotitalli)){
                        
                            $edit_data['kotitalli'] = $kotitalli;
                        }else {
                                $msg = "Antamasi kotitallin tunnus on virheellinen.";
                                return false;
                        }
                        
                        
                        if(isset($painotus) && strlen($painotus) > 0 && $painotus != -1){
                            $this->load->model('Listat_model');
                            if($this->Listat_model->skill_exists($painotus)){
                                $edit_data['painotus'] = $painotus;
                            }
                        }
                        
                    }
                    
                    if(sizeof($edit_data) > 0){
                        $this->db->where_in('reknro', $horses);
                        if(isset($edit_data['kuollut']) && $edit_data['kuollut'] == 1){
                            $this->db->where('kuollut', 0);
                        }
                        $this->db->update('vrlv3_hevosrekisteri', $edit_data);
                        $msg = "Muokkaus onnistui! Seuraavia hevosia muokattiin: " . implode(', ', $edited_list);
                        return true;
                    }
                    
                    if($this->input->post('tasot') !== null){
                        if(!isset($this->jaokset)){
                            $this->load->model('Jaos_model');
                            $this->jaokset = $this->Jaos_model->get_jaos_porr_list();
                        }
                        
                        //luetaan arvot
                        $values = array();
                        foreach ($this->jaokset as $jaokset){
                            $this->_massatuho_clean_input($jaokset['id'], $values, 99);
                        }
                        
                        if(sizeof($values) == 0){
                            $msg = "Et asettanut yhtäkään kilpailutasoa muokattavaksi.";
                            return false;
                        }else {
                        
                            $this->db->select('*');
                            $this->db->from('vrlv3_hevosrekisteri_kisatiedot');
                            $this->db->where_in('reknro', $horses);
                            $query = $this->db->get();
                            
                            $old_values = array();
                            foreach($query->result_array() as $result){
                                if(isset($oldvalues[$result['reknro']])){
                                    $oldvalues[$result['reknro']][$result['jaos']] = $result['taso_max'];
                                }else {
                                    $oldvalues[$result['reknro']] = array($result['jaos'] => $result['taso_max']);
                                }
                                
                            }
                            
                            $insert_values = array();
                            $update_values = array();
                            foreach ($horses as $horse){
                                foreach ($values as $jaos=>$value){
                                    if(isset($oldvalues[$horse][$jaos])){
                                        if($oldvalues[$horse][$jaos] != $value){
                                            if(!isset($update_values[$jaos])){
                                                $update_values[$jaos] = array();
                                            }
                                            $update_values[$jaos][] = $horse;
                                        } 
                                    }else {
                                        $insert_values[] =  array("reknro" => $horse, "jaos"=>$jaos, "taso_max"=>$value);
                                    }
                                }
                                
                            }
                            
                            if(sizeof($insert_values) > 0){
                                $this->db->insert_batch('vrlv3_hevosrekisteri_kisatiedot', $insert_values);
                            }
                            if(sizeof($update_values) > 0){
                                foreach ($update_values as $jaos => $horses_list){
                                    if(sizeof($horses_list) > 0){
                                        $this->db->where('jaos', $jaos);
                                        $this->db->where_in('reknro', $horses_list);
                                        $this->db->update('vrlv3_hevosrekisteri_kisatiedot', array("taso_max"=>$values[$jaos]));
                                    }
                                }
                            }
                        $msg = "Muokkaus onnistui! Seuraavia hevosia muokattiin: " . implode(', ', $edited_list);
                        return true;
                        }
                    }
                    
                    return true;
                } else {
                    $msg = "Et valinnut yhtään hevosta.";
                    return false;
                }
                
            
            }else {
             $msg = "Et valinnut yhtään hevosta.";
                    return false;
            }
        }
        return true;
        
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Rekisterointi
    /////////////////////////////////////////////////////////////////////////////////////////////////////////7//////
    

    function rekisterointi(){
        if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään rekisteröidäksesi hevosen!'));
        }else {
            $vars['sivu'] = 'yksi';
            $vars['title'] = 'Rekisteröi hevonen';
            $this->load->library('vrl_helper');

            if($this->input->server('REQUEST_METHOD') == 'GET')
            {			        
                $vars['form'] = $this->_get_horse_edit_form('new');
                $vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Muista, että hevosen sivuilta tulee olla löydettävissä sana "virtuaalihevonen"! Sinut merkitään hevosen omistajaksi. Voit lisätä hevoselle lisää omistajia rekisteröinnin jälkeen. <a href="' . site_url('virtuaalihevoset/rekisterointi/ohjeet') . '" title="Lue ohjeet hevosen rekisteröintiin">Lue ohjeet hevosen rekisteröintiin</a>.';
            $this->fuel->pages->render('hevoset/hevosten_rekisterointi', $vars);
            }
            else if($this->input->server('REQUEST_METHOD') == 'POST'){
                $poni = $this->_fill_horse_info();
                $msg = "";
                if (!$this->_validate_horse('new', $poni, $msg)){
                    $vars['msg'] = "Rekisteröinti epäonnistui! " . $msg;
					$vars['msg_type'] = "danger";
                    $vars['form'] = $this->_get_horse_edit_form('new', $poni);
            $this->fuel->pages->render('hevoset/hevosten_rekisterointi', $vars);
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
    public function rekisterointiohjeet(){
		$this->fuel->pages->render('hevoset/ohjeet');
	}
    
    function massarekisterointi(){
        
         if(!($this->ion_auth->logged_in()))
        {
            	$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään rekisteröidäksesi hevosen!'));
        } else {
            $data = array();
            
            $varsa = $this->_get_horse_edit_form('form');
            
            foreach($varsa as $nimi=>$field){
                if($field['type'] == 'section' || $field['type'] == 'hidden' || (isset($field['hidden']) && $field['hidden'] == true)){
                    continue;
                }else {
                    $kentta = $field;
                    if($field['type'] == 'checkbox'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">';
                        $kentta['kuvaus'] .= 'Arvo "0" tai "1", jossa 0 = ei ja 1 = kyllä</span>';

                    }else if($nimi == 'rotu'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">Arvo numerona';
                        $kentta['kuvaus'] .= ' (kts. <a href="'. site_url('virtuaalihevoset/rodut') . '">lista sallituista arvoista</a>)';
                        $kentta['kuvaus'] .= '</span>';
                    }
                    else if($nimi == 'vari'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">Arvo numerona';
                        $kentta['kuvaus'] .= ' (kts. <a href="'. site_url('virtuaalihevoset/varit') . '">lista sallituista arvoista</a>)';
                        $kentta['kuvaus'] .= '</span>';
                    }
                    else if($nimi == 'syntymamaa'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">Arvo numerona';
                        $kentta['kuvaus'] .= ' (kts. <a href="'. site_url('virtuaalihevoset/syntymamaat') . '">lista sallituista arvoista</a>)';
                        $kentta['kuvaus'] .= '</span>';
                    }
                    else if($nimi == 'painotus'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">Arvo numerona';
                        $kentta['kuvaus'] .= ' (kts. <a href="'. site_url('virtuaalihevoset/lajit') . '">lista sallituista arvoista</a>)';
                        $kentta['kuvaus'] .= '</span>';
                    }
                    else if($kentta['type'] == 'select'){
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= '<span class="form_comment">Arvo numerona. ';
                        unset($kentta['options'][-1]);
                        
                        foreach ($kentta['options'] as $id=>$option){
                            $kentta['kuvaus'] .= '"'.$id.'" = ' . $option .', ';
                        }
                         $kentta['kuvaus'] .= '</span>';
                    }
                    
                    else{
                        $kentta['kuvaus'] = $field['label'] ?? "";
                        $kentta['kuvaus'] .= $field['after_html'] ?? "";
                    }
                    $poni[$nimi] = $kentta;
                }
            }
                    
            
            unset($poni['luin_saannot']);
            $data['kentat'] = $poni;
            $data['sivu'] = 'massa';
            $data['allowed'] = $this->hevonen_model->mass_insert_available();
            $data['massarekisterointi'] = $this->load->view('hevoset/massarekisterointi', $data, TRUE);
            $this->fuel->pages->render('hevoset/hevosten_rekisterointi', $data);
        }

    
    }
    
    function rekisterointi_csv(){
        $result = array();
        if(!($this->ion_auth->logged_in()) || !$this->hevonen_model->mass_insert_available())
        {
            	$result['error'] = 1;
                $result['error_message'] = "Virhe! Et ole kirjautunut sisään tai sinulla ei ole oikeutta massarekisteröintiin.";
        }else {
            $result['error'] = 0;
            
            if($this->input->server('REQUEST_METHOD') == 'GET')
            {			        
                $result['error'] = 1;
                $result['error_message'] = "Virhe! Et lähettänyt mitään.";
            }
            else if($this->input->server('REQUEST_METHOD') == 'POST'){
                $msg = "";
                $poni = array();
                
                
                if(!$this->_fill_horse_info_csv($poni, $msg)){
                     $result['error'] = 1;
                    $result['error_message'] = $msg;
                }

                else if (!$this->_validate_horse('new', $poni, $msg)){
                    $result['error'] = 1;
                    $result['error_message'] = $msg;
                    
                }
                else {
                    try{
                        $vh = $this->hevonen_model->add_hevonen($poni, $this->ion_auth->user()->row()->tunnus, $msg);
                     
     
                    if ($vh === false){
                        $result['error'] = 1;
                        $result['error_message'] = $msg;

                    }
                    else {                        
                       $result['error'] = 0;
                       $result['vh'] = $vh;
                    }
                     } catch (Exception $e) {
                             echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
                }
           
            }
            

        }
		$this->load->view('rajapinta/json', array('json'=>json_encode($result)));

    }
    
    //////////////////////////////////////////////////////////////////////////////////////////7
    // Muokkaus
    ////////////////////////////////////////////////////////////////////////////////////////
	
	 function muokkaa($reknro, $sivu='tiedot', $tapa = null, $id = null)
    {
        $this->load->library("vrl_helper");
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));

        $mode = "edit";
        
        if(!$this->_is_editing_allowed($reknro, $msg)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
			return;
		}
        else if($this->user_rights->is_allowed()){
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
            $this->ownership->handle_horse_ownerships($mode, $tapa, $this->ion_auth->user()->row()->tunnus, $id, $this->vrl_helper->vh_to_number($reknro), $data);
			if($mode == 'admin' || $this->ownership->is_horses_main_owner($this->ion_auth->user()->row()->tunnus, $this->vrl_helper->vh_to_number($reknro))){				
								
				$data['form'] = $this->ownership->get_owner_adding_form('virtuaalihevoset/muokkaa/'.$reknro.'/');
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, true, 'virtuaalihevoset/muokkaa/'.$reknro.'/');
			} else {
				$data['ownership'] = $this->ownership->horse_ownerships($reknro, false, 'virtuaalihevoset/muokkaa/'.$reknro.'/');
                $data['delete_owner_url'] = 'virtuaalihevoset/muokkaa/'.$reknro.'/'.$sivu.'/poista/'.$this->ion_auth->user()->row()->tunnus;
			}
            $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

		}

		
		else if ($sivu == 'lopeta'){
			$data['editor'] = $this->_horse_edit_form();
            
        }
        else if($sivu == 'tiedot'){
           if($this->input->server('REQUEST_METHOD') == 'POST'){
                $poni = $this->_fill_horse_info($mode);
                if(!isset($poni['syntymaaika'])){
                    $poni['syntymaaika'] =  $data['hevonen']['syntymaaika'];
                }if(!isset($poni['nimi'])){
                    $poni['nimi'] = $data['hevonen']['nimi'];
                }
                $msg = "";
                if (!$this->_validate_horse($mode, $poni, $msg)){
                    $data['msg'] = "Rekisteröinti epäonnistui! " . $msg;
                    $data['msg_type'] = "danger";
                    $data['editor'] = $this->_get_horse_edit_form($mode, $poni, $data['hevonen']['reknro']);
                    $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);

                }
                else if (!$this->_validate_edits($mode, $poni, $data['hevonen'], $msg)){
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
                        $data['msg'] = "Muokkaus onnistui! " . $msg;
                        $data['msg_type'] = "success";
                        $data['hevonen'] = $this->hevonen_model->get_hevonen_edit($reknro);
                        $data['editor'] = $this->_get_horse_edit_form($mode, $data['hevonen'], $data['hevonen']['reknro']);
                        $this->fuel->pages->render('hevoset/hevonen_muokkaa', $data);
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
        
        $reknro = $this->vrl_helper->vh_to_number($reknro);
		
		//are you admin or editor?
		$this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
		
		//only admin, editor and owner can edit
		if(!($this->ion_auth->is_admin()) && !$this->user_rights->is_allowed()
           && !($this->hevonen_model->is_horse_owner($this->ion_auth->user()->row()->tunnus, $reknro))){
			$msg = "Jos et ole ylläpitäjä, voit muokata vain omia hevosiasi.";
			return false;
		}
		
		//does the horse exist?
		$this->load->library("vrl_helper");
		if(!$this->hevonen_model->onko_tunnus($this->vrl_helper->vh_to_number($reknro))){
			$msg = "Hevosta ei ole olemassa.";
			return false;
		}
		
		return true;		
		
	}
    
     function poista($reknro)
    {
        $reknro = $this->vrl_helper->get_vh($this->vrl_helper->vh_to_number($reknro));
        $this->load->library("vrl_helper");
        $admin = false;
        $msg ="";
        $msg = array('msg_type' => 'danger', 'msg' => "Poisto epäonnistui!");
        $owners = array();
        
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));

        if(!$this->vrl_helper->check_vh_syntax($reknro)){
            $msg['msg'] = "Virheellinen VH-tunnus";
        }
        else if($this->_is_editing_allowed($reknro, $msg['msg'])){
	        if($this->user_rights->is_allowed()){
                $admin = true;
                $owners = $this->hevonen_model->get_horse_owners($reknro);
            }
            //adminin annettava poistolle syy
            if($admin && ($this->input->server('REQUEST_METHOD') != 'POST' || strlen($this->input->post('syy')) == 0)){
                    $this->load->library('form_builder', array('submit_value' => 'Poista'));
                    $fields['syy'] = array('label'=>'Poiston syy', 'type' => 'text', 'class'=>'form-control');                 
                    $this->form_builder->form_attrs = array('method' => 'post');                            
                    $form =  $this->form_builder->render_template('_layouts/basic_form_template', $fields);

                    $this->fuel->pages->render('misc/haku', array("title"=>"Poista hevonen ".$reknro, "form"=>$form));
       
            }        
    
            else if($this->hevonen_model->delete_hevonen($reknro, $msg['msg'], $admin)){
                $msg['msg_type'] = "success";
                $msg['msg'] = "Poisto onnistui!";
                $user = $this->ion_auth->user()->row()->tunnus;
                if($admin){
                    $syy = $this->input->post('syy');
                    foreach($owners as $owner){
                     $this->tunnukset_model->send_message($user, $owner['omistaja'], "Hevosesi " . $reknro . " poistettiin rekisteristä. Syy:  " .$syy);
                    }
                }
                $this->fuel->pages->render('misc/naytaviesti', $msg);

            }else {
            
                $this->fuel->pages->render('misc/naytaviesti', $msg);
            }

        

        } else{
                    $this->fuel->pages->render('misc/naytaviesti', $msg);

        }
        

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
        $country = $this->hevonen_model->get_country($id);
        
        if(sizeof($country) < 1){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Maakoodi on virheellinen.'));

        }
        else {   
            $vars = array();
            $img = site_url() . "/assets/images/flags/48x48/";
            $img = '<img src="'.$img.$country['lyh'].'.png">';
            $vars['title'] = "Syntymämaastatistiikka: " . $country['maa'] . " " . $img;
            $vars['genders'] =  $this->hevonen_model->get_stats_country($id);
            $vars['year_stats'] = $this->_year_stats_table($this->hevonen_model->get_stats_country_year_list($id));

            $this->fuel->pages->render('hevoset/stats', $vars);
        }
		
		
	}
    
    public function lajit() {
        $this->load->model('sport_model');

        $vars['title'] = 'Painotuslajit';			
		$vars['text_view'] = "";
		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'pid', 'key_link' => site_url('virtuaalihevoset/laji/'));
		$vars['headers'][2] = array('title' => 'Painotus', 'key' => 'painotus');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->sport_model->get_sport_list());

		$this->fuel->pages->render('misc/taulukko', $vars);
    }
    
    public function laji($id){
        $this->load->model('sport_model');
        $laji = $this->sport_model->get_sport_info($id);
        
        if(sizeof($laji) < 1){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Lajikoodi on virheellinen.'));

        }
        else {   
            $vars = array();
            $vars['title'] = "Painotuslaji: " . $laji['painotus'];
            $vars['genders'] =  $this->hevonen_model->get_stats_sport($id);
            $vars['year_stats'] = $this->_year_stats_table($this->hevonen_model->get_stats_sport_year_list($id));

            $this->fuel->pages->render('hevoset/stats', $vars);
        }
		
		
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
	
	public function rotu($id = null){
		$vars = array();
        
        if(!isset($id)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Rotukoodi puuttuu.'));

        }else {
            $breed = $this->breed_model->get_breed_info($id);
            
            if(sizeof($breed) < 1){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Rotukoodi on virheellinen.'));

            }else {
                $vars['title'] = "Rotu: " . $breed['rotu'];
        
                $vars['genders'] =  $this->hevonen_model->get_stats_breed($id);
                $vars['puljut'] = $this->breed_model->get_breed_pulju($id, true);
                $vars['year_stats'] = $this->_year_stats_table($this->hevonen_model->get_stats_breed_year_list($id));
                $this->fuel->pages->render('hevoset/stats', $vars);
            }
        }
		
		
	}
	
    private function _year_stats_table($data, $title=null, $text = null){
            $vars['title'] = $title;
	        $vars['text_view'] = $text;
            $vars['headers'][1] = array('title' => 'Vuosi', 'key' => 'year', 'key_link' => site_url('virtuaalihevoset/statistiikka/'));
            $vars['headers'][2] = array('title' => 'Rekisteröityjä hevosia', 'key' => 'amount');
            $vars['headers'] = json_encode($vars['headers']);
                        
            $vars['data'] = json_encode($data);
            return $this->load->view('misc/taulukko', $vars, TRUE);
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
	
	public function vari($id = null){
		$this->load->model("color_model");

		$vars['colour'] = $this->color_model->get_colour_info($id);
        
        if(!isset($id) || sizeof($vars['colour']) < 1){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Värikoodi on virheellinen.'));

        }else {
        $vars['genders'] =  $this->hevonen_model->get_stats_colour($id);

		$vars['title'] = "Väri: " . $vars['colour']['vari'] . " (". $vars['colour']['lyhenne'] . ")";
		
		$vars['gene_lists'] = $this->color_model->get_genes_list($id);	
		
        $vars['year_stats'] = $this->_year_stats_table($this->hevonen_model->get_stats_color_year_list($id));

		
		$vars['other_data'] = array();
		$vars['other_data'][] = $vars['text_view'] = $this->load->view('hevoset/geenit', $vars, TRUE);
		
		$this->fuel->pages->render('hevoset/stats', $vars);
        }


	}
	
	
	public function get_horse_search_form($data = array()){
		$r_options = $this->hevonen_model->get_breed_option_list();
		$r_options[-1] = "Ei väliä";
		$skp_options = $this->hevonen_model->get_gender_option_list();
		$skp_options[-1] = "Ei väliä";
		$color_options = $this->hevonen_model->get_color_option_list();
		$color_options[-1] = "Ei väliä";
		
		$this->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$fields['reknro'] = array('type' => 'text', 'class'=>'form-control', 'value'=>$data['reknro'] ?? "");
		$fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'value'=>$data['nimi'] ?? "");
		$fields['rotu'] = array('type' => 'select', 'options' => $r_options, 'value'=>$data['rotu'] ?? -1, 'class'=>'form-control');
		$fields['sukupuoli'] = array('type' => 'select', 'options' => $skp_options, 'value'=>$data['sukupuoli'] ?? -1, 'class'=>'form-control');
		$fields['kuollut'] = array('type' => 'checkbox', 'checked'=>$data['kuollut'] ?? false, 'class'=>'form-control');
		$fields['vari'] = array('type' => 'select', 'options' => $color_options, 'value'=>$data['vari'] ?? -1, 'class'=>'form-control');
		$fields['syntynyt_v'] = array('type' => 'text', 'label'=>'Syntymävuosi', 'class'=>'form-control', 'value'=>$data['syntynyt_v'] ?? "");
		
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/virtuaalihevoset/haku'));
		        
		return $this->form_builder->render_template('_layouts/basic_form_template', $fields);

	}
	
	
	
	public function validate_horse_search_form(){
        $this->load->library('form_validation');
        
		$this->form_validation->set_rules('sukupuoli', 'Sukupuoli', 'min_length[1]|max_length[2]|numeric');
		$this->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('rotu', 'Rotu', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->form_validation->set_rules('syntynyt_v', 'Syntymävuosi', 'min_length[4]|max_length[4]|numeric');
        $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]");
        return $this->form_validation->run();

    }
    
    
    
    
    
    private function _get_horse_edit_form($type = 'new', $poni = array(), $reknro = null){
        $form = false;
        if($type == "form"){
            $form = true;
            $type = "new";
        }
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
            $fields['nimi'] = array('label'=>'Hevosen nimi', 'type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['nimi'] ?? '',
                                    'after_html' => '<span class="form_comment">Nimen tulee olla hyvien tapojen mukainen, eikä se saa olla jo käytössä samanrotuisella. Sopimattomat nimet sensuroidaan!</span>');
            $fields['sukupuoli'] = array('type' => 'select', 'label'=> 'Sukupuoli', 'options' => $skp_options, 'required' => TRUE, 'value'=> $poni['sukupuoli'] ?? -1, 'class'=>'form-control');
            $fields['rotu'] = array('label'=>'Rotu', 'type' => 'select', 'options' => $r_options, 'required' => TRUE, 'value'=> $poni['rotu'] ?? -1, 'class'=>'form-control');
            $fields['syntymaaika'] = array('type' => 'text', 'label'=>'Syntymäaika', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['syntymaaika'] ?? '', 'after_html' => '<span class="form_comment">Muodossa pp.kk.vvvv</span>');

        }else if( isset($poni['sukupuoli']) && $this->hevonen_model->spayable($poni['sukupuoli'])) {
            
            $skp_options = $this->hevonen_model->get_gender_spayable_option_list();
            $fields['sukupuoli'] = array('type' => 'select', 'label'=> 'Sukupuoli', 'options' => $skp_options, 'required' => TRUE, 'value'=> $poni['sukupuoli'] ?? -1, 'class'=>'form-control');

        }
        $fields['url'] = array('type' => 'text', 'label'=> 'Hevosen sivujen osoite', 'class'=>'form-control', 'required' => TRUE, 'value'=> $poni['url'] ?? 'http://',
                               'after_html' => '<span class="form_comment">Linkki hevosen omalle sivulle, muista http:// alku!</span>');
        $fields['kuollut'] = array('label'=>'Kuollut', 'type' => 'checkbox', 'checked' => $poni['kuollut'] ?? false, 'class'=>'form-control');
        $fields['kuol_pvm'] = array('type' => 'text', 'label'=>'Kuolinpäivä', 'class'=>'form-control', 'value'=> $poni['kuol_pvm'] ?? '',
                                    'after_html' => '<span class="form_comment">Muodossa pp.kk.vvvvv. Jos hevonen on kuollut eikä kuolinpäivää anneta, päiväksi merkataan tämä päivä. </span>'); 
        
        $fields['lisatiedot'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default"><div class="panel-heading">Lisätiedot (ei pakollisia)</div> <div class="panel-body"><div class="form-group">');
        
        $fields['sakakorkeus'] = array('label'=>'Säkäkorkeus', 'type' => 'text', 'class'=>'form-control', 'value'=> $poni['sakakorkeus'] ?? '', 'after_html' => '<span class="form_comment">Säkäkorkeus numeroina (senttimetreinä)</span>');
        $fields['vari'] = array('label'=>'Väri', 'type' => 'select', 'options' => $color_options,  'value'=> $poni['vari'] ?? '-1', 'class'=>'form-control',
                                'after_html' => '<span class="form_comment">Jos toivomasi väri ei löydy listalta, ole yhteydessä ylläpitoon.</span>');
		$fields['painotus'] = array('label'=>'Painotus', 'type' => 'select', 'options' => $skill_options, 'value' =>  $poni['painotus'] ?? -1, 'class'=>'form-control');
        $fields['porr_kilpailee'] = array('label'=>'Kilpailee porrastetuissa', 'type' => 'checkbox', 'checked' => $poni['porr_kilpailee'] ?? false, 'class'=>'form-control');

		$fields['syntymamaa'] = array('label'=>'Syntymämaa', 'type' => 'select', 'options' => $country_options, 'value' => $poni['syntymamaa'] ?? -1, 'class'=>'form-control');
        $this->load->model("Tallit_model");
        $tallilista  = $this->Tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus, false, true);
        
        $tallit = array();
        foreach ($tallilista as $talli){
           $tallit[$talli['tnro']] = $talli['tnro'];
        }

        $option_script = $this->vrl_helper->get_option_script('kotitalli', $tallit);
        $kotitalli_str = '<span class="form_comment">Tallitunnus muodossa XXXX0000.';
        if(!$form){
            $kotitalli_str .= ' Omat tallisi (klikkaa lisätäksesi): ' . $option_script['list'] . '</span>' . $option_script['script'];
        }else{
            $kotitalli_str .= '</span>';
        }

        $fields['kotitalli'] = array('label'=>'Kotitalli', 'type' => 'text', 'class'=>'form-control', 'value'=> $poni['kotitalli'] ?? '',
                                     'after_html'=> $kotitalli_str);

                
               
        $fields['sukutiedot'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default"><div class="panel-heading">Suku- ja kasvattajatiedot (vain suvullisille)</div> <div class="panel-body"><div class="form-group">');
        
        $fields['kasvattajanimi'] = array('type' => 'text', 'label'=> 'Kasvattajanimi', 'class'=>'form-control', 'value'=> $poni['kasvattajanimi'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Jätä tyhjäksi, jos kyseessä evm-hevonen.</span>');
        
        $option_script = $this->vrl_helper->get_option_script('kasvattaja_talli', $tallit);
        
        $kasvattaja_str = '<span class="form_comment">Tallitunnus muodossa XXXX0000.';
        if(!$form){
            $kasvattaja_str .= ' Omat tallisi (klikkaa lisätäksesi): ' . $option_script['list'] . '</span>' . $option_script['script'];
        }else{
            $kasvattaja_str .= '</span>';
        }
        $fields['kasvattaja_talli'] = array('label'=> 'Kasvattajatalli', 'type' => 'text', 'class'=>'form-control', 'value'=> $poni['kasvattaja_talli'] ?? '', 'class'=>'form-control',
                                    'after_html'=> $kasvattaja_str);
        $fields['kasvattaja_tunnus'] = array('label'=>'Kasvattajan VRL-tunnus', 'type' => 'text', 'class'=>'form-control', 'value'=> $poni['kasvattaja_tunnus'] ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Muodossa VRL-00000. Kasvattajan VRL-tunnus. Jätä tyhjäksi, jos kyseessä evm-hevonen.</span>');
        $i = null;
        $e = null;
        
        if(isset($poni['i_nro'])){
            $this->vrl_helper->get_vh($poni['i_nro']);  
        }
        if(isset($poni['e_nro'])){
            $this->vrl_helper->get_vh($poni['e_nro']);  
        }
        $fields['i_nro'] = array('type' => 'text', 'label'=> 'Isän rekisterinumero','class'=>'form-control', 'value'=> $i ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Isän rekisterinumero.</span>');
        $fields['e_nro'] = array('type' => 'text', 'label'=> 'Emän rekisterinumero', 'class'=>'form-control', 'value'=> $e ?? '', 'class'=>'form-control', 'after_html' => '<span class="form_comment">Emän rekisterinumero. </span>');
        
        
        
        $fields['syntymajat'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default syntymaajat">
                                      <div class="panel-heading">Syntymäpäivät (tarpeellisia porrastetuissa kilpaileville)</div> <div class="panel-body"><div class="form-group">');

        
        $this->_horse_birthdays_form_fields($fields, $poni);
        
        
        $fields['end'] = array('type'=>'hidden', 'after_html' => '</div>');
        if($type == 'new' || $type == 'edit'){
            $fields['luin_saannot'] = array('label'=>"Olen lukenut säännöt, ja hevoseni sivuilla lukee selvästi, että kyseessä on virtuaalihevonen!", 'type' => 'checkbox', 'after_html' => '<span class="form_comment">Uusia hevosia valvotaan, ja sääntöjä noudattamattomat voidaan poistaa rekisteristä!</span>', 'class'=>'form-control');
        }
        
        //uusi tai admin
        $submit = array();
        $submit['new'] = array('action' => site_url('/virtuaalihevoset/rekisterointi'), 'submit_value'=>'Rekisteröi');
        $submit['edit'] = array('action' => site_url('/virtuaalihevoset/muokkaa/'.$this->vrl_helper->get_vh($reknro)), 'submit_value'=>'Muokkaa');
        $submit['admin'] = array('action' => site_url('/virtuaalihevoset/muokkaa/'.$this->vrl_helper->get_vh($reknro)), 'submit_value'=>'Muokkaa');

                               
		$this->load->library('form_builder', array('submit_value' => $submit[$type]['submit_value']));
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => $submit[$type]['action']);
		
        if($form){
            return $fields;
        } else {
            return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
        }

	}
    
    private function _horse_birthdays_form_fields(&$fields, $poni = array()){
   
        $vuodet = $this->vuodet;
        foreach($vuodet as $vuosi){
            $value = null;
            if(isset($poni[$vuosi.'vuotta']) && $this->vrl_helper->validateDate($poni[$vuosi.'vuotta'], 'Y-m-d')){
                $value = $this->vrl_helper->sql_date_to_normal($poni[$vuosi.'vuotta']);
            }else if (isset($poni[$vuosi.'vuotta'])){
                $value = $poni[$vuosi.'vuotta'];
            }
            $fields[$vuosi.'vuotta'] = array('type' => 'text', 'label'=>$vuosi. ' vuotta', 'class'=>'form-control',
                                           'required' => FALSE, 'value'=> $value ?? '',
                                           'after_html' => '<span class="form_comment">Muodossa pp.kk.vvvv</span>');
        }
        
        $fields['ikaantyminen_d'] = array('label' => 'Ikääntyminen (päivissä)', 'type' => 'number', 'value' => $poni['ikaantyminen_d'] ?? 0,
                                          'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE,
                                           'after_html' => '<span class="form_comment">Montako päivää hevosen yksi vuosi kestää. Mikäli tämä on asetettu, syntymäpäivät lasketaan tämän perusteella. </span>');    

    }
    
    private function _fill_horse_info_csv(&$poni, &$msg){
        $poni = array();
        
        $headers = $this->input->post('headers');
        $values = $this->input->post('values');
        
        if(!isset($headers) || !isset($values)){
             $msg = "Otsikko- tai arvorivi puuttuu";
            return false;
        }

        
        $headers = str_getcsv($headers , ",","\"");
        $values = str_getcsv($values , ",","\"");
        
        $fields = $this->_get_horse_edit_form('form');
        $poni = array();
        if(sizeof($headers) == sizeof($values)){
            for ($x = 0; $x < sizeof($headers); $x++) {
                if(!isset($fields[$headers[$x]])){
                    $msg = "Otsikkokenttä " . $headers[$x] . " on virheellinen.";
                    return false;
                }
                else if(isset($values[$x]) && strlen($values[$x])>0){
                    $poni[$headers[$x]] = $values[$x];
                }
              }
            $poni['luin_saannot'] = 1;
            
            if(!isset($poni['kuollut'])){
                $poni['kuollut'] = 0;
            }
        }else {
            $msg = "Rekisteröitävällä rivillä ei ole oikeaa määrää kenttiä";
            return false;
            
        }
        return true;
    }
    

    private function _fill_horse_info($type = 'new'){
        $poni = array();
        
        if($type == 'new' || $type == 'admin'){
            $poni['nimi'] = $this->input->post('nimi');
            $poni['syntymaaika'] = $this->input->post('syntymaaika');
        }
        $poni['sukupuoli'] = $this->input->post('sukupuoli');

        $poni['luin_saannot'] = $this->input->post('luin_saannot');
        $poni['url'] = $this->input->post('url');
        $poni['kuollut'] = $this->input->post('kuollut');
        if ($poni['kuollut'] == null){
            $poni['kuollut'] = 0;
        }
        if($poni['kuollut']){
         $poni['kuol_pvm'] = $this->input->post('kuol_pvm');
         
        }else {
            $poni['kuol_pvm'] = null;
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
        if ($this->input->post('porr_kilpailee')){
            $poni['porr_kilpailee'] = $this->input->post('porr_kilpailee');
        }else {
            $poni['porr_kilpailee'] = 0;
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
        
        $vuodet = $this->vuodet;
        foreach($vuodet as $vuosi){
            if($this->input->post($vuosi."vuotta")){
            $poni[$vuosi."vuotta"] = $this->input->post($vuosi."vuotta");
            }else {
                $poni[$vuosi."vuotta"] = null;
            }
        }
        
        if($this->input->post('ikaantyminen_d')){
            $poni['ikaantyminen_d'] = $this->input->post('ikaantyminen_d');
        }
        
        return $poni;
    }
    private function _validate_horse($type, $poni, &$msg){
        $msg = "";
        $this->load->model('Listat_model');
        $this->load->model('tallit_model');
        $genders = $this->hevonen_model->get_gender_option_list();
        if(($type == 'new' || $type == 'edit') && (!isset($poni['luin_saannot']) || $poni['luin_saannot'] == 0)){
            $msg = "Muistathan lukea säännöt ennen hevosen rekisteröintiä!.";
                return false;
        }
        $ok = true;
        if($type == 'new' || $type == 'admin'){ 
            
            if (!isset($poni['rotu']) || !is_numeric($poni['rotu']) || $poni['rotu'] < 1 || $poni['rotu'] > 999 || !$this->Listat_model->breed_exists($poni['rotu'])){
                $msg .= "<li>Rotu on virheellinen.</li>";
                $ok = false;
            }
            
            if (!isset($poni['sukupuoli']) || !isset($genders[$poni['sukupuoli']])){
                    $msg .= "<li>Sukupuoli on virheellinen.</li>";
                    $ok = false;
            }
            if (!isset($poni['syntymaaika']) || !$this->vrl_helper->validateDate($poni['syntymaaika'])){     
                 $msg .= "<li>Syntymäaika on virheellinen.</li>";
                $ok = false;
            }
            if(!(isset($poni['nimi'])) || strlen($poni['nimi']) > 80 || strlen($poni['nimi']) < 2){
                $msg .= "<li>Nimi on pakollinen tieto, ja sen tulee olla min. 2, max. 80 merkkiä pitkä.</li>";
                $ok = false;
            }
            else if(isset($poni['rotu']) && isset($poni['nimi']) && $this->hevonen_model->onko_nimi($poni['nimi'], $poni['rotu']) && $type == 'new'){
               $msg .= "<li>Saman niminen ja rotuinen hevonen on jo rekisterissä.</li>";
                $ok = false;
            }
        }
        
         if (!isset($poni['url']) || strlen($poni['url']) < 12 || !substr( trim($poni['url']), 0, 4 ) === "http") {
                    $msg .= "<li>Osoite on virheellinen (muista http alku!).</li>";
                    $ok = false;
        }

        
        if (isset($poni['painotus']) && (!is_numeric($poni['painotus']) || $poni['painotus'] < 1 || $poni['painotus'] > 99 || !$this->Listat_model->skill_exists($poni['painotus']))){
                $msg .= "<li>Painotusta ei ole olemassa.</li>";
                $ok = false;
        }
        if (isset($poni['vari']) && (!is_numeric($poni['vari']) || $poni['vari'] < 1 || $poni['vari'] > 999 || !$this->Listat_model->colour_exists($poni['vari']))){
                $msg .= "<li>Väri on virheellinen.</li>";
                $ok = false;
        }
        if (isset($poni['sakakorkeus']) && (!is_numeric($poni['sakakorkeus']) || $poni['sakakorkeus'] < 40 || $poni['sakakorkeus'] > 300)){
                $msg .= "<li>Säkäkorkeus on virheellinen.</li>";
                $ok = false;
        }
        if (isset($poni['syntymamaa']) && (!is_numeric($poni['syntymamaa']) || $poni['syntymamaa'] < 1 || $poni['syntymamaa'] > 300 || !$this->Listat_model->country_exists($poni['syntymamaa']))){
            $msg .= "<li>Maakoodia ei ole olemassa.</li>";
            $ok = false;
        }
        if(isset($poni['porr_kilpailee']) && !($poni['porr_kilpailee'] == 1 ||$poni['porr_kilpailee'] == 0) ){
            $msg .= "<li>Virheellinen porrastettujen kilpailutieto.</li>";
            $ok = false;
        }
        if(isset($poni['kuollut']) && !($poni['kuollut'] == 1 ||$poni['kuollut'] == 0) ){
            $msg .= "<li>Virheellinen kuolintieto.</li>";
            $ok = false;
        }
        else if (isset($poni['kuollut']) && $poni['kuollut']){
            if(!isset($poni['kuol_pvm']) || strlen($poni['kuol_pvm'] )== 0){
                $poni['kuol_pvm'] = date('Y-m-d');
            }
            else if(!$this->vrl_helper->validateDate($poni['kuol_pvm'])){     
                $msg .= "<li>Kuolinaika on virheellinen.</li>";
                $ok = false;
            }
        }
        if (!$this->_check_parents($poni, $msg)){
            $ok = false;
        }
        if (isset($poni['kasvattajanimi']) && !empty($poni['kasvattajanimi']) && (strlen($poni['kasvattajanimi']) > 25 ||strpos($poni['nimi'], $poni['kasvattajanimi']) === false)){

            $msg .= "<li>Ilmoittamasi kasvattajanimi on liian pitkä tai se ei ole hevosen nimessä.</li>";
                $ok = false;
        }
        if (isset($poni['kotitalli']) && !empty($poni['kotitalli']) && (strlen($poni['kotitalli']) < 4 || strlen($poni['kotitalli']) > 8 || !$this->tallit_model->is_tnro_in_use($poni['kotitalli']))){
            $msg .= "<li>Kotitallin tunnus on virheellinen</li>";
                $ok = false;
        }
        if (isset($poni['kasvattaja_talli']) && !empty($poni['kasvattaja_talli']) && (strlen($poni['kasvattaja_talli']) < 4 || strlen($poni['kasvattaja_talli']) > 8 ||!$this->tallit_model->is_tnro_in_use($poni['kasvattaja_talli']))){
            $msg .= "<li>Kasvattajan tallitunnus on virheellinen.</li>";
                $ok = false;
        }
        if (isset($poni['kasvattaja_tunnus']) && !empty($poni['kasvattaja_tunnus']) && (
                 !$this->vrl_helper->check_vrl_syntax($poni['kasvattaja_tunnus'])
                 || !$this->tunnukset_model->onko_tunnus($this->vrl_helper->vrl_to_number($poni['kasvattaja_tunnus'])))){
           $msg .= "<li>Kasvattajan VRL-tunnus on virheellinen.</li>";
                $ok = false;
        }
        if(!isset($poni['ikaantyminen_d']) || $poni['ikaantyminen_d'] == 0){
            $vuodet = $this->vuodet;
            foreach($vuodet as $vuosi){
                if(isset($poni[$vuosi."vuotta"]) && !$this->vrl_helper->validateDate($poni[$vuosi."vuotta"])){
                        $msg .= "<li>Porrastettuihin liittyvissä syntymäpäivissä (".$vuosi." vuotta) on virhe.</li>";
                $ok = false;
                    
                }
            }
                        
        }else  if(isset($poni['ikaantyminen_d']) && !is_numeric($poni['ikaantyminen_d'])){
                $msg .= "<li>Ikääntymisen tulee olla numero.</li>";
                $ok = false;
        }
        if(!$this->_check_ages($poni, $msg)){
            $ok = false;
        }
        
        if(!$ok){
            $msg = "<ul>".$msg."</ul>";
        }
        return $ok;
    }
    
    private function _check_ages($poni, &$msg){
        $birth_date = null;
        $dead_date = null;
        
        if(isset($poni['syntymaaika'])){
            $birth_date = new DateTime($poni['syntymaaika']);
        }else {
            //todo hae syntymäaika
             $birth_date = new DateTime('1900-01-01');
        }
        if(isset($poni['kuollut']) && $poni['kuollut'] && isset($poni['kuol_pvm'])){
            $dead_date = new DateTime($poni['kuol_pvm']);
        }
        $current_date = new DateTime();
        
        $ok = true;

        if(isset($dead_date) && $birth_date > $dead_date){
            $msg.="<li>Hevonen on kuollut ennen syntymäänsä!</li>";
            $ok = false;
        }
        
        if(isset($dead_date) && $dead_date > $current_date){
            $msg.="<li>Hevosen kuolinpäivä on tulevaisuudessa!</li>";
           $ok = false;
        }
        
        if ($birth_date > $current_date)
        {
          $msg .= "<LI>Hevosen syntymäpäivä on tulevaisuudessa!</LI>";
          $ok = false;
        
        }
        else if($ok){
            $previous_date = $birth_date;
            
            foreach ($this->vuodet as $vuosi){
                if(isset($poni[$vuosi.'vuotta']) && $this->vrl_helper->validateDate($poni[$vuosi."vuotta"])){
                    
                    $vertailtava = new DateTime($poni[$vuosi.'vuotta']);
                    
                    if($previous_date > $vertailtava){
                        $msg .= "<li>".$vuosi."-vuotissyntymäpäivä on ennen edellistä syntymäpäivää</LI>";
                        $ok = false;
                    }
                    $previous_date = $vertailtava;
                }
            }
            
        }
                    return $ok;

    }
    
    private function _check_parents($poni, &$msg){
        $ok = true;
        if (isset($poni['i_nro'])){
            $ok = $this->_check_parent($poni, $poni['i_nro'], "isä", $msg);
        }
        if (isset($poni['e_nro'])) {
            $ok = $this->_check_parent($poni, $poni['e_nro'], "emä", $msg);
        }
        return $ok;
    }
    
    private function _check_parent($poni, $reknro, $vanhempi, &$msg){
                $ok = true;

        
        if($this->vrl_helper->check_vh_syntax($reknro)){
        
            $parent = $this->hevonen_model->get_hevonen_basic($reknro);
            $poni_date = new DateTime($poni['syntymaaika']);
            $parent_date = $birth_date = new DateTime($parent['syntymaaika']);
            
            
            if($parent_date > $poni_date){
                $msg .= "<li>Hevonen on vanhempi kuin sen " . $vanhempi . "</li>";
                $ok = false;
            }
            if(($vanhempi == "isä" && $parent['sukupuoli'] == 1)|| ($vanhempi == "ema" && $parent['sukupuoli'] != 1 )){
                $msg .= "<li>Hevosen " . $vanhempi . " on väärää sukupuolta!</li>";
                $ok = false;
            }
        }else {
            $msg .= "<li>Hevosen " . $vanhempi . "n rekisterinumero on virheellinen!</li>";
            $ok = false;

        }
        
        return $ok;
    }
    
    private function _validate_edits ($type, &$new, $old, &$msg) {

        $foals = $this->hevonen_model->get_horses_foals($old['reknro']);
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
        
        if(isset($new['kuollut']) && $new['kuollut'] && !$old['kuollut'] ){
            $hevonen['kuol_merkkasi'] = $this->ion_auth->user()->row()->tunnus;
        }
        
        return true;
           
            
    }
	
	
}
?>





