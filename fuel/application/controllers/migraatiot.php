<?php
class Migraatiot extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function tunnukset()
    {
        $this->load->model('migraatio_model');
        $vars = array();
        $vars['msg'] = $this->migraatio_model->migrate_tunnukset();
        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    
    function tiedotukset()
    {
        $this->load->model('migraatio_model');
        $vars = array();
        $vars['msg'] = $this->migraatio_model->migrate_tiedotukset();
        $this->fuel->pages->render('misc/showmessage', $vars);
    }
}
?>