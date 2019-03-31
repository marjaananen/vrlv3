<?php
class Virtuaalihevoset extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
	
	//pages
	public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    
	public function index (){
		$this->haku();
	}
	
	public function haku()
    {
		$this->load->model('hevonen_model');
		$this->load->library('form_validation');
		$this->load->library('form_collection');
		$vars['title'] = 'Hevosrekisteri';
		
		$vars['msg'] = 'Hae hevosia rekisteristä. Voit käyttää tähteä * jokerimerkkinä.';
		
		$vars['text_view'] = $this->load->view('hevoset/etusivu_teksti', NULL, TRUE);
		
		$vars['form'] = $this->form_collection->get_horse_search_form();
		
		if($this->input->server('REQUEST_METHOD') == 'POST')
		{
	
			if($this->form_collection->validate_horse_search_form() == true && !(empty($this->input->post('nimi'))));
			{
				$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'reknro', 'key_link' => site_url('virtuaalihevoset/hevonen/'));
				$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
				$vars['headers'][3] = array('title' => 'Rotu', 'key' => 'rotu');
				$vars['headers'][4] = array('title' => 'Sukupuoli', 'key' => 'sukupuoli');
				
				$vars['headers'] = json_encode($vars['headers']);
							
				$vars['data'] = json_encode($this->hevonen_model->search_horse($this->input->post('nimi'), $this->input->post('rotu'), $this->input->post('skp'), $this->input->post('kuollut'), $this->input->post('vari'), $this->input->post('syntynyt_v')));
			}
		}
		
		$this->fuel->pages->render('misc/haku', $vars);
    }
	
	
	public function hevosprofiili ($reknro){
		$this->load->model("hevonen_model");
		$this->load->library("vrl_helper");
		$this->load->library("pedigree_printer");
				
		if(empty($reknro) || !$this->vrl_helper->check_vh_syntax($reknro)){
			ECHO "EI";
		}
		
		$vars = array();
		$vars['hevonen'] = null;
		
		$vars['hevonen'] = $this->hevonen_model->get_hevonen($reknro);
		$vars['suku'] = array();
		$this->hevonen_model->get_suku($reknro, "", $vars['suku']);
		$vars['pedigree_printer'] = & $this->pedigree_printer;
		
		
		$this->fuel->pages->render('hevoset/profiili', $vars);
		

		
	}
	
	
}
?>





