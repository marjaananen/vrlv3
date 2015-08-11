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
        $this->fuel->pages->render('index', $vars);
    }
}
?>