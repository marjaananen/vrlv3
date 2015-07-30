<?php
class Jasenyys extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function liity()
    {
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Liity', 'required_text' => '*Pakollinen kenttä'));
            
            $options = array('1' => 'Ahvenanmaa', '2' => 'Etelä-Karjala', '3' => 'Etelä-Pohjanmaa', '4' => 'Etelä-Savo', '5' => 'Itä-Uusimaa', '6' => 'Kainuu', '7' => 'Kanta-Häme', '8' => 'Keski-Pohjanmaa',
                             '9' => 'Keski-Suomi', '10' => 'Kymenlaakso', '11' => 'Lappi', '12' => 'Pirkanmaa', '13' => 'Pohjanmaa', '14' => 'Pohjois-Karjala', '15' => 'Pohjois-Pohjanmaa',
                             '16' => 'Pohjois-Savo', '17' => 'Päijät-Häme', '18' => 'Satakunta', '19' => 'Uusimaa', '20' => 'Varsinais-Suomi', '21' => 'Ulkomaat');
             
            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'required' => TRUE, 'after_html' => '<span class="form_comment">Nimimerkit eivät ole yksilöllisiä</span>');
            $fields['email'] = array('type' => 'password', 'required' => TRUE, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">esimerkki@osoite.fi</span>');
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
                $vars['join_msg'] = "";
            }
            else
                $vars['join_msg'] = "Roskapostitarkastus epäonnistui.";
            
            $this->fuel->pages->render('jasenyys/liity', $vars);
        }
    }
}
?>