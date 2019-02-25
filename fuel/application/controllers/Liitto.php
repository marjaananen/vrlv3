<?php
class Liitto extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
	
	$this->load->model('Uutiset_model');

    }
	
	function index ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/index', $vars);
    }
	
	function yllapito ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/yllapito', $vars);
    }
	
	function wiki()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/wiki', $vars);
    }
	
		function somessa ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/somessa', $vars);
    }
	
		function mainosta ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/mainosta', $vars);
    }
	
		function copyright ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('liitto/copyright', $vars);
    }
    
    public $max_list_length = 400;
    public $max_items_per_page = 10;
    
    
    function tiedotukset()
    {
	$vars = array();

	$page = $this->input->get('sivu', TRUE);
	$vars['pagination'] = $this->_pagination($page, $this->Uutiset_model->hae_tiedotukset_kpl());

	$vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
	
	$tiedotukset = $this->Uutiset_model->hae_tiedotukset($this->max_items_per_page, $vars['pagination']['offset']);
	$vars['tiedotukset'] = $this->_kasittele_tiedotukset($tiedotukset);
	
	
	$this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
    }
    
    
    
    function kategoria($kat)
    {
	$vars = array();
	$vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
	
	$page = $this->input->get('sivu', TRUE);
	$vars['pagination'] = $this->_pagination($page, $this->Uutiset_model->hae_kategoria_kpl($kat));

	
	$tiedotukset = $this->Uutiset_model->hae_kategoria($kat, $this->max_items_per_page, $vars['pagination']['offset']);
	$vars['tiedotukset'] = $this->_kasittele_tiedotukset($tiedotukset);
	$vars['header'] = $tiedotukset[0]['kategoriat'][0]['kat'];

	$this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
    }

    
    function tiedotus($tid)
    {
	
	$vars = array();
	$vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
	
	$tiedotukset = $this->Uutiset_model->hae_tiedotus($tid);
	
	$this->load->library('Vrl_helper');
	foreach ($tiedotukset as &$tiedotus){    
	    $this->load->model('tunnukset_model');
	    $pinnumber = $this->vrl_helper->vrl_to_number($tiedotus['lahettaja']);
	    $user = $this->ion_auth->user($this->tunnukset_model->get_users_id($pinnumber))->row();
	    
	    $tiedotus['lahettaja_nick'] = $user->nimimerkki;
	}
	$vars['tiedotukset'] = $tiedotukset;
	
	$this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
    }
    
    function arkisto($v, $m = -1)
    {
	
	$page = $this->input->get('sivu', TRUE);
	$vars['pagination'] = $this->_pagination($page, $this->Uutiset_model->hae_tiedotukset_kpl($v, $m));

	$vars = array();
	$vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
	$tiedotukset = $this->Uutiset_model->hae_tiedotukset($this->max_items_per_page, $vars['pagination']['offset'], $v, $m);
	$vars['tiedotukset'] = $this->_kasittele_tiedotukset($tiedotukset);
	

	if ($m > 1){
	    $vars['header'] = $m . "/". $v;
	}
	
	else {
	    $vars['header'] = $v;
	}
	
	$this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
    }
    
    private function _kasittele_tiedotukset($tiedotukset){
	$this->load->library('Vrl_helper');
	foreach ($tiedotukset as &$tiedotus){
	    if (strlen($tiedotus ['teksti']) > $this->max_list_length){$tiedotus['teksti'] = substr($tiedotus['teksti'], 0, $this->max_list_length)."...";}
		
	    $this->load->model('tunnukset_model');
	    $pinnumber = $this->vrl_helper->vrl_to_number($tiedotus['lahettaja']);
	    $user = $this->ion_auth->user($this->tunnukset_model->get_users_id($pinnumber))->row();
		if ($user == NULL || $user == ""){
			$tiedotus['lahettaja_nick'] = '';
		}
	    else {
	    $tiedotus['lahettaja_nick'] =  $user->nimimerkki;
		}
	}
	
	return $tiedotukset;
		
    }
    
    private function _pagination($page, $items){
	
	$pagination = array();
	if (empty($page) || $page < 1){ $treshold = 0; $page = 1;}
	else { $treshold = ($page - 1)*$this->max_items_per_page;}
	
	$pagination['page'] = $page;
	$pagination['pages'] = ceil($items/$this->max_items_per_page);
	
	$pagination['offset'] = $treshold;
	
	return $pagination;
	
    }
   
}
?>






