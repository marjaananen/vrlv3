<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kisakeskus extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index($head_data = array())
	{
		
		$data = array();
		$this->load->model('Kisakeskus_model');
		$this->load->library('Age_calc');
		$data['edit_tools'] = false;
		if ($this->ion_auth->is_admin()) {
				$data['edit_tools'] = true;
			}
		$data['kisat'] = $this->Kisakeskus_model->hae_kisat($data['edit_tools']);

		
		foreach ($data['kisat'] as &$kisa){
			$kisa['hilight_vip'] = false;
			$kisa['downhilight_pvm'] = false;
			$kisa['hilight_notnumbered'] = false;
			
			if ($kisa['VRL_kisa_id'] == 0){
				$kisa['hilight_notnumbered'] = true;
			}
			if (date('Ymd') == date('Ymd', strtotime($kisa['vip']))){
				$kisa['hilight_vip'] = true;
			}
			
			else if (date('Ymd') > date('Ymd', strtotime($kisa['vip']))){
				$kisa['downhilight_pvm'] = true;
			}
			$user = $this->ion_auth->user($kisa['user'])->row();
			$kisa['username'] = $user->username;
			$kisa['pvm'] = $this->age_calc->calculateYears($kisa['pvm'], '0');
			$kisa['vip'] = $this->age_calc->calculateYears($kisa['vip'], '0');
			
		}
		$this->tulosta_ulkoasu('kisakeskus/etusivu', $data, $head_data);
		
		
	}
	
	public function stats (){
		$this->load->model('Kisakeskus_model');
		$data = array();
		$data['kisamaara'] = $this->Kisakeskus_model->comp_amount();
		$data['kisamaara_lajit'] = $this->Kisakeskus_model->comp_amount_per();
		$data['luokkamaara'] = $this->Kisakeskus_model->class_amount();
		$data['osallistujamaara'] = $this->Kisakeskus_model->competitor_amount();
		$data['kisaajamaara'] = $this->Kisakeskus_model->uniq_competitor_amount();
		$data['hevosmaara'] = $this->Kisakeskus_model->uniq_horse_amount();
		$data['luokkainfo'] = $this->Kisakeskus_model->class_info();
		$data['luokkainfo_part'] = $this->Kisakeskus_model->class_info_part();
		$data['parhaat_kisaajat'] = $this->Kisakeskus_model->best_competitors();
		$data['parhaat_hevoset'] = $this->Kisakeskus_model->best_horses();
		$this->tulosta_ulkoasu('kisakeskus/statistiikkaa', $data);
	}
	
	
	public function paneeli()
	{
		if ($this->ion_auth->logged_in()){
			$data = array();
			$this->load->model('Kisakeskus_model');
			$this->load->library('Age_calc');
			$data['edit_tools'] = false;
			if ($this->ion_auth->is_admin()) {
				$data['edit_tools'] = true;
			}
			
			$user = $this->ion_auth->user()->row();
			$kisa['username'] = $user->username;
			$data['kisat'] = $this->Kisakeskus_model->hae_omat_kisat($user->id, $data['edit_tools']);
	
			
			foreach ($data['kisat'] as &$kisa){
				$kisa['hilight_vip'] = false;
				$kisa['downhilight_pvm'] = false;
				$kisa['hilight_notnumbered'] = false;
				
				if ($kisa['VRL_kisa_id'] == 0){
					$kisa['hilight_notnumbered'] = true;
				}
				if (date('Ymd') == date('Ymd', strtotime($kisa['vip']))){
					$kisa['hilight_vip'] = true;
				}
				
				else if (date('Ymd') > date('Ymd', strtotime($kisa['vip']))){
					$kisa['downhilight_pvm'] = true;
				}
				$user = $this->ion_auth->user($kisa['user'])->row();
				$kisa['username'] = $user->username;
				$kisa['pvm'] = $this->age_calc->calculateYears($kisa['pvm'], '0');
				$kisa['vip'] = $this->age_calc->calculateYears($kisa['vip'], '0');
				
			}
			$this->tulosta_ulkoasu('kisakeskus/omatkisat', $data);
		}
		
		else {
			redirect('kisakeskus', 'refresh');
		}
	}
	
	
	
	public function k($id = -1, $error = array()){
		
		if ($id == -1){
			
			redirect('kisakeskus', 'refresh');
			
		}
		else {
			$this->load->model('Kisakeskus_model');
			$this->load->library('Age_calc');
			$this->load->helper('form');
			
			$data = $this->Kisakeskus_model->hae_kutsutiedot($id);
			$data['id'] = $id;
			$data['pvm'] = $this->age_calc->calculateYears($data["pvm"], 0);
			$data['vip'] = $this->age_calc->calculateYears($data["vip"], 0);
			$data['error'] = $error;
			$data['vip_sql'] = $data["vip"];
			
			$user = $this->ion_auth->user($data['user'])->row();
			$data['username'] = $user->username;
			$data['user_email'] = $user->email;
			$data['user_vrl'] = $user->vrl_name;
			
			$this->tulosta_ulkoasu('kisakeskus/kutsu', $data);
		}
	}
	
	public function kisaeditori($id = -1, $delete = 0, $sure = -1){
		if ($this->ion_auth->logged_in()){
			$user = $this->ion_auth->user()->row();
			$data = array();
			

						
			if($id == -1)
			{
				$this->load->helper('form');
				$data['id'] = -1;
				$data['pvm'] = "";
				$data['vip'] = "";
				$data['max_os_luokka'] = "100";
				$data['max_hevo_luokka'] = "10";
				$data['max_start_hevo'] = "2";
				$data['porrastettu'] = 1;
				$data['laji'] = 'este';
				$data['VRL_kisa_id'] = "";
				$data['otsikko'] = "";
				$data['talli_url'] = "";
				$data['oma'] = 0;
				$data['talli_nimi'] = "";
				$data['talli_vrl'] = "";
				$data['valitut_luokat'] = array();

				$data['message'] = "";
				$data['message_type'] = 'none';			
				$this->load->model('Kisakeskus_model');
				$data['luokat'] = $this->Kisakeskus_model->hae_luokat();
				
				$this->tulosta_ulkoasu('kisakeskus/lisaa_kisat', $data);

			}
			
			else if ($delete == 1 && $sure == "SURE"){
				$this->load->model('Kisakeskus_model');
				$this->Kisakeskus_model->poista_kutsu($id, $user->id);
				redirect('kisakeskus/paneeli', 'refresh');
				
			}
			else 
			{
				$this->load->helper('form');
				$this->load->model('Kisakeskus_model');
				
				$kutsun_user = $this->Kisakeskus_model->hae_kutsun_user($id);

				if ($kutsun_user === $user->id || $this->ion_auth->is_admin()){
					
					if ($this->ion_auth->is_admin()){
						$user = -1;
					}
					
					else {
						
						$user = $user->id;
					}
					$data = $this->Kisakeskus_model->hae_kutsutiedot($id, $user);
					$data['message'] = "";
					if (sizeof($data) == 0){
						redirect('kisakeskus/paneeli', 'refresh');
					}
					
					else {
						$this->tulosta_ulkoasu('kisakeskus/lisaa_kisat', $data);
					}
				}
				
				else {
					redirect('kisakeskus/paneeli', 'refresh');
				}
			}
		}
		else {
			redirect('kisakeskus', 'refresh');
		}
		
	}
	
	public function lisaa_kutsu(){
		
		if ($this->ion_auth->logged_in()){
			
		
		$user = $this->ion_auth->user()->row();
		
		$this->load->model('Kisakeskus_model');
		$id = $this->input->post('id', TRUE);
		
		if ($id == -1){
		
			$this->load->helper('form');

			$data['pvm'] = $this->input->post('pvm', TRUE);
			$data['vip'] = $this->input->post('vip', TRUE);
			$data['max_os_luokka'] = $this->input->post('max_os_luokka', TRUE);
			$data['max_hevo_luokka'] = $this->input->post('max_hevo_luokka', TRUE);
			$data['max_start_hevo'] = $this->input->post('max_start_hevo', TRUE);
			$data['porrastettu'] = 1;
			
			
			$data['laji'] = 'este';
			$data['VRL_kisa_id'] = ""; //EI VOI OLLA TIEDOSSA
			
			
			$data['otsikko'] = $this->input->post('otsikko', TRUE);
			$data['talli_url'] = $this->input->post('talli_url', TRUE);
			$data['oma'] = 0;
			$data['talli_nimi'] = $this->input->post('talli_nimi', TRUE);
			$data['talli_vrl'] = $this->input->post('talli_vrl', TRUE);
			
			
			$data['user'] = $user->id;
			$data['message'] = "";
			$data['message_type'] = "none";

			
			$luokka_idt = $this->input->post('luokat', TRUE);
			if (empty($luokka_idt)){
				$luokka_idt = array();
			}
			$data['valitut_luokat'] = $luokka_idt;
			
			$this->load->model('Kisakeskus_model');
			//Tarkistelut
			$kaikki_ok = true;
			
			if (empty($luokka_idt)){
				$data['message']= "Kutsussa ei ole yhtäkään luokkaa!";
				$data['message_type']= 'danger';
				$kaikki_ok = false;
			}
			//Tarkista että kaikki luokat on samaa lajia
			
			if ($kaikki_ok){
				$eka = true;
				$edellinen_laji;
				
				foreach ($luokka_idt as $luokka){
					$laji = $this->Kisakeskus_model->hae_luokan_laji($luokka);
					if ($eka){
						$edellinen_laji = $laji;
						$eka = false;
					}
					else if ($edellinen_laji != $laji){
						$kaikki_ok = false;
						$data['message']= "Eri lajin luokkia ei saa olla samassa kutsussa!";
						$data['message_type'] = 'danger';
						break;
					}
				}
				
				
			}
			
			if ($kaikki_ok){
				$data['laji'] = $edellinen_laji;
				$data['message']= "Kutsu tehty, voit tehdä samoilla tiedoilla uuden taikka muokata vähäsen.";
				$data['message_type']= 'success';
				$data['luokat'] = $this->Kisakeskus_model->hae_luokat();
				$this->Kisakeskus_model->lisaa_kutsu($data, $luokka_idt);
				$data['id'] = -1;
				$this->tulosta_ulkoasu('kisakeskus/lisaa_kisat', $data);
			}
			
			else {
				$data['id'] = -1;
				$data['luokat'] = $this->Kisakeskus_model->hae_luokat();
				$this->tulosta_ulkoasu('kisakeskus/lisaa_kisat', $data);
			}
			
						
			
		}
		
		else {
			
			$this->load->helper('form');
			$data['pvm'] = $this->input->post('pvm', TRUE);
			$data['vip'] = $this->input->post('vip', TRUE);
			$data['max_os_luokka'] = $this->input->post('max_os_luokka', TRUE);
			$data['max_hevo_luokka'] = $this->input->post('max_hevo_luokka', TRUE);
			$data['max_start_hevo'] = $this->input->post('max_start_hevo', TRUE);

			$data['VRL_kisa_id'] = $this->input->post('VRL_kisa_id', TRUE);

			
			$data['otsikko'] = $this->input->post('otsikko', TRUE);
			$data['talli_url'] = $this->input->post('talli_url', TRUE);
			$data['talli_nimi'] = $this->input->post('talli_nimi', TRUE);
			$data['talli_vrl'] = $this->input->post('talli_vrl', TRUE);
			
			
			$user = $user->id;
			$data['message'] = "";
			$data['message_type'] = 'none';
			
			$this->load->model('Kisakeskus_model');
			$this->Kisakeskus_model->muokkaa_kutsu($id, $user, $data);
			redirect('kisakeskus/paneeli', 'refresh');
		}
		
		}
		
		else {
			redirect('kisakeskus', 'refresh');
		}
		
	}
	
	
	public function osallistu(){
				
		$id = $this->input->post('id', TRUE);
		$this->load->model('Kisakeskus_model');
		$errors = array();
		
		
		$email_message = "Hei! \nOsallistuit Karus kisakeskuksen kilpailuihin seuraavalla kokoonpanolla: \n\n";
			
			
		$luokat = $this->Kisakeskus_model->hae_kisaluokat($id);
		$luokkanro = 1; 
		//jokainen luokka kerrallaan
		foreach ($luokat as $luokka){
			$osallistujalista = "";
			$osallistujalista = $this->input->post($luokka['kisaluokka_id'], TRUE);
			
			
			
			//jos oli osallistujia...
			if (strlen($osallistujalista) > 0){
				$email_message .= "Luokka " . $luokkanro . "\n";
				$osallistujalista = trim($osallistujalista);
				$osallistujat = explode("\n", $osallistujalista);
				$osallistujat = array_filter($osallistujat, 'trim');
				
				//Jokainen rivi/luokka käydään läpi
				foreach ($osallistujat as $rivi){
					$vh = "";
					$vrl = "";
					$vh_sijainti = 0;
					$vrl_sijainti = 0;
					
					$tunnuksia = preg_match_all('/\VRL\-[0-9]{5}/', $rivi, $vrl_osumat);
					preg_match('/\VRL\-[0-9]{5}/', $rivi, $vrl_osumat);

					//VRL-tunnus-tarkistelu
					if ($tunnuksia == 0){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Ei VRL tunnusta)";
						continue;
						
					}
					
					else if ($tunnuksia > 1){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Useampi kuin yksi VRL-tunnus)";
						continue;
					}
					
					else {						
						$vrl = $vrl_osumat[0];
						$vrl_sijainti = strpos($rivi, $vrl, 0);
					}
					
					//VH-tunnus-tarkistelu
					$tunnuksia = preg_match_all('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $rivi , $osumat);
					$rtunnuksia = preg_match_all('/\VH[0-9]{2}\-028\-[0-9]{4}/', $rivi , $rosumat);
					preg_match('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $rivi , $osumat);
					preg_match('/\VH[0-9]{2}\-028\-[0-9]{4}/', $rivi , $rosumat);
					
					if ($tunnuksia == 0){
						
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Ei VH tunnusta)";
						continue;
					}
					
					else if ($tunnuksia > 1){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Useampi kuin yksi VH-tunnus)";
						continue;
					}
					
					else if($luokka['russeille'] == 1 AND $rtunnuksia == 0){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Luokka on avoin vain rekisteröidyille russeille!)";
						continue;
						
					}
					
					
					else {
						
						$vh = $osumat[0];
						$vh_sijainti = strpos($rivi, $vh, 0);
					}
					
					//Löytyykö tageja
					$tageja = preg_match('/<[^>]*>/', $rivi, $tags);
					if ($tageja != 0){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Osallistumisessa on ylimääräisiä tageja)";
						continue;
						
					}
					
					//Onko VRL-tunnuksessa sulut?
					$sulkumaara = preg_match_all('/\(\VRL\-[0-9]{5}\)/', $rivi, $vrl_sulut);
			
					
					
					if ($vrl_sijainti < 2 // VRL-tunnus ei saa olla alussa
					    OR $vh_sijainti < 15 //Ennen sitä pitää olla vähintään X(VRL-0000)-X verran kirjaimia
					    OR $vrl_sijainti > $vh_sijainti //VRL pitää olla ennen VH:ta
					    OR sizeof($vrl_sulut) == 0){
						
					    $errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Jotain vikaa osallistumismuodossa)";
						continue;
					}
					
					
					
					$this->db->trans_start();
					$osallistumistulo['result'] = false;
					$osallistumistulos = $this->Kisakeskus_model->voi_osallistua($vh, $vrl, $id, $luokka['kisaluokka_id']);
					
					if($osallistumistulos['result'] === true){
						
						$this->Kisakeskus_model->lisaa_osallistuja_luokkaan ($id, $luokka['kisaluokka_id'], $vh, $vrl, $rivi);
						$email_message .= $rivi . "\n";
					}
					
					else {
						
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (" . $osallistumistulos['message'].")";
					}
					$this->db->trans_complete();
				}
				
				
				$email_message .= "\n\n";
			}
			
			
			$luokkanro++;
			
		}
		
		$email = $this->input->post('send_email', TRUE);
		
		if ($email == true){
			
			$email_message .= "Kutsun url: http://karkuranta.marsupieni.net/kisakeskus/k/" . $id . " \n Tämä on kuittaus osallistumisestasi, älä vastaa tähän viestiin! \n\n Karkurannan Ponitalli";
			$address = $this->input->post('email', TRUE);
			
			$this->load->library('email');
			$this->email->from('noreply@karkuranta.marsupieni.net', 'Karus kisakeskus');
			$this->email->to($address); 	
			$this->email->subject('Kilpailukuittaus');
			$this->email->message($email_message);			
			$this->email->send();
		}
		
		$this->k($id, $errors);
	}
	

	
	
	private function tulosta_ulkoasu($view, $data, $head_data = array()){
		
		$this->load->helper('form');
		if (empty($head_data)){
			$head_data['message'] = "";
			$head_data['message_type'] = "none";
			$head_data['logged_in'] = false;

			$head_data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => "",
			);
			$head_data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);
		}
		
		$head_data['logged_in'] = $this->ion_auth->logged_in();
		
		$this->load->view('kisakeskus/header', $head_data);
		$this->load->view($view, $data);
		$this->load->view('kisakeskus/footer');
		
	}
	
		//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('kisakeskus', 'refresh');
	}
	
	function login(){
		
		$this->load->library('ion_auth');
		$this->load->library('form_validation');
		$this->load->helper('url');

		// Load MongoDB library instead of native db driver if required
		$this->config->item('use_mongodb', 'ion_auth') ?
		$this->load->library('mongo_db') :

		$this->load->database();

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
		$this->load->helper('language');
		$head_data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('/kisakeskus', 'refresh');
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('/kisakeskus', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$head_data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$head_data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => $this->form_validation->set_value('identity'),
			);
			$head_data['password'] = array('name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);
			
			$this->index($head_data);

		}
		
	}
	
}
