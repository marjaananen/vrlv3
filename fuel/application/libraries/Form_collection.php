<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Form_collection
{
    private $CI = 0;
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    //moodit ovat: application, edit, admin
    public function get_stable_form($mode)
    {
        $this->CI->load->library('form_builder', array('submit_value' => 'Rekisteröi talli', 'required_text' => '*Pakollinen kenttä'));
        $this->CI->load->model('tallit_model');

        $fields['nimi'] = array('type' => 'text', 'required' => TRUE, 'class'=>'form-control');
        $fields['kategoria'] = array('type' => 'select', 'required' => TRUE, 'options' => $this->CI->tallit_model->get_category_option_list(), 'after_html' => '<span class="form_comment">Valitse tallin pääkategoria. Voit lisätä kategorioita lisää myöhemmin.</span>', 'class'=>'form-control');
        $fields['kuvaus'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 3, 'class'=>'form-control');
        $fields['osoite'] = array('type' => 'text', 'required' => TRUE, 'value' => 'http://', 'class'=>'form-control');
        
        $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/omat-tallit/rekisteroi'));

        return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
}



