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
        $this->yllapito();
    }
    

	
	function yllapito ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->load->model("Oikeudet_model");
        $vars['user'] = array();
        $vars['users']['admin'] = $this->_get_groups_users("admin", "Ylläpitotiimi");
        $vars['users']['tunnukset'] = $this->_get_groups_users("tunnukset", "Tunnusvastaava");
        $vars['users']['jaos'] = $this->_get_groups_users("jaos", "Jaosvastaava");
        $vars['users']['tyovoima'] = $this->_get_groups_users("tyovoima", "Työvoimavastaava");
        $vars['users']['hevosrekisteri'] = $this->_get_groups_users("hevosrekisteri", "Hevosrekisteritiimi");
        $vars['users']['tallirekisteri'] = $this->_get_groups_users("tallirekisteri", "Tallirekisteritiimi");
        $vars['users']['kasvattajanimet'] = $this->_get_groups_users("kasvattajanimet", "Kasvattajanimirekisteritiimi");


    
        
        $this->fuel->pages->render('liitto/yllapito', $vars);
    }
    
    private function _get_groups_users($group_name, $desc){
        $users = array();
        if($this->Oikeudet_model->does_user_group_exist_by_name($group_name)){
            $users = $this->Oikeudet_model->users_in_group_name($group_name);
            
        }else {
            $this->ion_auth->create_group($group_name, $desc);   
        }
        return $users;
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
     $vars = $this->_announcement_menu($this->Uutiset_model->hae_tiedotukset_kpl());

	
	$tiedotukset = $this->Uutiset_model->hae_tiedotukset($this->max_items_per_page, $vars['pagination']['offset']);
	$vars['tiedotukset'] = $this->_kasittele_tiedotukset($tiedotukset);
	
	
	$this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
    }
    

    
    
    function kategoria($kat)
    {
        $kategoria = $this->Uutiset_model->hae_kategoria_info($kat);
        
        if(sizeof($kategoria) > 0){
            $vars = $this->_announcement_menu($this->Uutiset_model->hae_kategoria_kpl($kat));
            
            $tiedotukset = $this->Uutiset_model->hae_kategoria($kat, $this->max_items_per_page, $vars['pagination']['offset']);
            $vars['tiedotukset'] = $this->_kasittele_tiedotukset($tiedotukset);
            $vars['header'] = "#".$kategoria['kategoria'];
        
            $this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kategoriaa ei löydy!'));
    
        }
    }

    
    function tiedotus($tid)
    {
	
        
        $tiedotukset = $this->Uutiset_model->hae_tiedotus($tid);
        
        if(sizeof($tiedotukset) > 0){
            $vars = $this->_announcement_menu(null);
            $vars['tiedotukset'] = $tiedotukset;
            $vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
        
            $this->fuel->pages->render('tiedotukset/tiedotukset', $vars);
        }
        
        else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Tiedotetta ei löydy!'));
    
            }
    }
    
    function arkisto($v, $m = -1)
    {
	$vars = $this->_announcement_menu($this->Uutiset_model->hae_tiedotukset_kpl($v, $m), $v);
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
	    if (strlen($tiedotus ['teksti']) > $this->max_list_length){
            $tiedotus['teksti'] = substr($tiedotus['teksti'], 0, $this->max_list_length)."...";
            }
		
	}
	
	return $tiedotukset;
		
    }
    private function _announcement_menu($kpl, $v = null){
        $page = $this->input->get('sivu', TRUE);
           $vars = array();
        $vars['pagination'] = $this->_pagination($page, $kpl);
        $vars['years'] = $this->Uutiset_model->hae_vuodet_amount();
        $vars['selected_year'] = $v;   
        $vars['tag_cloud'] =  $this->Uutiset_model->tiedotus_tag_cloud_json();
        
        return $vars;
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






