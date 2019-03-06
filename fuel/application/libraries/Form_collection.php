<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Form_collection
{
    private $CI = 0;
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    //moodit ovat: application, edit, admin
    public function get_stable_form($mode, $tnro=-1)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return "";
        
        $this->CI->load->model('tallit_model');
        
        if($mode == 'application')
        {
            $this->CI->load->library('form_builder', array('submit_value' => 'Rekisteröi talli', 'required_text' => '*Pakollinen kenttä'));
    
            $fields['nimi'] = array('type' => 'text', 'required' => TRUE, 'class'=>'form-control');
            $fields['kategoria'] = array('type' => 'select', 'required' => TRUE, 'options' => $this->CI->tallit_model->get_category_option_list(), 'after_html' => '<span class="form_comment">Valitse tallin pääkategoria. Voit lisätä kategorioita lisää myöhemmin.</span>', 'class'=>'form-control');
            $fields['kuvaus'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 3, 'class'=>'form-control');
            $fields['osoite'] = array('type' => 'text', 'required' => TRUE, 'value' => 'http://', 'class'=>'form-control');
            $fields['lyhehd'] = array('type' => 'text', 'label' => 'Lyhenne ehdotus', 'after_html' => '<span class="form_comment">Voit ehdottaa 2-4 merkkistä lyhenteen kirjainosaa tallillesi. Ylläpito ottaa sen huomioon tallinumeroa päätettäessä.</span>', 'class'=>'form-control');
            
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/tallit/rekisteroi'));
        }
        
        if($mode == 'edit' || $mode == 'admin')
        {
            $this->CI->load->library('form_builder', array('submit_value' => 'Muokkaa', 'required_text' => '*Pakollinen kenttä'));
            $stable = $this->CI->tallit_model->get_stable($tnro);
    
            if($mode == 'admin')
            {
                $fields['tallinumero'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['tnro'], 'class'=>'form-control');
            }
    
            $fields['nimi'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['nimi'], 'class'=>'form-control');
            $fields['kuvaus'] = array('type' => 'textarea', 'value' => $stable['kuvaus'], 'cols' => 40, 'rows' => 3, 'class'=>'form-control');
            $fields['osoite'] = array('type' => 'text', 'required' => TRUE, 'value' => $stable['url'], 'class'=>'form-control');
            
            $catvalues = array();
            $cats = $this->CI->tallit_model->get_stables_categories($tnro);
            foreach ($cats as $cat) {
                $catvalues[] = $cat['kategoria'];
            }
            
            
            $fields['kategoria'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE, 'options' => $this->CI->tallit_model->get_category_option_list(), 'value'=>$catvalues, 'class'=>'form-control', 'wrapper_tag' => 'li');

            
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/tallit/muokkaa') . '/' . $tnro);
        }

        return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    public function validate_stable_form($mode)
    {
        if($mode != 'application' && $mode != 'edit' && $mode != 'admin')
            return false;
        
        $this->CI->load->library('form_validation');

        $this->CI->form_validation->set_rules('nimi', 'Nimi', "required|min_length[1]|max_length[128]");
        $this->CI->form_validation->set_rules('kuvaus', 'Kuvaus', "max_length[1024]");
        $this->CI->form_validation->set_rules('osoite', 'Osoite', "required|min_length[4]|max_length[1024]|regex_match[/^[A-Za-z0-9_\-.:,; \/*~#&'@()]*$/]");

        if($mode == 'application')
        {
            $this->CI->form_validation->set_rules('kategoria', 'Kategoria', 'required|min_length[1]|max_length[2]|numeric');
            $this->CI->form_validation->set_rules('lyhehd', 'Lyhenne ehdotus', "min_length[2]|max_length[4]|alpha");
        }
        
        if($mode == 'admin')
        {
            $this->CI->form_validation->set_rules('tallinumero', 'Tallinumero', "required|min_length[6]|max_length[8]|alphanumeric");
            //ei täydellinen check!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        
        return $this->CI->form_validation->run();
    }
    
    
    public function get_stable_search_form(){
        $options = $this->CI-> tallit_model->get_category_option_list();
        $this->CI->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$options[-1] = 'Mikä tahansa';
		
		$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
		$fields['kategoria'] = array('type' => 'select', 'options' => $options, 'value' => '-1', 'class'=>'form-control');
		$fields['tallinumero'] = array('type' => 'text', 'class'=>'form-control');
	
		$this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/tallit/haku'));
		return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    public function validate_stable_search_form(){
        $this->CI->load->library('form_validation');
        
        $this->CI->form_validation->set_rules('nimi', 'Nimi', "min_length[4]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
		$this->CI->form_validation->set_rules('kategoria', 'Kategoria', 'min_length[1]|max_length[2]');
		$this->CI->form_validation->set_rules('tallinumero', 'Tallinumero', "min_length[6]|max_length[8]|regex_match[/^[A-Z0-9]*$/]");
        return $this->CI->form_validation->run();

    }
	
	///////////////// HORSE FORMS
	
	public function get_horse_search_form(){
		$r_options = $this->CI->hevonen_model->get_breed_option_list();
		$r_options[-1] = "Ei väliä";
		$skp_options = $this->CI->hevonen_model->get_gender_option_list();
		$skp_options[-1] = "Ei väliä";
		$color_options = $this->CI->hevonen_model->get_color_option_list();
		$color_options[-1] = "Ei väliä";
		
		$this->CI->load->library('form_builder', array('submit_value' => 'Hae'));

		
		$fields['reknro'] = array('type' => 'text', 'class'=>'form-control');
		$fields['nimi'] = array('type' => 'text', 'class'=>'form-control');
		$fields['rotu'] = array('type' => 'select', 'options' => $r_options, 'value' => '-1', 'class'=>'form-control');
		$fields['skp'] = array('type' => 'select', 'options' => $skp_options, 'value' => '-1', 'class'=>'form-control');
		$fields['kuollut'] = array('type' => 'checkbox', 'checked' => false, 'class'=>'form-control');
		$fields['vari'] = array('type' => 'select', 'options' => $color_options, 'value' => '-1', 'class'=>'form-control');
		$fields['syntynyt_v'] = array('type' => 'text', 'label'=>'Syntymävuosi', 'class'=>'form-control');
		
		$this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/virtuaalihevoset/haku'));
		        
		return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);

	}
	
	
	
	public function validate_horse_search_form(){
        $this->CI->load->library('form_validation');
        
		$this->CI->form_validation->set_rules('skp', 'Sukupuoli', 'min_length[1]|max_length[1]|numeric');
		$this->CI->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->CI->form_validation->set_rules('rotu', 'Rotu', 'min_length[1]|max_length[4]|numeric');
		$this->CI->form_validation->set_rules('vari', 'Väri', 'min_length[1]|max_length[4]|numeric');
		$this->CI->form_validation->set_rules('syntynyt_v', 'Syntymävuosi', 'min_length[4]|max_length[4]|numeric');
        $this->CI->form_validation->set_rules('nimi', 'Nimi', "min_length[4]");
        return $this->CI->form_validation->run();

    }
}



