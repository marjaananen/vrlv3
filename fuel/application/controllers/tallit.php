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
    
    function talliprofiili($tnro)
    {
	$this->fuel->pages->render('misc/showmessage', array('msg' => $tnro));
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
    
    function haku()
    {
	$this->load->model('tallit_model');
	$this->load->library('form_validation');
	$this->load->library('form_builder', array('submit_value' => 'Hae'));
	$vars['title'] = 'Tallihaku';
	$vars['msg'] = 'Hae talleja tallirekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
	
	$options = $this->tallit_model->get_category_option_list();
	$options[-1] = 'Mikä tahansa';
	
	$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
	$fields['kategoria'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');
	$fields['tallitunnus'] = array('type' => 'text', 'class'=>'form-control');

	$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/tallit/haku'));
	$vars['form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields);
	
	if($this->input->server('REQUEST_METHOD') == 'POST')
	{
	    $this->form_validation->set_rules('nimi', 'Nimi', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
	    $this->form_validation->set_rules('kategoria', 'Kategoria', 'min_length[1]|max_length[2]');
	    $this->form_validation->set_rules('tallitunnus', 'Tallitunnus', "min_length[6]|max_length[8]|regex_match[/^[A-Z0-9]*$/]");

	    if($this->form_validation->run() == true && !(empty($this->input->post('nimi')) && empty($this->input->post('tallitunnus')) && $this->input->post('kategoria') == "-1"))
	    {
		$vars['headers'][1] = array('title' => 'Tallitunnus', 'key' => 'tnro', 'profile_link' => site_url('talli/') . '/');
		$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Kategoria', 'key' => 'katelyh', 'aggregated_by' => 'tnro');
		$vars['headers'][4] = array('title' => 'Perustettu', 'key' => 'perustettu');
		
		$vars['headers'] = json_encode($vars['headers']);
		
		$vars['data'] = json_encode($this->tallit_model->search_stables($this->input->post('nimi'), $this->input->post('kategoria'), $this->input->post('tallitunnus')));
	    }
	}
	
	$this->fuel->pages->render('misc/haku', $vars);
    }
}
?>





