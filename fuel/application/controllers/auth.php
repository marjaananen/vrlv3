<?php
class Auth extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function login()
    {
        $vars['testi'] = $this->input->post('tunnus', TRUE) . $this->input->post('salasana', TRUE);
        
        $this->fuel->pages->render('index', $vars);
    }
}
?>