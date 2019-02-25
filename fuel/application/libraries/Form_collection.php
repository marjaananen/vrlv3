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
            
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/omat-tallit/rekisteroi'));
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
            $fields['kategoria'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE, 'options' => $this->CI->tallit_model->get_category_option_list(), 'class'=>'form-control', 'wrapper_tag' => 'li');

            
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/omat-tallit/muokkaa') . '/' . $tnro . '/' . $mode);
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
    
    public function get_stable_category_form($tnro)
    {
        $this->CI->load->model('tallit_model');

        $this->CI->load->library('form_builder', array('submit_value' => 'Ano kategoria', 'required_text' => '*Pakollinen kenttä'));

        $fields['tallinumero'] = array('type' => 'text', 'required' => TRUE, 'readonly' => true, 'value' => $tnro, 'class'=>'form-control');
        $fields['kategoria'] = array('type' => 'select', 'required' => TRUE, 'options' => $this->CI->tallit_model->get_category_option_list(), 'class'=>'form-control');

        $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili/rekisteroi_kategoria') . '/' . $tnro);

        return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    public function validate_stable_category_form($tnro)
    {
        $this->CI->load->model('tallit_model');
        $this->CI->load->library('form_validation');

        $this->CI->form_validation->set_rules('kategoria', 'Kategoria', 'required|min_length[1]|max_length[2]|numeric');
        
        if($this->CI->input->post('tallinumero') != $tnro)
            return false;
        
        if(!$this->CI->tallit_model->is_stable_owner($this->CI->ion_auth->user()->row()->tunnus, $tnro))
            return false;
        
        if($this->CI->tallit_model->stable_has_category($tnro, $this->CI->input->post('kategoria')))
            return false;
        
        return $this->CI->form_validation->run();
    }
}



