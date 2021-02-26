<?php
class Jasenyys extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
	
	function rekisteriseloste()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('jasenyys/rekisteriseloste', $vars);
    }
    

	
	function index()
    {
            echo 'Current PHP version: ' . phpversion();

       // var_dump(date("Y-m-d H:i:s", NOW()));
       
        $this->haku();
    }
	
	
	function liity()
    {
		$this->load->library('form_validation');
        $vars['title'] = 'Liity jäseneksi';
        $vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröitymisen jälkeen saat sähköpostilla salasanan ja koodin, jolla aktivoida hakemuksesi. Huomaathan, että ylläpidon tulee tarkastaa hakemuksesi ennen kuin voit kirjautua!';
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Liity', 'required_text' => '*Pakollinen kenttä'));
            $this->load->model('tunnukset_model');

            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'required' => TRUE, 'after_html' => '<span class="form_comment">Nimimerkit eivät ole yksilöllisiä</span>', 'class'=>'form-control');
            $fields['email'] = array('type' => 'text', 'required' => TRUE, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">esimerkki@osoite.fi</span>', 'class'=>'form-control');
            $fields['roskapostitarkastus'] = array('type' => 'number', 'required' => TRUE, 'after_html' => '<span class="form_comment">Montako kaviota hevosella on? Numerona.</span>', 'class'=>'form-control');
            
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/jasenyys/liity'));
    
            // render the page
            $vars['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
            
            $this->fuel->pages->render('misc/jonorekisterointi', $vars);
        }
        else if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            if($this->input->post('roskapostitarkastus') !='4')
            {
				$vars['msg'] = "Roskapostitarkastus epäonnistui. Olet botti.";
				$vars['msg_type'] = "danger";
			}
			else {
				$this->load->helper(array('form', 'url'));
                $this->load->model('tunnukset_model');
                $this->load->library('email');
                
                $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "required|min_length[1]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
                $this->form_validation->set_rules('email', 'Sähköpostiosoite', 'required|valid_email|is_unique[vrlv3_tunnukset.email]|is_unique[vrlv3_tunnukset_jonossa.email]');


				if ($this->form_validation->run() == FALSE)
                {
					$vars['msg'] = "Lomakkeen lähetys epäonnistui!";
					$vars['msg_type'] = "danger";
                }
				else
				{
                    $return_data = $this->tunnukset_model->add_new_application($this->input->post('nimimerkki'), $this->input->post('email'));
					
					$to = $this->input->post('email');
                    $data = array();
                    $data['varmistus'] = $return_data['varmistus'];
                    $data['email'] = $this->input->post('email');
					$subject = "Varmista VRL-tunnushakemuksesi!";
					$message = $this->load->view('email/tunnus_hakemus', $data, TRUE);

					$this->load->library('vrl_email');
					if ($this->vrl_email->send($to, $subject, $message)){
						$vars['msg'] = "Lomakkeen lähetys onnistui!<br />Tarkasta antamasi sähköpostin postilaatikko (jos ei näy, katso roskapostikansio) ja seuraa lähetettyjä ohjeita.";
						$vars['msg_type'] = "success";
                        

                                                
					}else {
                        $vars['msg'] = "Lomakkeen lähetys epäonnistui, sähköpostin lähetys antamaasi osoitteeseen ei onnistunut!";
                        $vars['msg_type'] = 'danger';
                    }
					//What if sending fails?
				}
            }
            
            $this->fuel->pages->render('misc/jonorekisterointi', $vars);
        }
        else
            redirect('/', 'refresh');
    }

    function tunnus($tunnus="", $sivu = "")
    {
        $this->load->library('Vrl_helper');
        $fields = array();
        $fields['logged_in'] = $this->ion_auth->logged_in();
        $fields['admin'] = false;
        $fields['oma'] = false;
        $fields['sivu'] = $sivu;

        $logged_user = array();
        
        if($fields['logged_in']){
        
            $logged_user = $this->ion_auth->user()->row();

            if(empty($tunnus)){
                $tunnus = $logged_user->tunnus;
            }
          
            $this->load->library('user_rights', array('groups' => array('admin', 'tunnukset', 'hevosrekisteri', 'tallirekisteri')));
                
            $fields['admin'] = $this->user_rights->is_allowed();
                
            $fields['oma'] =  $logged_user->tunnus == $this->vrl_helper->vrl_to_number($tunnus);

        } 
        
        if(empty($tunnus) || !($this->vrl_helper->check_vrl_syntax($tunnus) && $this->ion_auth->identity_check($this->vrl_helper->vrl_to_number($tunnus))))
        {
            $this->fuel->pages->render('misc/naytaviesti', array('msg' => 'Hakemaasi tunnusta ei löytynyt!'));
        }
        else
        {
            $this->load->model('tunnukset_model');
            $pinnumber = $this->vrl_helper->vrl_to_number($tunnus);
            $user = null;
            if($fields['oma']){
                $user = $logged_user;
                $fields['url'] = 'profiili/tunnus/';
            }
            else {
                $fields['url'] = "tunnus/";
                $user = $this->ion_auth->user($this->tunnukset_model->get_users_id($pinnumber))->row();
            }
            
            $fields['tunnus'] = $this->vrl_helper->get_vrl($tunnus);
            $fields['nimimerkki'] = $user->nimimerkki;
            $fields['kuvaus'] = $user->kuvaus;

            $fields['rekisteroitynyt'] = $this->vrl_helper->sanitize_registration_date($user->hyvaksytty);
    
            $fields['email'] = "Ei saatavilla.";
            
            
            if($user->nayta_email == 1){
                if (!$fields['logged_in']){
                      $fields['email'] = "Kirjaudu sisään nähdäksesi!";
                }else {
                     $fields['email'] = $user->email;
                }
            } else {
                 if($fields['oma'] || $fields['admin']){
                    $fields['email'] = $user->email;
                    $fields['email'] .= " (Näytetään vain käyttäjälle itselleen ja ylläpidolle)";
                 }
            }         
        
            
            if($fields['logged_in']){
                $fields['muut_yhteystiedot'] = $this->tunnukset_model->get_users_public_contacts($pinnumber);        
                $fields['nimimerkit'] = $this->tunnukset_model->get_previous_nicknames($pinnumber);
                
                 if($sivu == 'kasvatit'){				
                        $fields['foals'] = $this->_omat_kasvatit($pinnumber, $fields['oma'], $fields['admin'] );
                    }
                
                    
                }
                else {
                    $fields['foals'] = "Kirjaudu sisään nähdäksesi tiedot.";
                    $fields['names'] = "Kirjaudu sisään nähdäksesi tiedot.";
                    
                }
                if($sivu == 'hevoset'){				
                        $fields['horses'] = $this->_omat_hevoset($pinnumber, $fields['oma'], $fields['admin']);
                    }
                else if($sivu == 'tallit'){
                     $fields['stables'] = $this->_omat_tallit($pinnumber, $fields['oma'], $fields['admin']);
                }
                else if($sivu=="vastuut"){
                    $fields['vastuut'] = $this->_omat_vastuut($user, $fields['oma'] || $fields['admin']);
                } else if($sivu == 'kasvattajanimet'){				
                    $fields['names'] = $this->_omat_kasvattajanimet($pinnumber);
                    }
                
                
            $this->fuel->pages->render('jasenyys/tunnus', $fields);
        
        }
    }
        
    
        
    function vahvista()
        {
            $this->load->model('tunnukset_model');
            
            $email = $this->input->get('email', TRUE);
            $code = $this->input->get('code', TRUE);
            
            if($email != false && $code != false && $this->tunnukset_model->validate_application($email, $code) == true){
                $vars['msg'] = "Sähköpostiosoitteesi vahvistaminen onnistui!<br /><br />Hakemuksesi siirtyy nyt tunnusjonoon, josta VRL:n työntekijä hyväksyy sen.<br />
                Saat tämän jälkeen sähköpostilla linkin jonka kautta pääset asettamaan tunnuksen ja salasanan.";
                $vars['msg_type'] = 'success';
                $this->load->model("oikeudet_model");
                $admins = $this->oikeudet_model->users_in_group_name('tunnukset');
                $sent = 0; //just for safety
                $this->load->library('vrl_email');
                foreach($admins as $admin){
                    if($sent > 5){
                        break; //safety
                    }
                    $to = $admin['email'];
                    $subject = "VRL-tunnusjonossa on uusi hakemus!";
                    $message = $this->load->view('email/tunnus_jonossa', array(), TRUE);
                    $this->vrl_email->send($to, $subject, $message);
                    $sent = $sent++;
                }

            
            }
            else {
                $vars['msg'] = "Jotain meni pieleen!<br /><br />Varmista, ettei sähköpostiisi tullut osoite katkennut osoitepalkille siirrettäessä, tai että et ole jo vahvistanut hakemustasi!.";
            $vars['msg_type'] = 'danger';
            }   
        $this->fuel->pages->render('misc/naytaviesti', $vars);
    }
    
    function haku()
    {
    $this->load->library('Vrl_helper');
	$this->load->model('tunnukset_model');
	$this->load->library('form_validation');
	$this->load->library('form_builder', array('submit_value' => 'Hae'));
	$data['title'] = 'Jäsenhaku';
	$data['msg'] = 'Hae VRL:n jäseniä. Voit käyttää tähteä * jokerimerkkinä.';
           $hakudata = array();

 	
	if($this->input->server('REQUEST_METHOD') == 'POST')
	{
        $this->form_validation->set_rules('tunnus', 'VRL-tunnus', "min_length[5]|max_length[9]|regex_match[/^[VRL*\-0-9]*$/]");
	    $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "min_length[2]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
        $this->form_validation->set_rules('email', 'Email', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");


	    if($this->form_validation->run() == true)
	    {
			$vars['headers'][1] = array('title' => 'VRL-tunnus', 'key' => 'tunnus', 'key_link' => site_url('tunnus/') . '/', 'prepend_text' => 'VRL-');
			$vars['headers'][2] = array('title' => 'Nimimerkki', 'key' => 'nimimerkki');
            $vars['headers'][3] = array('title' => 'Email', 'key' => 'email');
          
			$vars['headers'] = json_encode($vars['headers']);
            
            $tunnus = null;
            $nick = null;
            $email = null;
            
            $admin = false;
            
            $allowed_user_groups = array('admin', 'tunnukset');
            
            $this->load->library('user_rights', array('groups' => $allowed_user_groups));
            if ($this->user_rights->is_allowed()){       
                $admin = true;
            }
            
            if (!empty($this->input->post('tunnus'))){
                $hakudata['tunnus'] = $this->input->post('tunnus');
                $valitunnus = $this->vrl_helper->vrl_to_number($this->input->post('tunnus'));
                if($valitunnus == -1){
                    $tunnus = preg_replace('/[^0-9*]/', "", $this->input->post('tunnus'));
                }else {
                    $tunnus = $valitunnus;
                }
            }
            if (!empty($this->input->post('nimimerkki'))){
                $hakudata['nimimerkki'] = $this->input->post('nimimerkki');
                $nick = $this->input->post('nimimerkki');
            }
            if (!empty($this->input->post('email'))){
                $hakudata['email'] = $this->input->post('email');
                $email = $this->input->post('email');
            }
            $vars['data'] = $this->tunnukset_model->search_users($tunnus, $nick, $email, $admin);         
			$vars['data'] = json_encode($vars['data']);
			
			$data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);

	    }
	}
    
    
	
	
	$fields['tunnus'] = array('type' => 'text', 'label' => 'VRL-tunnus', 'class'=>'form-control', 'value'=>$hakudata['tunnus'] ?? "");
	$fields['nimimerkki'] = array('type' => 'text', 'class'=>'form-control', 'value'=>$hakudata['nimimerkki'] ?? "");
    $fields['email'] = array('type' => 'text', 'value'=>$hakudata['email'] ?? "", 'after_html' => '<span class="form_comment"> Hakee oletuksena vain julkiseksi asetetuista sähköposteista. Ylläpidon tekemät haut hakevat kaikista.</span>', 'class'=>'form-control');
            


	$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/jasenyys/haku'));
	$data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);

	
	$this->fuel->pages->render('misc/haku', $data);
    }
    
    function _date_valid($date)
    {
        if($date == '' || preg_match('/^(?:(?:31(\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date) == 1)
            return true;
        else
            return false;
    }
	
	
	
	
	
	// Tunnussivun matskut
		private function _omat_tallit($nro, $oma = false, $admin = false){
			
			$this->load->model('tallit_model');	
				
			$vars['title'] = '';
            
            $vars['headers'][1] = array('title' => 'Perustettu', 'key' => 'perustettu', 'type' => 'date');
			$vars['headers'][2] = array('title' => 'Tallinumero', 'key' => 'tnro', 'key_link' => site_url('tallit/talli/'));
			$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
            $vars['headers'][4] = array('title' => 'Kategoria', 'key' => 'katelyh', 'aggregated_by' => 'tnro');
            $vars['headers'][5] = array('title' => 'Kuvaus', 'key' => 'kuvaus', 'type'=>'small');
			
            if($oma || $admin){
                $vars['text_view'] = $this->load->view('misc/naytaviesti', array("msg"=>"Lopettaneet tallit näkyvät tässä vain ylläpidolle ja käyttäjälle itselleen."), TRUE);
                $vars['headers'][6] = array('title' => 'Lopettanut', 'key' => 'lopettanut', 'type'=>'bool');

            }

			$vars['headers'] = json_encode($vars['headers']);
                        
			
			$stables =  $this->tallit_model->get_users_stables($nro, true, !($oma||$admin));		
			$vars['data'] = json_encode($stables);
			
			return $this->load->view('misc/taulukko', $vars, TRUE);
	
	}
	
	
	
	
	private function _omat_hevoset($nro, $oma = false, $admin = false)
    {
		$this->load->model('hevonen_model');
		$vars['title'] = "";
		
		$vars['msg'] = '';
		
	
		
        $vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
        $vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
        $vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
        $vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
        $vars['headers'][5] = array('title' => 'Syntymäaika', 'key' => 'syntymaaika', 'type'=>'date');

        
        if($oma || $admin){
            $vars['text_view'] = $this->load->view('misc/naytaviesti', array("msg"=>"Kuolleet hevoset näkyvät tässä vain ylläpidolle ja käyttäjälle itselleen."), TRUE);
            $vars['headers'][6] = array('title' => 'Kuollut', 'key' => 'kuollut', 'type'=>'bool');

        }

        
        
        $vars['headers'] = json_encode($vars['headers']);
                    
        $vars['data'] = json_encode($this->hevonen_model->get_owners_horses($nro, !($oma||$admin)));
		
		
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
	
	
	private function _omat_kasvatit($nro, $oma, $admin)
    {
				$this->load->model('hevonen_model');

		$vars['title'] = "";
		
		$vars['msg'] = '';
        
        
		
		$vars['text_view'] = $this->load->view('misc/naytaviesti', array("msg"=>"Lopettaneiden tallien kasvatit näkyvät tässä vain ylläpidolle ja käyttäjälle itselleen."));		
	
	
		
			$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'), 'type'=>'VH');
			$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
            $vars['headers'][3] = array('title' => 'Syntymäaika', 'key' => 'syntymaaika', 'type'=>'date');
			$vars['headers'][4] = array('title' => 'Rotu', 'key' => 'rotu');
			$vars['headers'][5] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
            $vars['headers'][6] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');
            $vars['headers'][7] = array('title' => 'Kasvattajan tunnus', 'key' => 'kasvattaja_tunnus', 'key_link' => site_url('tunnus/'));
            $vars['headers'][8] = array('title' => 'Kasvattajatalli', 'key' => 'kasvattaja_talli', 'key_link' => site_url('virtuaalitallit/talli/'));
			
			$vars['headers'] = json_encode($vars['headers']);
						
			$vars['data'] = json_encode($this->hevonen_model->get_users_foals_full($nro, !($oma||$admin)));
		
		
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
	
	private function _omat_kasvattajanimet($nro){
				$this->load->model('kasvattajanimi_model');

	
		$vars['title'] = "";
		
		$vars['msg'] = '';
		
		$vars['text_view'] = "";		
						
			$vars['headers'][1] = array('title' => 'Kasvattajanimi', 'key' => 'kasvattajanimi');

			$vars['headers'][2] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/nimi/'));
			$vars['headers'][3] = array('title' => 'Rodut', 'key' => 'lyhenne', 'aggregated_by' => 'id');
			$vars['headers'][4] = array('title' => 'Rekisteröity', 'key' => 'rekisteroity', 'type' => 'date');
			//$vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			//$vars['headers'][6] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url('kasvatus/kasvattajanimet/poista/'), 'image' => site_url('assets/images/icons/delete.png'));

			$vars['headers'] = json_encode($vars['headers']);
				
			$vars['data'] = json_encode($this->kasvattajanimi_model->get_users_names($nro));
			
	
		return $this->load->view('misc/taulukko', $vars, TRUE);
    }
    
    
    private function _omat_vastuut($user, $oma){
        $this->load->model("oikeudet_model");
        $this->load->model("jaos_model");

        $vars['stats'] = array();
        $vars['stats']['jaokset'] = $this->jaos_model->get_users_jaos($user->tunnus);
        $vars['stats']['puljut'] = $this->jaos_model->get_users_pulju($user->tunnus);
                
        $id = $user->id;
        
        $groups = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result_array();
        $vars['vastuut'] = $this->oikeudet_model->sort_users_privileges($groups, $currentGroups);
    
        $vars['oma'] =  $oma;
        
        return $this->load->view('jasenyys/vastuut', $vars, TRUE);
    }
    
    
    
	
}
?>