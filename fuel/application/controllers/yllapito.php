<?php
class Yllapito extends CI_Controller
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
                
                $this->session->set_flashdata('return_info', 'Hakemus hyväksytty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            else
            {
                $this->load->model('misc_model');
                $this->misc_model->add_rejected_queue_item('tunnus'); //Hylkäys muistiin
                
                $this->session->set_flashdata('return_info', 'Hakemus hylätty.');
                $this->session->set_flashdata('return_status', 'success');
            }
            
                //sposti
            $this->email->from('jasenyys@virtuaalihevoset.net', 'Jäsenyyskone');
            $this->email->to($application_data['email']); 
            
            $this->email->subject('VRL-tunnushakemuksesi on käsitelty');
            
            if($approved == 'hyvaksy')
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
    
    //TALLIREKISTERI-OSUUS
    
    function tallirekisteri_etusivu()
    {
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $vars['title'] = "Tallirekisteri";
        
        $vars['view_status'] = "queue_status";
        
        $frontpage = $this->queue_manager->get_queue_frontpage();
        $vars['queue_status_html'] = "<h3>Tallijono</h3>" . $frontpage['html'];
        
        $this->queue_manager->set_db_table('vrlv3_tallirekisteri_kategoriat_jonossa');
        $frontpage = $this->queue_manager->get_queue_frontpage();
        $vars['queue_status_html'] .= "<h3>Kategoriajono</h3>" . $frontpage['html'];
            
        $this->fuel->pages->render('misc/jonokasittely', $vars);
    }
    
    function tallijono()
    {
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $vars['title'] = "Tallijono";
        
        $this->load->model('tallit_model');
        $vars['view_status'] = "next_queue_item";
        $qitem = $this->queue_manager->get_next();
        
        if($qitem['success'] == false)
        {
            $this->session->set_flashdata('return_info', 'Seuraavan tallianomuksen noutaminen epäonnistui!<br />Joku saattaa olla jo hyväksymässä haettua tallia, tai tapahtui muu virhe.');
            $this->session->set_flashdata('return_status', 'danger');
            redirect('/yllapito/tallirekisteri');
        }
        
        $raw_data = array();
        $raw_data['Nimi'] = $qitem['nimi'];
        $raw_data['Lyhenne'] = $qitem['lyhenne'];
        $raw_data['Kategoria'] = $this->tallit_model->get_category($qitem['kategoria']);
        $raw_data['URL'] = $qitem['url'];
        $raw_data['Kuvaus'] = $qitem['kuvaus'];
        $raw_data['Anottu'] = $qitem['lisatty'];
        $raw_data['Anoja'] = "VRL-" . $qitem['lisaaja'];
        
        $raw_data['__extra_param'] = $qitem['lyhenne'];
        
        $vars['queue_item_html'] = $this->queue_manager->format_html('Tallianomus', $raw_data, $qitem['id']);
            
        $this->fuel->pages->render('misc/jonokasittely', $vars);
    }
    
    function kasittele_talli($approved, $id)
    {
        $this->load->model('tallit_model');
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $rej_reason = $this->input->post('rejection_reason');
        $tnro_alpha = $this->input->post('tnro_alpha');
        $insert_data = array();
        $msg = "";
        
        if(empty($rej_reason))
            $rej_reason = "-";
        
        if($this->input->server('REQUEST_METHOD') == 'POST' && is_numeric($id) && $id >= 0 && ($approved == 'hyvaksy' || $approved == 'hylkaa'))
        {
            $qitem = $this->queue_manager->get_by_id($id);
            
            if($qitem['success'] == false)
            {
                $this->session->set_flashdata('return_info', 'Anomuksen käsittely epäonnistui!');
                $this->session->set_flashdata('return_status', 'danger');
                redirect('/yllapito/tallirekisteri/tallijono/hyvaksy');
            }
            
            if($approved == 'hyvaksy')
            {
                if(empty($tnro_alpha) || strlen($tnro_alpha) < 2 || strlen($tnro_alpha) > 4 || !ctype_alpha($tnro_alpha))
                {
                    $this->session->set_flashdata('return_info', 'Anomuksen käsittely epäonnistui, koska annoit virheellisen tallilyhenteen kirjainosan!');
                    $this->session->set_flashdata('return_status', 'danger');
                    redirect('/yllapito/tallirekisteri/tallijono/hyvaksy');
                }
                
                $approved = true;
                $this->session->set_flashdata('return_info', 'Anomus hyväksytty.');
                $this->session->set_flashdata('return_status', 'success');
                
                $msg = "Tallianomuksesi tallille " . $qitem['nimi'] . " on hyväksytty.";
                
                $date = new DateTime();
                $date->setTimestamp(time());
                
                $insert_data['nimi'] = $qitem['nimi'];
                $insert_data['url'] = $qitem['url'];
                $insert_data['kuvaus'] = $qitem['kuvaus'];
                $insert_data['perustettu'] = $date->format('Y-m-d H:i:s');
                $insert_data['tnro'] = strtoupper($tnro_alpha) . rand(1000, 9999);
                
                while($this->tallit_model->is_tnro_in_use($insert_data['tnro']))
                {
                    $insert_data['tnro'] = strtoupper($tnro_alpha) . rand(1000, 9999);
                }
            }
            else
            {
                $this->load->model('misc_model');
                $this->misc_model->add_rejected_queue_item('talli'); //Hylkäys muistiin
                
                $approved = false;
                $this->session->set_flashdata('return_info', 'Anomus hylätty.');
                $this->session->set_flashdata('return_status', 'success');
                
                $msg = "Valitettavasti tallianomuksesi tallille " . $qitem['nimi'] . " hylättiin. Syy: " . $rej_reason;
            }
            
            $this->queue_manager->process_queue_item($id, $approved, $insert_data, $qitem['lisaaja'], $msg);
            
            if($approved)
            {
                $this->tallit_model->add_category_to_stable($insert_data['tnro'], $qitem['kategoria'], $qitem['lisaaja']);
                $this->tallit_model->add_owner_to_stable($insert_data['tnro'], $qitem['lisaaja'], 1);
            }
        }
            
        redirect('/yllapito/tallirekisteri/tallijono/hyvaksy');
    }
    
    function muokkaa_talli()
    {
        $this->load->library('form_validation');
        $this->load->model('tallit_model');

        if($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('tallinumero') != null && !empty($this->tallit_model->get_stable($this->input->post('tallinumero'))))
           redirect('/profiili/muokkaa_talli/' . $this->input->post('tallinumero') . '/admin');
        else
        {
            //näytä lomake jolle annetaan tallitunnus, lomake redirectaa muokkaukseen
            $this->load->library('form_builder', array('submit_value' => 'Muokkaa', 'required_text' => '*Pakollinen kenttä'));
            
            $fields['tallinumero'] = array('type' => 'text', 'class'=>'form-control');  
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => '');
            $vars['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
            
            $vars['title'] = 'Tallin muokkaus';
            $vars['msg'] = 'Anna muokattavan tallin tallinumero.';
            
            $this->fuel->pages->render('misc/jonorekisterointi', $vars);
        }
    }
    
    function talli_haku()
    {
	$this->load->model('tallit_model');
	$this->load->library('form_validation');
	$this->load->library('form_builder', array('submit_value' => 'Hae'));
	$vars['title'] = 'Tallihaku';
	$vars['msg'] = 'Hae talleja tallirekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
	$vars['text_view'] = $this->load->view('tallit/etusivu_teksti', NULL, TRUE);
	
	$options = $this->tallit_model->get_category_option_list();
	$options[-1] = 'Mikä tahansa';
	
	$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
	$fields['kategoria'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');
	$fields['tallinumero'] = array('type' => 'text', 'class'=>'form-control');

	$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/yllapito/talli_haku'));
	$vars['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
	
	if($this->input->server('REQUEST_METHOD') == 'POST')
	{
	    $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
	    $this->form_validation->set_rules('kategoria', 'Kategoria', 'min_length[1]|max_length[2]');
	    $this->form_validation->set_rules('tallinumero', 'Tallinumero', "min_length[6]|max_length[8]|regex_match[/^[A-Z0-9]*$/]");

	    if($this->form_validation->run() == true && !(empty($this->input->post('nimi')) && empty($this->input->post('tallinumero')) && $this->input->post('kategoria') == "-1"))
	    {
		$vars['headers'][1] = array('title' => 'Tallinumero', 'key' => 'tnro', 'profile_link' => site_url('talli/') . '/');
		$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Kategoria', 'key' => 'katelyh', 'aggregated_by' => 'tnro');
		$vars['headers'][4] = array('title' => 'Perustettu', 'key' => 'perustettu');
                $vars['headers'][5] = array('title' => 'Muokkaa', 'key' => 'tnro', 'admin_edit' => site_url('profiili/muokkaa_talli') . '/');
		
		$vars['headers'] = json_encode($vars['headers']);
		
		$vars['data'] = json_encode($this->tallit_model->search_stables($this->input->post('nimi'), $this->input->post('kategoria'), $this->input->post('tallinumero')));
	    }
	}
	
	$this->fuel->pages->render('misc/haku', $vars);
    }
    
    function tallikategoriajono()
    {
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_kategoriat_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $vars['title'] = "Tallikategoriajono";
        
        $this->load->model('tallit_model');
        $vars['view_status'] = "next_queue_item";
        $qitem = $this->queue_manager->get_next();
        
        if($qitem['success'] == false)
        {
            $this->session->set_flashdata('return_info', 'Seuraavan tallikategoria-anomuksen noutaminen epäonnistui!<br />Joku saattaa olla jo hyväksymässä haettua tallikategoria-anomusta, tai tapahtui muu virhe.');
            $this->session->set_flashdata('return_status', 'danger');
            redirect('/yllapito/tallirekisteri');
        }
        
        $raw_data = array();
        $raw_data['Tallinumero'] = $qitem['tnro'];
        $raw_data['Kategoria'] = $this->tallit_model->get_category($qitem['kategoria']);
        $raw_data['Anottu'] = $qitem['lisatty'];
        $raw_data['Anoja'] = "VRL-" . $qitem['lisaaja'];
        
        $vars['queue_item_html'] = $this->queue_manager->format_html('Tallikategoria-anomus', $raw_data, $qitem['id']);

        $this->fuel->pages->render('misc/jonokasittely', $vars);
    }
    
    function kasittele_tallikategoria($approved, $id)
    {
        $this->load->model('tallit_model');
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_kategoriat_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $rej_reason = $this->input->post('rejection_reason');
        $insert_data = array();
        $msg = "";
        
        if(empty($rej_reason))
            $rej_reason = "-";
        
        if($this->input->server('REQUEST_METHOD') == 'POST' && is_numeric($id) && $id >= 0 && ($approved == 'hyvaksy' || $approved == 'hylkaa'))
        {
            $qitem = $this->queue_manager->get_by_id($id);
            
            if($qitem['success'] == false)
            {
                $this->session->set_flashdata('return_info', 'Anomuksen käsittely epäonnistui!');
                $this->session->set_flashdata('return_status', 'danger');
                redirect('/yllapito/tallirekisteri/kategoriajono/hyvaksy');
            }
            
            if($approved == 'hyvaksy')
            {
                $approved = true;
                $this->session->set_flashdata('return_info', 'Anomus hyväksytty.');
                $this->session->set_flashdata('return_status', 'success');
                
                $msg = "Tallikategoria-anomuksesi tallille " . $qitem['tnro'] . " on hyväksytty.";
                
                $date = new DateTime();
                $date->setTimestamp(time());
                
                $insert_data['tnro'] = $qitem['tnro'];
                $insert_data['kategoria'] = $qitem['kategoria'];
                $insert_data['anoi'] = $qitem['lisaaja'];
                $insert_data['lisatty'] = $date->format('Y-m-d H:i:s');
            }
            else
            {
                $this->load->model('misc_model');
                $this->misc_model->add_rejected_queue_item('kategoria'); //Hylkäys muistiin
                
                $approved = false;
                $this->session->set_flashdata('return_info', 'Anomus hylätty.');
                $this->session->set_flashdata('return_status', 'success');
                
                $msg = "Valitettavasti tallikategoria-anomuksesi tallille " . $qitem['tnro'] . " hylättiin. Syy: " . $rej_reason;
            }
            
            $this->queue_manager->process_queue_item($id, $approved, $insert_data, $qitem['lisaaja'], $msg);
        }
            
        redirect('/yllapito/tallirekisteri/kategoriajono/hyvaksy');
    }
}
?>