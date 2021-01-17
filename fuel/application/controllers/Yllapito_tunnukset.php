<?php
class Yllapito_tunnukset extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'tunnukset');
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
        $this->load->model("tunnukset_model");
        $this->load->model("Oikeudet_model");
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
            
            $vars['application_data']['rekisteroitynyt'] = date('d.m.Y H:i',strtotime($vars['application_data']['rekisteroitynyt']));
                
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
                $additional_data = array('nimimerkki' => $application_data['nimimerkki']);
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
    
    
    
    public function muokkaa($tunnus = null)
	{
        $data['title'] = "Muokkaa käyttäjän oikeuksia";
        $this->load->library("vrl_helper");
        //jos haettiin tunnusta, avataan ko. tunnuksen editori
        if($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('tunnushaku')){
            $tunnus = $this->input->post('VRL');
            if($this->vrl_helper->check_vrl_syntax($tunnus)){
                redirect('/yllapito/tunnukset/muokkaa/'.$this->vrl_helper->vrl_to_number($tunnus), 'refresh');
                return;
            } else  {
                $data['msg'] = "Tunnusta ei löydy";
                $data['msg_type'] = "danger";
                
                $this->fuel->pages->render('misc/naytaviesti', $data);
            }
         
        }
        //jos haluttiin tallentaa oikeuksia
        else if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('oikeus')){
            $tunnus = $this->vrl_helper->vrl_to_number($this->input->post('tunnus'));
            $this->sort_users_groups($this->input->post('oikeudet'), $this->ion_auth->get_user_id_from_identity($tunnus));
            redirect('/yllapito/tunnukset/oikeudet', 'refresh');
 
        }
        
        else if ($tunnus != null){
            $user_id = $this->ion_auth->get_user_id_from_identity($this->vrl_helper->vrl_to_number($tunnus));
            
            if ($user_id == false){
                $data['msg'] = "Tunnusta ei löydy";
                $data['msg_type'] = "danger";
                
                $this->fuel->pages->render('misc/naytaviesti', $data);

            }
            else {
                $groups = $this->ion_auth->groups()->result_array();
                $currentGroups = $this->ion_auth->get_users_groups($user_id)->result();
                $group_options = $this->Oikeudet_model->sanitize_automatic_groups($groups);
                
                $users_groups=array();
                foreach ($currentGroups as $group){
                    $users_groups[]=$group->id;
                }
                
           
                $data['msg'] = "Valitse käyttäjälle sopivat oikeudet";
                $this->load->library('form_builder', array('submit_value' => "Muokkaa oikeuksia", 'submit_name' => 'oikeus', 'required_text' => '*Pakollinen kenttä'));
                $fields['tunnus'] = array('type' => 'hidden', 'value' => $tunnus);
                $fields['oikeudet'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE, 'options' => $group_options, 'value'=>$users_groups, 'class'=>'form-control', 'wrapper_tag' => 'li');
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => '/yllapito/tunnukset/muokkaa/'.$this->vrl_helper->vrl_to_number($tunnus));
    
                
                $data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                
        
                $this->fuel->pages->render('misc/lomakemuokkaus', $data);
    
            }
        
        }
    
        
        //eio tunnusta, haetaan tunnuksenhakulomake
        else {
            $this->load->library('form_builder', array('submit_value' => 'Hae', 'submit_name'=>'tunnushaku'));
            $fields['VRL'] = array('type' => 'text', 'class'=>'form-control');
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/tunnukset/muokkaa'));                  
            $data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
             $this->fuel->pages->render('misc/lomakemuokkaus', $data);
        }
                    
            
    }
        
    public function oikeudet($oikeus = null){
        $this->load->model('Oikeudet_model');
        $vars['title'] = 'Käyttöoikeudet';				

        if ($oikeus == null){        
        
			$vars['text_view'] = "";			
			$vars['headers'][1] = array('title' => 'Id', 'key' => 'id', 'key_link' => site_url('yllapito/tunnukset/oikeudet/'));
			$vars['headers'][2] = array('title' => 'Oikeusryhmä', 'key' => 'name');
			$vars['headers'][3] = array('title' => 'Kuvaus', 'key' => 'description');			
			$vars['headers'][4] = array('title' => 'Jäsenet (kpl)', 'key' => 'kpl');
			
			$vars['headers'] = json_encode($vars['headers']);		
			$stables = $this->Oikeudet_model->get_groups();
			
			$vars['data'] = json_encode($stables);
	
			$this->fuel->pages->render('misc/taulukko', $vars);
        }
            
        else {
            				
			$vars['text_view'] = "";
			
			$vars['headers'][1] = array('title' => 'Tunnus', 'key' => 'tunnus', 'type'=>'VRL', 'key_link' => site_url('/tunnus/'));
			$vars['headers'][2] = array('title' => 'Nimimerkki', 'key' => 'nimimerkki');
			$vars['headers'][3] = array('title' => 'Editoi', 'key' => 'tunnus', 'key_link' => site_url('yllapito/tunnukset/muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
			
			
			$vars['headers'] = json_encode($vars['headers']);
			
			
			$stables = $this->Oikeudet_model->users_in_group_id($oikeus);
			$vars['data'] = json_encode($stables);
	
			$this->fuel->pages->render('misc/taulukko', $vars);
            }
        
        
    }
    
    public function kirjautumiset($tapa = 'tunnus'){
        $data = array();
        $data['title'] = 'Viimeisimpien kirjautumisten haku';
        $this->load->library('vrl_helper');
        if($tapa == 'tunnus'){
                $this->load->library('form_builder', array('submit_value' => 'Hae kirjautumiset', 'submit_name'=>'tunnushaku'));
                $fields['tunnus'] = array('type' => 'text', 'class'=>'form-control', 'after_html' => '<span class="form_comment">VRL-tunnus, jonka viimeiset kirjautumistiedot haluat.</span>');
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/tunnukset/kirjautumiset/tunnus'));                  
                $data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
        }else if ($tapa == 'ip'){
                $this->load->library('form_builder', array('submit_value' => 'Hae kirjautumiset', 'submit_name'=>'iphaku'));
                $fields['ip'] = array('type' => 'text', 'class'=>'form-control', 'after_html' => '<span class="form_comment">IP osoite (Esim. 127.0.0.1) josta kirjautuneet haluat.</span>');
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/tunnukset/kirjautumiset/ip'));                  
                $data['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
        }
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $vars['text_view'] = "";
            
            $vars['headers'][1] = array('title' => 'Tunnus', 'key' => 'tunnus', 'type' => 'vrl', 'key_link' => site_url('tunnus/'));
            $vars['headers'][2] = array('title' => 'Nimimerkki', 'key' => 'nimimerkki');
            $vars['headers'][3] = array('title' => 'Aika', 'key' => 'aika', 'type'=>'date');
            $vars['headers'][4] = array('title' => 'Ip-osoite', 'key' => 'ip');

            $vars['headers'] = json_encode($vars['headers']);
            if($tapa == 'tunnus' && $this->input->post('tunnushaku')){
                if($this->vrl_helper->check_vrl_syntax($this->input->post('tunnus'))){
                    $latest_logins = $this->tunnukset_model->get_latest_logins($this->vrl_helper->vrl_to_number($this->input->post('tunnus')), 20);
                
                    $vars['data'] = json_encode($latest_logins);            
                    $data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);

                }
                else {
                    $this->fuel->pages->render('misc/naytaviesti', array("msg"=>"Virheellinen tunnus!", "msg_type"=>"danger"));
                }
            }
            
            else if($tapa == 'ip' && $this->input->post('iphaku')){
                $latest_logins = $this->tunnukset_model->get_logins_by_ip($this->input->post('ip'), 20);
                
                $vars['data'] = json_encode($latest_logins);            
                $data['tulokset'] = $this->load->view('misc/taulukko', $vars, TRUE);


            }
        }


            $this->fuel->pages->render('misc/haku', $data);
            
            
        
        
        
    }
    
    
    
        
    private function sort_users_groups($groupData, $id){
                            // Only allow updating groups if user is admin
            // Update the groups user belongs to

            if (isset($groupData) && !empty($groupData))
            {

                $this->ion_auth->remove_from_group('', $id);

                foreach ($groupData as $grp)
                {
                    $this->ion_auth->add_to_group($grp, $id);
                }

            }
                    
    }
    
    
	}
    

?>