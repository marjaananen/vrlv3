<?php
class Tallit extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
	$this->load->model('tallit_model');
	
	$vars['stables'] = $this->tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
	
	$this->fuel->pages->render('tallit/index', $vars);
    }

    function rekisteroi()
    {
	$this->load->library('form_validation');
	$this->load->library('form_collection');
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
	    $vars['form'] = $this->form_collection->get_stable_form('application'); //pyydetään lomake hakemusmoodissa
	    
	    $this->fuel->pages->render('tallit/rekisteroi', $vars);
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
            
            $this->fuel->pages->render('tallit/rekisteroi', $vars);
        }
        else
            redirect('/', 'refresh');
    }
    
    //sisältää sekä käyttäjän että ylläpidon muokkauslogiikan riippuen modesta (edit tai admin)
    function muokkaa($tnro, $mode)
    {
	$this->load->model('tallit_model');
	
	if(empty($tnro) || empty($mode))
	    redirect('/');
	
	//vain ylläpito tai omistaja saa muokata
	if(!($this->ion_auth->logged_in() && $this->ion_auth->in_group('yllapito') && $mode == 'admin') && !($this->tallit_model->is_stable_owner($this->ion_auth->user()->row()->tunnus, $tnro) && $mode == 'edit'))
	    redirect('/');
	
	$this->load->library('form_validation');
	$this->load->library('form_collection');
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
	    $vars['form'] = $this->form_collection->get_stable_form($mode, $tnro); //pyydetään lomake muokkausmoodissa
	    
	    if($vars['form'] == "")
		redirect('/');
	    
	    $this->fuel->pages->render('tallit/muokkaa', $vars);
        }
        else if($this->input->server('REQUEST_METHOD') == 'POST')
        {
	    $this->load->model('tallit_model');
	    
	    if ($this->form_collection->validate_stable_form($mode) == FALSE)
	    {
		$vars['msg'] = "Muokkaus epäonnistui!";
		$vars['msg_type'] = "danger";
	    }
	    else
	    {
		$vars['msg'] = "Muokkaus onnistui!";
		$vars['msg_type'] = "success";
		
		if($mode == 'edit')
		    $this->tallit_model->edit_stable($this->input->post('nimi'), $this->input->post('kuvaus'), $this->input->post('osoite'), $tnro);
		else
		    $this->tallit_model->edit_stable($this->input->post('nimi'), $this->input->post('kuvaus'), $this->input->post('osoite'), $tnro, $this->input->post('tallinumero'));
	    }
	    
	    $vars['form'] = $this->form_collection->get_stable_form($mode, $tnro);
            
	    if($mode == 'edit')
		$this->fuel->pages->render('tallit/muokkaa', $vars);
	    else
		redirect($this->input->server('HTTP_REFERER'));
        }
        else
            redirect('/', 'refresh');
    }
}
?>