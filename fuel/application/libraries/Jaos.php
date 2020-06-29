<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Jaos
{
    var $CI;
    
    public function __construct(){
        $this->CI =& get_instance();
         $this->CI->load->model('Jaos_model');

    }
    
	public function get_jaos_form ($url, $mode = "new", $admin = false, $jaos_id = null) {
        
        
        $sport_options = $this->CI->Jaos_model->get_sport_option_list();
		$sport_options[-1] = "";
        $jaos = array();
        
        if($mode == "edit" ){
            $jaos = $this->CI->Jaos_model->get_jaos($jaos_id);
        }

        
		$this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));
		if($mode == "new" || $admin){
            $fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE,'value' => $jaos['nimi'] ?? "" );
            $fields['lyhenne'] = array('type' => 'text', 'class'=>'form-control','required' => TRUE, 'value' => $jaos['lyhenne'] ?? "");
        }
                
        if($mode == "edit"){
            $fields['toiminnassa'] = array('type' => 'checkbox', 'checked' => $jaos['toiminnassa'] ?? false, 'class'=>'form-control', 'after_html' => '<span class="form_comment">Jos jaos ei ole toiminnassa, sen alaisia kilpailuja ei voi järjestää. Tarkasta säännöt ja sallitut luokat ennen jaoksen merkitsemistä toimivaksi.</span>');
        }
        
        $fields['url'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $jaos['url'] ?? "http://");

        $fields['kuvaus'] = array('type' => 'textarea', 'value' => $jaos['kuvaus'] ?? "",'required' => TRUE, 'cols' => 40, 'rows' => 3, 'class'=>'form-control', 'after_html' => '<span class="form_comment">Kuvaus näkyy jaoslistassa VRL:n sivuilla.</span>');

		$fields['laji'] = array('type' => 'select', 'options' => $sport_options, 'value' => $jaos['laji'] ?? -1, 'class'=>'form-control');
		
		$this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
		return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
	}
    
    
   function validate_jaos_form($type = 'new', $admin = false){

        $this->CI->load->library('form_validation');
        
        if($type == 'new' || $admin){
            $this->CI->form_validation->set_rules('nimi', 'Nimi', 'min_length[1]|max_length[45]|required');
            $this->CI->form_validation->set_rules('lyhenne', 'Lyhenne', 'min_length[1]|max_length[10]|required');
        }
      
		$this->CI->form_validation->set_rules('url', 'Url', 'min_length[1]|max_length[360]|required');
        $this->CI->form_validation->set_rules('kuvaus', 'Kuvaus', 'min_length[1]|max_length[740]|required');
       

        return $this->CI->form_validation->run();        
    }
    
    function validate_jaos($type = "new", $admin = false, $jaos, &$msg, $id = null){
                  $this->CI->load->model('Jaos_model');
                  $this->CI->load->model('Sport_model');

       
        if(isset($jaos['lyhenne']) && $this->CI->Jaos_model->is_lyhenne_in_use($jaos['lyhenne'], $id)){
            $msg = "Lyhenne on jo käytössä.";
            return false;
        }
        if(isset($jaos['nimi']) && $this->CI->Jaos_model->is_name_in_use($jaos['nimi'], $id)){
            $msg = "Nimi on jo käytössä.";
            return false;
        }
        
        if(!$this->CI->Sport_model->sport_exists($jaos['laji'])){
                $msg = "Valittua lajia ei ole olemassa";
                return false;
        }
            
        return true;
    }
    
    function read_jaos_input(){
      $jaos = array();
      $jaos['nimi'] = $this->CI->input->post("nimi");
      $jaos['lyhenne'] = $this->CI->input->post("lyhenne");
      $jaos['laji'] = $this->CI->input->post("laji");
      if($this->CI->input->post("toiminnassa")){
         $jaos['toiminnassa'] = $this->CI->input->post("toiminnassa");
      }
      else {
         $jaos['toiminnassa'] = false;
         }
      $jaos['url'] = $this->CI->input->post("kuvaus");

      return $jaos;
      
    }
    
    
    function jaostaulukko($url_poista, $url_muokkaa){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
      $vars['headers'][4] = array('title' => 'Toiminnassa', 'key' => 'toiminnassa');
       $vars['headers'][5] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][6] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png'));
  
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->CI->Jaos_model->get_jaos_list());
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
	
    
    
    
    function get_rules_form(){
        /*$fields['s_salli_porrastetut'] = array('type' => 'enum', 'mode' => 'radios', 'value' => $jaos['s_salli_porrastetut'],
                                               'label' => "Salli porrastetut", 'options' => array(0=>"Ei", 1=>"Kyllä"), 'class'=>'form-control',
                                               'after_html' => '<span class="form_comment">Salli porrastetut kilpailut. Huom! Valitse myös porrastetut luokat. </span>');
                                               */
        $fields['toiminnassa'] = array('type' => 'checkbox', 'checked' => $jaos['toiminnassa'] ?? false, 'class'=>'form-control', 'after_html' => '<span class="form_comment">Jos jaos ei ole toiminnassa, sen alaisia kilpailuja ei voi järjestää, ja ainoastaan jaosvastaava voi muokata sen tietoja.</span>');
   
        $fields['number'] = array('type' => 'number', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => TRUE, 'decimal' => TRUE);

    }
    
    function validate_rules_form(){
        
    }
    
    function delete_jaos($id, &$msg){
      //todo: tsekkaa onko kisoja jne
      return $this->CI->Jaos_model->delete_jaos($id, $msg);
    }
    
    
}
    
?>