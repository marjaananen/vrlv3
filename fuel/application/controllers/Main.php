<?php
class Main extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->model("uutiset_model");
                $this->load->model("Oikeudet_model");

        $vars = array();
        $vars['admins'] = $this->Oikeudet_model->users_in_group_id(1);
        $vars['message'] = $this->session->flashdata('message');
        $vars['tiedotukset'] = $this->uutiset_model->hae_tiedotukset(5,0);
        $this->fuel->pages->render('index', $vars);
    }
    
    function yllapito(){
        $this->fuel->pages->render('yllapito/index');

    }
    

    
}
?>