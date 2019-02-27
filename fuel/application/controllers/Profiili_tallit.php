<?php
class Profiili_tallit extends Loggedin_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //Kaikki funktiot täällä vaativat kirjautuneen käyttäjän, joten:
        if(!($this->ion_auth->logged_in()))
        {
            redirect('/');
        }
		
		$this->load->model('tallit_model');

    }
    
    
    function index()
    {
		$this->load->model('tallit_model');
		
		$vars['stables'] = $this->tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
		
		$this->fuel->pages->render('profiili/tallit/index', $vars);
    }
    
    function rekisteroi_talli()
    {
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		$vars['title'] = 'Rekisteröi talli';
			
		if($this->input->server('REQUEST_METHOD') == 'GET')
		{
			$vars['form'] = $this->form_collection->get_stable_form('application'); //pyydetään lomake hakemusmoodissa
			$vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröimisen jälkeen ylläpito käsittelee anomuksesi. Muista, että tallin kaikilta pääsivuilta tulee olla löydettävissä sana "virtuaalitalli"! Tallin omistajaksi merkitään rekisteröintihakemuksen lähettäjä. Voit lisätä tallille lisää omistajia rekisteröinnin jälkeen.';
			
			$this->fuel->pages->render('misc/jonorekisterointi', $vars);
		}
		else if($this->input->server('REQUEST_METHOD') == 'POST')
		{
			$this->load->model('tallit_model');
		
			if ($this->form_collection->validate_stable_form('application') == FALSE)
			{
				$vars['msg'] = "Anomuksen lähetys epäonnistui!";
				$vars['msg_type'] = "danger";
			}
			else
			{
				$vars['msg'] = "Anomuksen lähetys onnistui!";
				$vars['msg_type'] = "success";
				$this->tallit_model->add_new_application($this->input->post('nimi'), $this->input->post('kuvaus'), $this->input->post('osoite'), $this->input->post('kategoria'), strtoupper($this->input->post('lyhehd')));
			}
			
			$this->fuel->pages->render('misc/jonorekisterointi', $vars);
		}
		else
			redirect('/', 'refresh');
    }
    
    function muokkaa_talli($tnro)
    {
		$mode = 'edit';
		
		if(empty($tnro))
			$this->index();
		
		
		//vain omistaja saa muokata
		if(!($this->tallit_model->is_stable_owner($this->ion_auth->user()->row()->tunnus, $tnro)))
			$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Voit muokata vain omia tallejasi.'));
			
		
			
		$vars['title'] = 'Muokkaa tallin tietoja';

		
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		
			
		if($this->tallit_model->is_stable_active($tnro))
			$vars['append'] = "<p><a href='" . site_url('profiili/lopeta_talli') . "/" . $tnro . "'>Lopeta talli</a></p>";
			
		if($this->input->server('REQUEST_METHOD') == 'GET')
			{
				$vars['form'] = $this->form_collection->get_stable_form($mode, $tnro); //pyydetään lomake muokkausmoodissa
				
				if($vars['form'] == ""){
					$this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Virhe lomakkeen tulostamisessa. Ota yhteys ylläpitoon!'));	
				}
				else {
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);
				}
			}
		else if($this->input->server('REQUEST_METHOD') == 'POST')
			{
				
				if($this->form_collection->validate_stable_form($mode) == FALSE || count($this->input->post('kategoria')) == 0)
				{
					var_dump($this->input->post('kategoria'));
					$vars['msg'] = "Muokkaus epäonnistui!";
					$vars['msg_type'] = "danger";
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);	

				}
				else
				{
					$vars['msg'] = "Muokkaus onnistui!";
					$vars['msg_type'] = "success";
					
					$this->tallit_model->edit_stable($this->input->post('nimi'), $this->input->post('kuvaus'), $this->input->post('osoite'), $tnro);
					$this->tallit_model->mass_edit_categories($tnro, $this->input->post('kategoria'));
					
					$vars['form'] = $this->form_collection->get_stable_form($mode, $tnro);
						
					$this->fuel->pages->render('misc/lomakemuokkaus', $vars);
				}
	
			}
			else
				$this->fuel->pages->render('misc/naytaviesti');	
    }
    
    function lopeta_talli($tnro)
    {
        $this->load->model('tallit_model');
        
        if(!isset($tnro) || !$this->tallit_model->is_stable_owner($this->ion_auth->user()->row()->tunnus, $tnro))
            redirect('/');
        
        $this->tallit_model->mark_stable_inactive($tnro);
            
        $this->fuel->pages->render('misc/naytaviesti', array('msg' => 'Tallisi on merkattu lopettaneeksi.'));
    }
    
    function rekisteroi_kategoria($tnro)
    {
	$this->load->library('form_validation');
	$this->load->library('form_collection');
	$this->load->model('tallit_model');
	$vars['title'] = 'Ano tallille uusi kategoria';
	
	if(empty($tnro) || !$this->tallit_model->is_stable_owner($this->ion_auth->user()->row()->tunnus, $tnro))
	    redirect('/');
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
	    $vars['form'] = $this->form_collection->get_stable_category_form($tnro);
	    $vars['msg'] = 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröimisen jälkeen ylläpito käsittelee anomuksesi.';
	    
	    $this->fuel->pages->render('misc/jonorekisterointi', $vars);
        }
        else if($this->input->server('REQUEST_METHOD') == 'POST')
        {
	    if($this->form_collection->validate_stable_category_form($tnro) == FALSE)
	    {
		$vars['msg'] = "Anomuksen lähetys epäonnistui!";
		$vars['msg_type'] = "danger";
	    }
	    else
	    {
		$vars['msg'] = "Anomuksen lähetys onnistui!";
		$vars['msg_type'] = "success";
		$this->tallit_model->add_new_category_application($this->input->post('tallinumero'), $this->input->post('kategoria'));
	    }
            
            $this->fuel->pages->render('misc/jonorekisterointi', $vars);
        }
        else
            redirect('/', 'refresh');
    }
    
    function poista_kategoria($id)
    {
	$this->load->model('tallit_model');
	
	if(empty($id))
	    redirect('/', 'refresh');
	
	if(!$this->tallit_model->is_category_owner($id, $this->ion_auth->user()->row()->tunnus))
	    redirect('/', 'refresh');
	
	$this->tallit_model->delete_category($id);
	
	redirect($_SERVER['HTTP_REFERER'], 'refresh'); 
    }
    
    
}
?>






