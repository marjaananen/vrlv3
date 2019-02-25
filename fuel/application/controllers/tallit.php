<?php
class Tallit extends CI_Controller
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
	
	public function uusimmat (){
		$this->pipari();
	}
	
	public function suosituimmat (){
		$this->pipari();
	}
	
	public function paivitetyt (){
		$this->pipari();
	}
	
	
	
	public function rekisterointi()
    {
		$vars['loggedin'] = $this->ion_auth->logged_in();	
		
		$this->fuel->pages->render('tallit/rekisterointi', $vars);
    }
	
	    public function talliprofiili($tnro="")
    {
		$this->load->model('tallit_model');
		
		if(empty($tnro))
			redirect('/');
		
		if(!$this->tallit_model->is_tnro_in_use($tnro))
			redirect('/');
			
		$vars['stable'] = $this->tallit_model->get_stable($tnro);
		$vars['categories'] = $this->tallit_model->get_stables_categories($tnro);
		$vars['owners'] = $this->tallit_model->get_stables_owners($tnro);
		$vars['likes'] = $this->tallit_model->get_stables_likes($tnro);
		
		if($this->ion_auth->logged_in())
			$vars['liked_date'] = $this->tallit_model->get_stables_like_by_user($tnro, $this->ion_auth->user()->row()->tunnus);
		else
			$vars['liked_date'] = "notset";
		
		
		$this->fuel->pages->render('tallit/profiili', $vars);
    }
	
	
	//functions
    
    function tykkaa($tnro, $yesno)
    {
		$this->load->model('tallit_model');
		
		if(empty($tnro) || empty($yesno) || !$this->ion_auth->logged_in())
			redirect('/');
			
		if($yesno == 1)
			$this->tallit_model->add_stable_like($tnro);
		else if($yesno == -1)
			$this->tallit_model->delete_stable_like($tnro);
			
		redirect($this->input->server('HTTP_REFERER'));
    }
    
    function haku()
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
	
		$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/tallit/haku'));
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
			
			$vars['headers'] = json_encode($vars['headers']);
			
			$vars['data'] = json_encode($this->tallit_model->search_stables($this->input->post('nimi'), $this->input->post('kategoria'), $this->input->post('tallinumero')));
			}
		}
		
		$this->fuel->pages->render('misc/haku', $vars);
    }
}
?>





