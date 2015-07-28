<?php
class Main extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        // load form_builder
        $this->load->library('form_builder', array('submit_value' => 'Kirjaudu sisään', 'required_text' => '*Pakollinen kenttä'));
         
        // create fields
        $fields['tunnus'] = array('type' => 'text', 'required' => TRUE);
        $fields['salasana'] = array('type' => 'password', 'required' => TRUE);
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/auth/login'));

                        
        // set the fields
        $this->form_builder->set_fields($fields);
        // render the page
        $vars['login_form'] = $this->form_builder->render();


        $this->fuel->pages->render('index', $vars);
    }
}
?>