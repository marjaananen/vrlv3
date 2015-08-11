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
        $this->load->library('form_builder', array('submit_name'=>'Kirjaudu', 'submit_value'=>'Kirjaudu sisään', 'required_text' => '*Pakollinen kenttä'));
         
        // create fields
        $fields['tunnus'] = array('type' => 'text', 'required' => TRUE, 'name' => 'identity', 'label' => 'Tunnus', 'class'=>'form-control');
        $fields['salasana'] = array('type' => 'password', 'required' => TRUE, 'name' => 'password', 'label' => 'Salasana', 'class'=>'form-control');

        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/auth/login'));   

        // render the page
        $vars['login_form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields );
        
        if ($this->ion_auth->logged_in())
        {
            $vars['login_status'] = "Olet kirjautunut sisään nimellä: " . $this->session->userdata( 'username' ) . "<br> <a href=" . site_url('/auth/logout') . "> Logout </a>";
        }
        else
        {
            $vars['login_status'] = "Et ole kirjautunut sisään";
        }

        $this->fuel->pages->render('index', $vars);
    }
}
?>