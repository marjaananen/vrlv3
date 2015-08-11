<?php
class Yllapito extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //Kaikki funktiot täällä vaativat kirjautuneen ylläpitäjän, joten:
        if(!($this->ion_auth->logged_in() && $this->ion_auth->in_group('yllapito')))
        {
            redirect('/');
        }
    }

    //salainen adminin funktio, jolla voi lisätä käyttäjän johonkin käyttöoikeusryhmään
    //parametrit query stringinä
    function add_user_to_group()
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

        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    
    function hakemusjono()
    {
        $this->load->model('tunnukset_model');
        $this->session->set_flashdata('return_status', '');
        
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $vars['view_status'] = "next_join_application";
            
            $vars['application_data'] = $this->tunnukset_model->get_next_application();
            
            if($vars['application_data']['success'] == false)
            {
                $this->session->set_flashdata('return_info', 'Hakemustietojen noutaminen epäonnistui!<br />Joku saattaa olla jo hyväksymässä haettua hakemusta, tai tapahtui muu virhe.');
                $this->session->set_flashdata('return_status', 'danger');
                redirect('/yllapito/tunnukset/hyvaksy');
            }
            
            $vars['application_data']['rekisteroitynyt'] = date('d.m.Y H:i',strtotime($vars['application_data']['rekisteroitynyt']));
            $vars['application_data']['sijainti'] = $this->tunnukset_model->get_location($vars['application_data']['sijainti']);
            if($vars['application_data']['syntymavuosi'] == '0000-00-00')
                $vars['application_data']['syntymavuosi'] = 'Ei saatavilla';
            else
                $vars['application_data']['syntymavuosi'] = date("d.m.Y", strtotime($vars['application_data']['syntymavuosi']));
        }
        else
        {
            $vars['view_status'] = "queue_status";
            
            $vars['queue_length'] = $this->tunnukset_model->get_application_queue_length();
            
            $this->load->library('form_builder', array('submit_value' => 'Hae seuraava hakemus'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/tunnukset/hyvaksy'));
            $vars['get_next_application'] = $this->form_builder->render();
        }
            
        $this->fuel->pages->render('yllapito/hakemusjono', $vars);
    }
    
    function kasittele_hakemus($paatos, $id)
    {
        $user = $this->ion_auth->user()->row();
        $date = new DateTime();
        $new_pinnumber = -1;
        $date->setTimestamp(time());
        $this->session->set_flashdata('return_status', '');
        $rej_reason = $this->input->post('rejection_reason');
        
        if($this->input->server('REQUEST_METHOD') == 'POST' && is_numeric($id) && $id >= 0 && ($paatos == 'hyvaksy' || $paatos == 'hylkaa'))
        {
            $this->load->library('email');
            $this->load->model('tunnukset_model');
            
            $application_data = $this->tunnukset_model->get_application($id);
            
            if($application_data['success'] == false)
            {
                $this->session->set_flashdata('return_info', 'Hakemuksen käsittely epäonnistui!');
                $this->session->set_flashdata('return_status', 'danger');
                redirect('/yllapito/tunnukset/hyvaksy');
            }
            
            if($paatos == 'hyvaksy')
            {   
                $additional_data = array('laani' => $application_data['sijainti'], 'syntymavuosi' => $application_data['syntymavuosi'], 'nimimerkki' => $application_data['nimimerkki']);
                $additional_data['hyvaksytty'] = $date->format('Y-m-d H:i:s');
                $additional_data['hyvaksyi'] = $user->tunnus;
                $additional_data['tunnus'] = $this->tunnukset_model->get_next_pinnumber();
                $new_pinnumber = $additional_data['tunnus'];
                
                $this->ion_auth->register($application_data['nimimerkki'], $application_data['salasana'], $application_data['email'], $additional_data);
                
                $this->session->set_flashdata('return_info', 'Hakemus hyväksytty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            else
            {
                $this->session->set_flashdata('return_info', 'Hakemus hylätty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            
                //sposti
            $this->email->from('jasenyys@virtuaalihevoset.net', 'Jäsenyyskone');
            $this->email->to($application_data['email']); 
            
            $this->email->subject('VRL-tunnushakemuksesi on käsitelty');
            
            if($paatos == 'hyvaksy')
                $this->email->message('Tunnushakemuksesi on hyväksytty, tervetuloa käyttämään VRL:ää!\nVoit kirjautua sisään alla olevalla tunnuksella ja salasanalla sivuston oikeassa yläkulmassa olevan lomakkeen avulla. Kirjoita tunnuksen numero-osa ensimmäiseen laatikkoon ja salasanasi toiseen. Muista vaihtaa salasana ensimmäisellä kirjautumiskerralla!\n\n---------------------------------------\n\nVRL-tunnus: ' .  $new_pinnumber . '\nSalasana: ' .  $application_data['salasana'] . '\n\n---------------------------------------\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net');
            else
            {
                if($rej_reason != false)
                    $this->email->message('Valitettavasti tunnushakemuksesi on hylätty!\nSyy: ' . $rej_reason . '\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net');
                else
                    $this->email->message('Valitettavasti tunnushakemuksesi on hylätty!\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net');
            }
                
            //$this->email->send();
            //TESTAAMATON --^
                
                //poistetaan hakemus kun se on nyt käsitelty
            $this->tunnukset_model->delete_application($id);
        }
            
        redirect('/yllapito/tunnukset/hyvaksy');
    }
}
?>