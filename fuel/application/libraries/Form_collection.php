<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Form_collection
{
    private $CI = 0;
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    //moodit ovat: application, edit, admin

	
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



