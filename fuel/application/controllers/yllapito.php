<?php
class Yllapito extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    //salainen adminin funktio, jolla voi lisätä käyttäjän johonkin käyttöoikeusryhmään
    //parametrit query stringinä
    function add_user_to_group()
    {
        if ($this->ion_auth->logged_in())
        {
            if(!$this->ion_auth->is_admin())
                $vars['msg'] = "Et ole admin";
            else
            {
                $userid = $this->input->get('userid', TRUE);
                $groupid = $this->input->get('groupid', TRUE);
                
                if($userid != false && $groupid != false)
                {
                    if($this->ion_auth->add_to_group($groupid, $userid) == true)
                        $vars['msg'] = "Onnistui";
                    else
                        $vars['msg'] = "Epäonnistui";
                }
                else
                    $vars['msg'] = "Kämmäsit parametrit";
            }
        }
        else
            $vars['msg'] = "Et ole kirjautunut sisään";

        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    
    function hakemusjono()
    {
        if($this->ion_auth->logged_in() && $this->ion_auth->in_group('yllapito'))
        {
            $this->load->model('tunnukset_model');
            
            if($this->input->server('REQUEST_METHOD') == 'POST')
            {
                $vars['view_status'] = "next_join_application";
                
                $vars['application_data'] = $this->tunnukset_model->get_next_application();
                
                if($vars['application_data']['success'] == false)
                {
                    //flash msg että feil
                    redirect('/yllapito/hakemusjono', 'refresh');
                }
            }
            else
            {
                $vars['view_status'] = "queue_status";
                
                $vars['queue_length'] = $this->tunnukset_model->get_queue_length();
                
                $this->load->library('form_builder', array('submit_value' => 'Hae seuraava hakemus'));
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/hakemusjono'));
                $vars['get_next_application'] = $this->form_builder->render();
            }
        }
        else
            $vars['view_status'] = "restricted";
            
        $this->fuel->pages->render('yllapito/hakemusjono', $vars);
    }
}
?>