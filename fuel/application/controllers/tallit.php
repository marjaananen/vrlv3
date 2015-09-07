<?php
class Tallit extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
	$this->fuel->pages->render('misc/showmessage', array('msg' => 'Tätä ei oo tehty'));
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
}
?>