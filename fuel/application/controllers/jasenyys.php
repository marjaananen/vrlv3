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
        $this->haku();
    }
	
	
	function liity()
    {
		$this->load->library('form_validation');
        $vars['title'] = 'Liity jäseneksi';
        $vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröitymisen jälkeen saat sähköpostilla salasanan ja koodin, jolla aktivoida tunnuksesi. Huomaathan, että ylläpidon tulee tarkastaa hakemuksesi ennen kuin voit kirjautua!';
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Liity', 'required_text' => '*Pakollinen kenttä'));
            $this->load->model('tunnukset_model');
			$options = $this->tunnukset_model->get_location_option_list();

            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'required' => TRUE, 'after_html' => '<span class="form_comment">Nimimerkit eivät ole yksilöllisiä</span>', 'class'=>'form-control');
            $fields['email'] = array('type' => 'text', 'required' => TRUE, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">esimerkki@osoite.fi</span>', 'class'=>'form-control');
            $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'size' => '10', 'value' => '', 'after_html' => '<span class="form_comment">esim. 31.12.1999</span>', 'class'=>'form-control');
            $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'first_option' => 'En halua kertoa', 'after_html' => '<span class="form_comment">Voit halutessasi laittaa iän ja sijainnin näkyväksi rekisteröitymisen jälkeen</span>', 'class'=>'form-control');
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
                $this->form_validation->set_rules('syntymavuosi', 'Syntymäaika', 'min_length[8]|max_length[10]|callback__date_valid');
                $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]|numeric');

				if ($this->form_validation->run() == FALSE)
                {
					$vars['msg'] = "Lomakkeen lähetys epäonnistui!";
					$vars['msg_type'] = "danger";
                }
				else
				{
                    $return_data = $this->tunnukset_model->add_new_application($this->input->post('nimimerkki'), $this->input->post('email'), $this->input->post('syntymavuosi'), $this->input->post('sijainti'));
					
					$to = $this->input->post('email');
					$subject = "Varmista VRL-tunnushakemuksesi!";
					$message = 'Tervetuloa virtuaalisen ratsastajainliiton jäseneksi!\nVarmista lähettämäsi hakemus käymällä seuraavassa osoitteessa:\n\n---------------------------------------\n\n';
					$message = $message . base_url() . 'jasenyys/vahvista/?email=' . $this->input->post('email') . '&code=' . $return_data['varmistus'] . '\n\n---------------------------------------\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net';

					$this->load->library('vrl_email');
					if ($this->vrl_email->send($to, $subject, $message)){
						$vars['msg'] = "Lomakkeen lähetys onnistui!<br />Tarkasta antamasi sähköpostin postilaatikko (jos ei näy, katso roskapostikansio) ja seuraa lähetettyjä ohjeita.";
						$vars['msg_type'] = "success";
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
        
	if(empty($tunnus))
	    redirect('/');
        
	$fields['logged_in'] = $this->ion_auth->logged_in();
	$fields['sivu'] = $sivu;
        
	if(!($this->vrl_helper->check_vrl_syntax($tunnus) && $this->ion_auth->identity_check($this->vrl_helper->vrl_to_number($tunnus))))
	{
	    $this->fuel->pages->render('misc/naytaviesti', array('msg' => 'Profiilia ei löytynyt!'));
	}
	else
	{
            $this->load->model('tunnukset_model');
	    $pinnumber = $this->vrl_helper->vrl_to_number($tunnus);
	    $user = $this->ion_auth->user($this->tunnukset_model->get_users_id($pinnumber))->row();
	    
            $fields['tunnus'] = $this->vrl_helper->get_vrl($tunnus);
	    $fields['nimimerkki'] = $user->nimimerkki;
            $fields['rekisteroitynyt'] = date("d.m.Y", strtotime($user->hyvaksytty));

            if($user->nayta_email == 1)
                $fields['email'] = $user->email;
            else
                $fields['email'] = "Ei saatavilla";            
	    
            if(strtotime($user->syntymavuosi) >= strtotime('-16 year'))
            {
                $fields['syntymavuosi'] = "Ei saatavilla";
                $fields['sijainti'] = "Ei saatavilla";
                $fields['muut_yhteystiedot'] = array();
            }
            else
            {
                $fields['muut_yhteystiedot'] = $this->tunnukset_model->get_users_public_contacts($pinnumber);
                
                if($user->nayta_vuosi == 1)
                    $fields['syntymavuosi'] = date("d.m.Y", strtotime($user->syntymavuosi));
                else
                    $fields['syntymavuosi'] = "Ei saatavilla";
                
                if($user->nayta_laani == 1)
                    $fields['sijainti'] = $this->tunnukset_model->get_location($user->laani);
                else
                    $fields['sijainti'] = "Ei saatavilla";
                    
            }
            
            $fields['nimimerkit'] = $this->tunnukset_model->get_previous_nicknames($pinnumber);
	    
            if($sivu == 'tallit')
            {
                $this->load->model('tallit_model');
                $fields['stables'] = $this->tallit_model->get_users_stables($pinnumber);
            }
            
	    $this->fuel->pages->render('jasenyys/tunnus', $fields);
	}
    }
    

    
    function vahvista()
    {
        $this->load->model('tunnukset_model');
        
        $email = $this->input->get('email', TRUE);
        $code = $this->input->get('code', TRUE);
        
        if($email != false && $code != false && $this->tunnukset_model->validate_application($email, $code) == true)
            $vars['msg'] = "Sähköpostiosoitteesi vahvistaminen onnistui!<br /><br />Hakemuksesi siirtyy nyt tunnusjonoon, josta VRL:n työntekijä hyväksyy sen.<br />Saat tämän jälkeen sähköpostilla tunnuksen ja salasanan, joilla pääset kirjautumaan sisään.";
        else
            $vars['msg'] = "Jotain meni pieleen!<br /><br />Varmista, ettei sähköpostiisi tullut osoite katkennut osoitepalkille siirrettäessä ja yritä uudelleen.";
            
        $this->fuel->pages->render('misc/naytaviesti', $vars);
    }
    
    function haku()
    {
    $this->load->library('Vrl_helper');
	$this->load->model('tunnukset_model');
	$this->load->library('form_validation');
	$this->load->library('form_builder', array('submit_value' => 'Hae'));
	$vars['title'] = 'Jäsenhaku';
	$vars['msg'] = 'Hae VRL:n jäseniä. Voit käyttää tähteä * jokerimerkkinä. Sijainnin perusteella tehdyssä haussa näkyvät vain yli 16-vuotiaat jäsenet, jotka ovat merkinneet sijaintinsa näkyväksi kaikille.';
	
	$options = $this->tunnukset_model->get_location_option_list();
	$options[-1] = 'Mikä tahansa';
	
	$fields['tunnus'] = array('type' => 'text', 'label' => 'VRL-tunnus', 'class'=>'form-control');
	$fields['nimimerkki'] = array('type' => 'text', 'class'=>'form-control');
	$fields['sijainti'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');

	$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/jasenyys/haku'));
	$vars['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
	
	if($this->input->server('REQUEST_METHOD') == 'POST')
	{
        $this->form_validation->set_rules('tunnus', 'VRL-tunnus', "min_length[5]|max_length[9]|regex_match[/^[VRL*\-0-9]*$/]");
	    $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
	    $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]');

	    if($this->form_validation->run() == true && !(empty($this->input->post('tunnus')) && empty($this->input->post('nimimerkki')) && $this->input->post('sijainti') == "-1"))
	    {
		$vars['headers'][1] = array('title' => 'VRL-tunnus', 'key' => 'tunnus', 'profile_link' => site_url('tunnus/') . '/', 'prepend_text' => 'VRL-');
		$vars['headers'][2] = array('title' => 'Nimimerkki', 'key' => 'nimimerkki');
		$vars['headers'][3] = array('title' => 'Ikä', 'key' => 'syntymavuosi', 'date_to_age' => true);
		$vars['headers'][4] = array('title' => 'Sijainti', 'key' => 'maakunta');
		$vars['headers'] = json_encode($vars['headers']);
                
                $vars['data'] = $this->tunnukset_model->search_users($this->vrl_helper->vrl_to_number($this->input->post('tunnus')), $this->input->post('nimimerkki'), $this->input->post('sijainti'));
                
                foreach($vars['data'] as $key => $data)
                {
                    //jos piilotettu tai alle 16v niin ei lähetetä oikeita tietoja
                    if(intval(substr(date('Ymd') - date('Ymd', strtotime($data['syntymavuosi'])), 0, -4)) < 16)
                    {
                        $vars['data'][$key]['syntymavuosi'] = '0000-00-00';
                        $vars['data'][$key]['maakunta'] = 'Ei saatavilla';
                    }
                    else
                    {
                        if($data['nayta_vuosi'] == 0)
                            $vars['data'][$key]['syntymavuosi'] = '0000-00-00';
                            
                        if($data['nayta_laani'] == 0)
                            $vars['data'][$key]['maakunta'] = 'Ei saatavilla';
                    }
                }
                
		$vars['data'] = json_encode($vars['data']);		
	    }
	}
	
	$this->fuel->pages->render('misc/haku', $vars);
    }
    
    function _date_valid($date)
    {
        if($date == '' || preg_match('/^(?:(?:31(\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date) == 1)
            return true;
        else
            return false;
    }
}
?>