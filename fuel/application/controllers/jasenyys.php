<?php
class Jasenyys extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function liity()
    {
	$this->load->library('form_validation');
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Liity', 'required_text' => '*Pakollinen kenttä'));
            
            $options = array('1' => 'Ahvenanmaa', '2' => 'Etelä-Karjala', '3' => 'Etelä-Pohjanmaa', '4' => 'Etelä-Savo', '5' => 'Itä-Uusimaa', '6' => 'Kainuu', '7' => 'Kanta-Häme', '8' => 'Keski-Pohjanmaa',
                             '9' => 'Keski-Suomi', '10' => 'Kymenlaakso', '11' => 'Lappi', '12' => 'Pirkanmaa', '13' => 'Pohjanmaa', '14' => 'Pohjois-Karjala', '15' => 'Pohjois-Pohjanmaa',
                             '16' => 'Pohjois-Savo', '17' => 'Päijät-Häme', '18' => 'Satakunta', '19' => 'Uusimaa', '20' => 'Varsinais-Suomi', '21' => 'Ulkomaat');
             
            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'required' => TRUE, 'after_html' => '<span class="form_comment">Nimimerkit eivät ole yksilöllisiä</span>');
            $fields['email'] = array('type' => 'text', 'required' => TRUE, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">esimerkki@osoite.fi</span>');
            $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'size' => '10', 'value' => 'pp.kk.vvvv', 'after_html' => '<span class="form_comment">esim. 31.12.1999</span>');
            $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'first_option' => 'En halua kertoa', 'after_html' => '<span class="form_comment">Voit halutessasi laittaa iän ja sijainnin näkyväksi rekisteröitymisen jälkeen</span>');
            $fields['roskapostitarkastus'] = array('type' => 'number', 'required' => TRUE, 'after_html' => '<span class="form_comment">Montako kaviota hevosella on? Numerona.</span>');
            
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/jasenyys/liity'));
    
                            
            // set the fields
            $this->form_builder->set_fields($fields);
            // render the page
            $vars['join_form'] = $this->form_builder->render();
            
            $this->fuel->pages->render('jasenyys/liity', $vars);
        }
        else if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            if($this->input->post('roskapostitarkastus') == '4')
            {
		$this->load->helper(array('form', 'url'));
                $this->load->model('tunnukset_model');
                $this->load->library('email');
                
                $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "required|min_length[1]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
                $this->form_validation->set_rules('email', 'Sähköpostiosoite', 'required|valid_email|is_unique[vrlv3_tunnukset.email]|is_unique[vrlv3_tunnukset_jonossa.email]');
                $this->form_validation->set_rules('syntymavuosi', 'Syntymäaika', 'min_length[8]|max_length[10]|callback__date_valid');
                $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]|numeric');

		if ($this->form_validation->run() == FALSE)
                {
		    $vars['join_msg'] = "Lomakkeen lähetys epäonnistui!";
		    $vars['join_msg_type'] = "danger";
                }
		else
		{
		    $vars['join_msg'] = "Lomakkeen lähetys onnistui!<br />Tarkasta antamasi sähköpostin postilaatikko (jos ei näy, katso roskapostikansio) ja seuraa lähetettyjä ohjeita.";
                    
                    $return_data = $this->tunnukset_model->add_new($this->input->post('nimimerkki'), $this->input->post('email'), $this->input->post('syntymavuosi'), $this->input->post('sijainti'));
                    
                    $this->email->from('jasenyys@virtuaalihevoset.net', 'Jäsenyyskone');
                    $this->email->to($this->input->post('email')); 
                    
                    $this->email->subject('Varmista VRL-tunnushakemuksesi!');
                    $this->email->message('Tervetuloa virtuaalisen ratsastajainliiton jäseneksi!\nVarmista lähettämäsi hakemus käymällä seuraavassa osoitteessa:\n\n---------------------------------------\n\nhttp://www.virtuaalihevoset.net/jasenyys/vahvista/?email=' . $this->input->post('email') . '&code=' . $return_data['varmistus'] . '\n\n---------------------------------------\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net');
                    
                    //$this->email->send();
                    //TESTAAMATON --^
		}
            }
            else
            {
                $vars['join_msg'] = "Roskapostitarkastus epäonnistui. Olet botti.";
		$vars['join_msg_type'] = "danger";
            }
            
            $this->fuel->pages->render('jasenyys/liity', $vars);
        }
        else
            redirect('/', 'refresh');
    }
    
    function vahvista()
    {
        $this->load->model('tunnukset_model');
        
        $email = $this->input->get('email', TRUE);
        $code = $this->input->get('code', TRUE);
        
        if($email != false && $code != false && $this->tunnukset_model->validate($email, $code) == true)
            $vars['msg'] = "Sähköpostiosoitteesi vahvistaminen onnistui!<br /><br />Hakemuksesi siirtyy nyt tunnusjonoon, josta VRL:n työntekijä hyväksyy sen.<br />Saat tämän jälkeen sähköpostilla tunnuksen ja salasanan, joilla pääset kirjautumaan sisään.";
        else
            $vars['msg'] = "Jotain meni pieleen!<br /><br />Varmista, ettei sähköpostiisi tullut osoite katkennut osoitepalkille siirrettäessä ja yritä uudelleen.";
            
        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    
    function _date_valid($date)
    {
        if(preg_match('/^(?:(?:31(\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date) == 1)
            return true;
        else
            return false;
    }
}
?>