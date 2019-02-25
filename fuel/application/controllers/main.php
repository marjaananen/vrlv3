<?php
class Main extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('index', $vars);
    }
    

    
}
?>