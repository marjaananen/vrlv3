<?php
class Yllapito_tunnukset extends CI_Controller
{
    
    private $allowed_user_groups = array('admin');
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
    }

    //ADMIN-OSUUS
    
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

        $this->fuel->pages->render('misc/naytaviesti', $vars);
    }
    
    //HAKEMUSJONO-OSUUS
    
    function hakemusjono_etusivu()
    {
        $this->load->model('tunnukset_model');
        $this->session->set_flashdata('return_status', '');
        
        $vars['view_status'] = "queue_status";
        
        $vars['queue_length'] = $this->tunnukset_model->get_application_queue_length();
        if($vars['queue_length'] > 0)
            $vars['oldest_application'] = $this->tunnukset_model->get_oldest_application();
            
        $vars['queue_unlocked_num'] = $this->tunnukset_model->get_application_queue_unlocked_num();
        $vars['latest_approvals'] = $this->tunnukset_model->get_latest_approvals();
        $vars['latest_logins'] = $this->tunnukset_model->get_latest_logins();
        $vars['latest_failed_logins'] = $this->tunnukset_model->get_latest_failed_logins();
            
        $this->fuel->pages->render('yllapito/hakemusjono', $vars);
    }
    
    function hakemusjono()
    {
        $this->load->model('tunnukset_model');
        $this->session->set_flashdata('return_status', '');
        
        $vars['view_status'] = "next_join_application";
        
        $vars['application_data'] = $this->tunnukset_model->get_next_application();
        
        if($vars['application_data']['success'] == false)
        {
            $this->session->set_flashdata('return_info', 'Uuden hakemuksen noutaminen epäonnistui!<br />Joku saattaa olla jo hyväksymässä loppuja hakemuksia, hakemukset loppuivat, tai tapahtui muu virhe.');
            $this->session->set_flashdata('return_status', 'danger');
            redirect('/yllapito/tunnukset');
        }
        else {
            $vars['same_ip_logins'] = $this->tunnukset_model->get_logins_by_ip($vars['application_data']['ip']);
            $vars['same_nicknames'] = $this->tunnukset_model->get_pinnumbers_by_nickname($vars['application_data']['nimimerkki']);
            if($vars['application_data']['syntymavuosi'] != '0000-00-00')
                $vars['same_dateofbirths'] = $this->tunnukset_model->get_users_by_dateofbirth($vars['application_data']['syntymavuosi']);
            else
                $vars['same_dateofbirths'] = array();
            
            $vars['application_data']['rekisteroitynyt'] = date('d.m.Y H:i',strtotime($vars['application_data']['rekisteroitynyt']));
            $vars['application_data']['sijainti'] = $this->tunnukset_model->get_location($vars['application_data']['sijainti']);
            if($vars['application_data']['syntymavuosi'] == '0000-00-00')
                $vars['application_data']['syntymavuosi'] = 'Ei saatavilla';
            else
                $vars['application_data']['syntymavuosi'] = date("d.m.Y", strtotime($vars['application_data']['syntymavuosi']));
                
            $this->fuel->pages->render('yllapito/hakemusjono', $vars);
        }
    }
    
    function kasittele_hakemus($approved, $id)
    {
        $user = $this->ion_auth->user()->row();
        $date = new DateTime();
        $new_pinnumber = -1;
        $date->setTimestamp(time());
        $this->session->set_flashdata('return_status', '');
        $rej_reason = $this->input->post('rejection_reason');
        
        if(empty($rej_reason))
            $rej_reason = "-";
        
        if($this->input->server('REQUEST_METHOD') == 'POST' && is_numeric($id) && $id >= 0 && ($approved == 'hyvaksy' || $approved == 'hylkaa'))
        {
            $this->load->library('email');
            $this->load->model('tunnukset_model');
        
            $application_data = $this->tunnukset_model->get_application($id);
            
            //email message
            $email = "";
            
            if($application_data['success'] == false)
            {
                $this->session->set_flashdata('return_info', 'Hakemuksen käsittely epäonnistui!');
                $this->session->set_flashdata('return_status', 'danger');
                redirect('/yllapito/tunnukset/hyvaksy');
            }
            
            if($approved == 'hyvaksy')
            {   
                $additional_data = array('laani' => $application_data['sijainti'], 'syntymavuosi' => $application_data['syntymavuosi'], 'nimimerkki' => $application_data['nimimerkki']);
                $additional_data['hyvaksytty'] = $date->format('Y-m-d H:i:s');
                $additional_data['hyvaksyi'] = $user->tunnus;
                $additional_data['tunnus'] = $this->tunnukset_model->get_next_pinnumber();
                $new_pinnumber = str_pad($additional_data['tunnus'], 5, '0', STR_PAD_LEFT);
                
                $this->ion_auth->register($new_pinnumber, $application_data['salasana'], $application_data['email'], $additional_data);
                
                $message = 'Tunnushakemuksesi on hyväksytty, tervetuloa käyttämään VRL:ää!\nVoit kirjautua sisään alla olevalla tunnuksella ja salasanalla sivuston oikeassa yläkulmassa olevan lomakkeen avulla. Kirjoita tunnuksen numero-osa ensimmäiseen laatikkoon ja salasanasi toiseen. Muista vaihtaa salasana ensimmäisellä kirjautumiskerralla!\n\n---------------------------------------\n\nVRL-tunnus: ' .  $new_pinnumber . '\nSalasana: ' .  $application_data['salasana'] . '\n\n---------------------------------------\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net';

                $this->session->set_flashdata('return_info', 'Hakemus hyväksytty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            else
            {
                //Onko tarve? $this->tunnukset_model->add_rejected_user($id); //Hylkäys muistiin
                
                if($rej_reason != false)
                    $message = 'Valitettavasti tunnushakemuksesi on hylätty!\nSyy: ' . $rej_reason . '\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net';
                else
                    $message = 'Valitettavasti tunnushakemuksesi on hylätty!\n\nÄlä vastaa tähän sähköpostiin!\nJos et ole lähettänyt jäsenhakemusta, ota yhteys VRL:n ylläpitoon osoitteessa yllapito@virtuaalihevoset.net';
                   
                $this->session->set_flashdata('return_info', 'Hakemus hylätty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            
                //email
            $this->load->library('vrl_email');
            $to = $application_data['email']; 
            $subject = 'VRL-tunnushakemuksesi on käsitelty';
                
			if ($this->vrl_email->send($to, $subject, $message)){
				$vars['msg'] = "Tunnuksen käsittely onnistui.";
				$vars['msg_type'] = "success";
            }
					//What if sending fails?
                
            //poistetaan hakemus kun se on nyt käsitelty
            $this->tunnukset_model->delete_application($id);
            redirect('/yllapito/tunnukset/hyvaksy');

        }
            
    }
}
?>