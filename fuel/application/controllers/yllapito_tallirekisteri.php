<?php
class Yllapito_tallirekisteri extends CI_Controller
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
   
    function tallirekisteri_etusivu()
    {
        $this->load->library('queue_manager', array('db_table' => 'vrlv3_tallirekisteri_jonossa'));
        $this->session->set_flashdata('return_status', '');
        $vars['title'] = "Tallirekisteri";
        
        $vars['view_status'] = "queue_status";
        
        $frontpage = $this->queue_manager->get_queue_frontpage();
        $vars['queue_status_html'] = "<h3>Tallijono</h3>" . $frontpage['html'];
            
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
                redirect('/yllapito/tallirekisteri/hyvaksy');
            }
            
            if($approved == 'hyvaksy')
            {
                if(empty($tnro_alpha) || strlen($tnro_alpha) < 2 || strlen($tnro_alpha) > 4 || !ctype_alpha($tnro_alpha))
                {
                    $this->session->set_flashdata('return_info', 'Anomuksen käsittely epäonnistui, koska annoit virheellisen tallilyhenteen kirjainosan!');
                    $this->session->set_flashdata('return_status', 'danger');
                    redirect('/yllapito/tallirekisteri/hyvaksy');
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
                $this->tallit_model->add_rejected_stable(); //Hylkäys muistiin
                
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
            
        redirect('/yllapito/tallirekisteri/hyvaksy');
    }
    
    function muokkaa_talli()
    {
        //näytä lomake jolle annetaan tallitunnus. se redirectaa muokkaukseen
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
                $this->tallit_model->add_rejected_category(); //Hylkäys muistiin
                
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