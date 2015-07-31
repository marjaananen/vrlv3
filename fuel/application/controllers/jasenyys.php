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
            $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'size' => '10', 'value' => 'vvvv-kk-pp', 'after_html' => '<span class="form_comment">esim. 1999-12-31</span>');
            $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'first_option' => 'En halua kertoa', 'after_html' => '<span class="form_comment">Voit halutessasi laittaa iän ja sijainnin näkyväksi rekisteröitymisen jälkeen</span>');
            $fields['roskapostitarkastus'] = array('type' => 'number', 'required' => TRUE, 'after_html' => '<span class="form_comment">Montako kaviota hevosella on? Numerona.</span>');
            
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/jasenyys/liity'));
    
                            
            // set the fields
            $this->form_builder->set_fields($fields);
            // render the page
            $vars['join_form'] = $this->form_builder->render();
            
            $this->fuel->pages->render('jasenyys/liity', $vars);
        }
        else
        {
            if($this->input->post('roskapostitarkastus') == '4')
            {
		$this->load->helper(array('form', 'url'));
                $this->load->model('tunnukset_jonossa_model');
                $this->load->library('email');
                
                $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "required|min_length[1]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
                $this->form_validation->set_rules('email', 'Sähköpostiosoite', 'required|valid_email|is_unique[vrlv3_tunnukset.email]');
                $this->form_validation->set_rules('syntymavuosi', 'Syntymäaika', 'regex_match[/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/]'); //validointi ei ole valmis, ja kentän formaattikin muuttuu vielä suomalaiseen muotoon
                $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]|numeric');

		if ($this->form_validation->run() == FALSE){
		    $vars['join_msg'] = "Lomakkeen lähetys epäonnistui!";
		    $vars['join_msg_type'] = "danger";
		}
		else
		{
		    $vars['join_msg'] = "Lomakkeen lähetys onnistui!<br />Tarkasta antamasi sähköpostin postilaatikko (jos ei näy, katso roskapostikansio) ja seuraa lähetettyjä ohjeita.";
                    $vars['join_msg_type'] = "success";
                    $return_data = $this->tunnukset_jonossa_model->add_new($this->input->post('nimimerkki'), $this->input->post('email'), $this->input->post('syntymavuosi'), $this->input->post('sijainti'));
                    
                    $this->email->from('jasenyys@virtuaalihevoset.net', 'Jäsenyyskone');
                    $this->email->to($this->input->post('email')); 
                    
                    $this->email->subject('Varmista VRL-tunnushakemuksesi!');
                    $this->email->message('Tervetuloa virtuaalisen ratsastajainliiton jäseneksi!\nVarmista lähettämäsi hakemus alla olevalla koodilla osoitteessa:\n\nhttp://www.virtuaalihevoset.net/jasenyys/vahvista\n\n---------------------------------------\n\nSähköpostiosoite: glowie@gmail.com\n\nVahvistuskoodi: aabfa435b6\n\nSalasana: 733dc471\n\n---------------------------------------\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net');
                    
                    //$this->email->send();
                    //TESTAAMATON
		}
            }
            else
                $vars['join_msg'] = "Roskapostitarkastus epäonnistui. Olet botti.";
		$vars['join_msg_type'] = "danger";
            
            $this->fuel->pages->render('jasenyys/liity', $vars);
        }
    }
}
?>