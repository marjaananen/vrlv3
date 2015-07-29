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
        $fields['tunnus'] = array('type' => 'text', 'required' => TRUE, 'name' => 'identity', 'label' => 'Tunnus');
        $fields['salasana'] = array('type' => 'password', 'required' => TRUE, 'name' => 'password', 'label' => 'Salasana');
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/auth/login'));

                        
        // set the fields
        $this->form_builder->set_fields($fields);
        // render the page
        $vars['login_form'] = $this->form_builder->render();
        
        if ($this->ion_auth->logged_in())
        {
            $vars['testi'] = "Olet kirjautunut sisään: " . $this->ion_auth->get_user_id();
        }
        else
        {
            $vars['testi'] = "Et ole kirjautunut sisään";
        }
        
        //$vars['testi'] = $this->session->userdata('identity');//$this->session->flashdata('message');


        $this->fuel->pages->render('index', $vars);
    }
}
?>