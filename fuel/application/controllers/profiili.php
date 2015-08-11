<?php
class Profiili extends Loggedin_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function tiedot()
    {
        $vars = array();
        $this->fuel->pages->render('profiili/tiedot', $vars);
    }
}
?>