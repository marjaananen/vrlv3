<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Jaos
{
    var $CI;
    
    public function __construct(){
        $this->CI =& get_instance();
         $this->CI->load->model('Jaos_model');
         $this->CI->load->model('Sport_model');

    }
    
    function jaostaulukko($url_poista, $url_muokkaa, $url_tapahtumat){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
      $vars['headers'][4] = array('title' => 'Toiminnassa', 'key' => 'toiminnassa');
      $vars['headers'][5] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      $vars['headers'][6] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png'));
      $vars['headers'][7] = array('title' => 'Tapahtumat', 'key' => 'id', 'key_link' => site_url($url_tapahtumat), 'image' => site_url('assets/images/icons/award_star_gold_2.png'));

		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->CI->Jaos_model->get_jaos_list());
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
    
      function tapahtumataulukko ($jaos, $url_poista, $url_muokkaa){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Päivämäärä', 'key' => 'pv', 'type'=>'date');
		$vars['headers'][3] = array('title' => 'Otsikko', 'key' => 'otsikko');
      $vars['headers'][4] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png'));

		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->CI->Jaos_model->get_event_list($jaos));
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
      function tapahtumaosallistujat ($id, $url_poista){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'oid');
		$vars['headers'][2] = array('title' => 'Rekisterinumero', 'key' => 'vh', 'type'=>'VH', 'key_link'=>site_url('virtuaalihevoset/hevonen/'));
		$vars['headers'][3] = array('title' => 'Tulos', 'key' => 'tulos');
      $vars['headers'][4] = array('title' => 'Palkinto', 'key' => 'palkinto');
      $vars['headers'][5] = array('title' => 'Kommentti', 'key' => 'kommentti', 'type'=>'small');
      $vars['headers'][6] = array('title' => 'Poista', 'key' => 'oid', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));

		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->CI->Jaos_model->get_event_horses($id));
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
   function delete_jaos($id, &$msg){
      $jaos = $this->CI->Jaos_model->get_jaos($id);
      if(sizeof($jaos) > 0){
         $msg = "Jaosta ei ole olemassa.";
         return false;
      }else if($jaos['toiminnassa'] === "1"){
          $msg = "Et voi poistaa toiminnassa olevaa jaosta.";
          return false;
      }else {
         //todo: tsekkaa onko kisoja jne
         return $this->CI->Jaos_model->delete_jaos($id, $msg);
      }
    }
    
    
	public function get_jaos_form ($url, $mode = "new", $admin = false, $jaos = array()) {
        
        
        $sport_options = $this->CI->Sport_model->get_sport_option_list();
         $sport_options[-1] = "";        

        
		$this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));
		if($mode == "new" || $admin){
            $fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE,'value' => $jaos['nimi'] ?? "" );
            $fields['lyhenne'] = array('type' => 'text', 'class'=>'form-control','required' => TRUE, 'value' => $jaos['lyhenne'] ?? "");
            $fields['laji'] = array('type' => 'select', 'options' => $sport_options, 'value' => $jaos['laji'] ?? -1, 'class'=>'form-control');

        }
            
        
        $fields['url'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $jaos['url'] ?? "http://");
        $fields['kuvaus'] = array('type' => 'textarea', 'value' => $jaos['kuvaus'] ?? "",'required' => TRUE, 'cols' => 40, 'rows' => 3, 'class'=>'form-control', 'after_html' => '<span class="form_comment">Kuvaus näkyy jaoslistassa VRL:n sivuilla.</span>');
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
        
        if(isset($jaos['laji']) && !$this->CI->Sport_model->sport_exists($jaos['laji'])){
                $msg = "Valittua lajia ei ole olemassa";
                return false;
        }
        
        if($type == "edit"){
            $old_jaos = $this->CI->Jaos_model->get_jaos($id);
            
            if($old_jaos['toiminnassa'] && isset($jaos['laji']) && $jaos['laji'] != $old_jaos['laji']){
               $msg = "Jaos on toiminnassa, joten sen lajia ei saa vaihtaa.";
               return false;
            }
            $luokat = $this->CI->Jaos_model->get_class_list($id, false);
            if(sizeof($luokat)> 0 && isset($jaos['laji']) && $jaos['laji'] != $old_jaos['laji']){
               $msg = "Jaokselle on merkitty luokkia, joten sen lajia ei saa vaihtaa.";
               return false;
            }

            
        }
        return true;
    }
    
   function read_jaos_input(&$jaos){
      if($this->CI->input->post("nimi")){
          $jaos['nimi'] = $this->CI->input->post("nimi");
      }
      if($this->CI->input->post("lyhenne")){
         $jaos['lyhenne'] = $this->CI->input->post("lyhenne");
      }
      if($this->CI->input->post("laji")){
         $jaos['laji'] = $this->CI->input->post("laji");
      }
      if($this->CI->input->post("toiminnassa")){
         $jaos['toiminnassa'] = $this->CI->input->post("toiminnassa");
      }
      else {
         $jaos['toiminnassa'] = false;
         }
      $jaos['url'] = $this->CI->input->post("url");
      $jaos['kuvaus'] = $this->CI->input->post("kuvaus");


      return $jaos;
      
    }
    
    ////////////////////////////////////////////////////////
    // TOIMINNASSA
    ///////////////////////////////////////////////////////
    
    function get_toiminnassa_form($url, $jaos){
         $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

         $fields = array();
         $fields['toiminnassa'] = array('type' => 'checkbox', 'label'=> "Toiminnassa", 'checked' => $jaos['toiminnassa'] ?? false, 'class'=>'form-control',
                                        'after_html' => '<span class="form_comment">Jos jaos ei ole toiminnassa, sen alaisia kilpailuja ei voi järjestää. Tarkasta säännöt ja sallitut luokat ennen jaoksen merkitsemistä toimivaksi.</span>');
         $fields['s_salli_porrastetut'] = array('type' => 'checkbox', 'label'=> "Salli porrastetut", 'checked' => $jaos['s_salli_porrastetut'] ?? false, 'class'=>'form-control',
                                                'after_html' => '<span class="form_comment">Jos jaos ei salliporrastettuja, niitä ei voi järjestää. Tarkasta säännöt, sallitut luokat ja vaikuttavat ominaisuudet ennen porrastettujen sallimista.
                                                <b>Vaikuttavia ominaisuuksia ei voi enää muokata kun porrastetut on asetettu sallituksi!</b></span>');
         
          $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);

    }
    
   function read_toiminnassa_input(&$jaos){
      $jaos['toiminnassa'] = 0;
      $jaos['s_salli_porrastetut'] = 0;
      
      
      if($this->CI->input->post('toiminnassa')){
            $jaos['toiminnassa'] = 1;
         }
      if($this->CI->input->post('s_salli_porrastetut')){
            $jaos['s_salli_porrastetut'] = 1;
         }
      
      return $jaos;
      
    }
    
    function validate_toiminnassa_form($id, $toiminnassa, &$msg){
      $jaos = $this->CI->Jaos_model->get_jaos($id);
      
      if($jaos['toiminnassa'] === $toiminnassa['toiminnassa'] && $jaos['s_salli_porrastetut'] === $toiminnassa['s_salli_porrastetut']){
         $msg = "Et muuttanut asetuksia.";
         return false;
      }
      
      $ok = true;
      //jaos offlinesta onlineen
      if($toiminnassa['toiminnassa'] === '1'){
         $ok = $this->jaos_ready($id, $msg);
         //jos porrastetut on myös menossa online, tsekataan nekin
         if ($ok && $toiminnassa['s_salli_porrastetut'] === '1'){
            $ok = $this->jaos_porr_ready($id, $msg);
         }
      }else if ($toiminnassa['s_salli_porrastetut'] === '1'){
         $ok = $this->jaos_porr_ready($id, $msg);
      }
      
      return $ok;
    }
    
    function jaos_ready($id, &$msg){
      $ok = true;
         $luokat = $this->get_classes($id);
         $owners = $this->get_owners($id);       
         if(sizeof($luokat) < 1){
            $msg .= " Jaokselle ei ole vielä asetettu luokkia!";
            $ok = false;
         } if(sizeof($omistajat) < 1){
            $msg .= " Jaokselle ei ole vielä ylläpitäjiä!";
            $ok = false;
         }
         return $ok;
    }
    
    function jaos_porr_ready($id, &$msg){
      $ok = true;
      $jaos = $this->CI->Jaos_model->get_jaos($id);     
         $luokat = $this->get_classes_porr($id);
         $traits = $this->get_traits($id);       
         if(sizeof($luokat) < 1){
            $msg .= " Jaokselle ei ole vielä asetettu porrastettuja luokkia!";
            $ok = false;
         } if(sizeof($traits) < 1){
               $msg .= " Jaokselle ei ole vielä valittu porrastettujen ominaisuuksia!";
            $ok = false;
         }         
         return $ok;
    }
    
    

   ////////////////////////////////////////////////////////
    // SÄÄNNÖT
    ///////////////////////////////////////////////////////
    
    
    function get_rules_form($url, $jaos){
      $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

      $fields = array();
      $fields['s_luokkia_per_kisa_min'] = array('label' => 'Luokkamäärä/kisa (min)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_kisa_min'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_luokkia_per_kisa_max'] = array('label' => 'Luokkamäärä/kisa (max)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_kisa_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $fields['s_hevosia_per_luokka_min'] = array('label' => 'Hevosia/luokka/ratsastaja (min)', 'type' => 'number', 'value' => $jaos['s_hevosia_per_luokka_min'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_hevosia_per_luokka_max'] = array('label' => 'Hevosia/luokka/ratsastaja (max)', 'type' => 'number', 'value' => $jaos['s_hevosia_per_luokka_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $fields['s_luokkia_per_hevonen_min'] = array('label' => 'Luokkia/hevonen/kisa (min)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_hevonen_min'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_luokkia_per_hevonen_max'] = array('label' => 'Luokkia/hevonen/kisa (max)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_hevonen_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    var $rules_array = array("s_luokkia_per_kisa_max" => 0,
                             "s_luokkia_per_kisa_min" => 0,
                             "s_hevosia_per_luokka_max"=>0,
                             "s_hevosia_per_luokka_min"=>0,
                             "s_luokkia_per_hevonen_min"=>0,
                             "s_luokkia_per_hevonen_max"=>0);
    
    function validate_rules_form(){
    
      $this->CI->load->library('form_validation');      
      foreach($this->rules_array as $rule=>$value){
         $this->CI->form_validation->set_rules($rule, $rule, 'min_length[1]|max_length[3]|numeric');
      }
        return $this->CI->form_validation->run();
    }
    
   function read_rules_input(&$jaos){      
      foreach($this->rules_array as $rule=>$value){
         if($this->CI->input->post($rule)){
            $jaos[$rule] = $this->CI->input->post($rule);
         }
      }   
      return $jaos;
      
    }
    
    function check_rules_form($jaos, &$msg){
      if($jaos['s_luokkia_per_kisa_min']<0
         ||$jaos['s_hevosia_per_luokka_min'] <0
         || $jaos['s_luokkia_per_hevonen_min'] <0){
          $msg = "Arvot eivät saa olla negatiivisia!";
         return false;
      }
      
      else if(($jaos['s_luokkia_per_kisa_max']<$jaos['s_luokkia_per_kisa_min'])
         ||($jaos['s_hevosia_per_luokka_max']<$jaos['s_hevosia_per_luokka_min'])
         ||($jaos['s_luokkia_per_hevonen_max']<$jaos['s_luokkia_per_hevonen_min'])){
         $msg = "Maksimin pitää olla aina suurempi kuin minimin!";
         return false;
      }
      
      else if($jaos['s_hevosia_per_luokka_max'] > 100){
         $msg = "Yli 100 hevosen luokkia ei saa järjestää.";
         return false;
      }
      else if($jaos['s_hevosia_per_luokka_min'] < 30){
         $msg = "Luokan minimikoko pitää olla 30 tai yli.";
         return false;
      }
      return true;
      
        
    }
    

    
    
    ////////////////////////////////////////////////////////
    // LUOKAT
    ///////////////////////////////////////////////////////
    
    
    
   function luokkataulukko($id, $url_poista, $url_muokkaa){
      //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Järj. nro', 'key' => 'jarjnro');
		$vars['headers'][3] = array('title' => 'Nimi', 'key' => 'nimi');
      $vars['headers'][4] = array('title' => 'Porras- tettu', 'key' => 'porrastettu');
      $vars['headers'][5] = array('title' => 'Käy- tössä', 'key' => 'kaytossa');
      $vars['headers'][6] = array('title' => 'Vaik. taso', 'key' => 'taso');
      $vars['headers'][7] = array('title' => 'Aste', 'key' => 'aste');
      $vars['headers'][8] = array('title' => 'Min. säkä', 'key' => 'minheight');
      $vars['headers'][9] = array('title' => '&nbsp;', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      $vars['headers'][10] = array('title' => '&nbsp;', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png')); 
		$vars['headers'] = json_encode($vars['headers']);				
		$vars['data'] = json_encode($this->CI->Jaos_model->get_class_list($id, false));
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);       
    }
    
    
    function get_class_form($url, $class){
      $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));
      $aste_options = array(0=>"",1=> "Seurataso", 2=>"Aluetaso", 3=> "Kansallinen taso");

      $fields = array();
      $fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $class['nimi'] ?? "");
      $fields['porrastettu'] = array('type' => 'checkbox', 'label'=> "Porrastettu", 'checked' => $class['porrastettu'] ?? false, 'class'=>'form-control');
      $fields['jarjnro'] = array('label' => 'Järjestysnumero', 'type' => 'number', 'value' => $class['jarjnro'] ?? 999, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE,
                                 'after_html' => '<span class="form_comment">Luokat listataan säännöissä ja kisoissa järjestyksessä tämän mukaan (pienin ylimpänä). Jos kahdella luokalla on sama numero, esitetään aakkosjärjestyksessä.</span>');    
      $fields['kaytossa'] = array('type' => 'checkbox', 'label'=> "Käytössä", 'checked' => $class['kaytossa'] ?? true, 'class'=>'form-control',
                                  'after_html' => '<span class="form_comment">Vain käytössä oleviksi merkityt luokat näkyvät jaoksen säännöissä. </span>');
    
      $fields['porastettu_info'] = array('type'=>'hidden', 'before_html' => '</div></div></div><div class="panel panel-default"><div class="panel-heading">Porrastetun luokan tiedot (vain porrastetuille)</div> <div class="panel-body"><div class="form-group">');

      $fields['taso'] = array('label' => 'Taso (0-10)', 'type' => 'number', 'value' => $class['taso'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE,
                              'after_html' => '<span class="form_comment">Taso vaikuttaa myös osallistujan minimi-ikään!</span>');    
      $fields['aste'] = array('type' => 'select', 'options' => $aste_options, 'value' => $class['aste'] ?? -1, 'class'=>'form-control');
      $fields['minheight'] = array('label' => 'Minimisäkäkorkeus', 'type' => 'number', 'value' => $class['minheight'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);    
              
      $fields['end'] = array('type'=>'hidden', 'after_html' => '</div>');



      $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    function read_class_input(&$class){
         $class['nimi'] = $this->CI->input->post('nimi');
         if($this->CI->input->post('porrastettu')){
            $class['porrastettu'] = $this->CI->input->post('porrastettu');
            $class['taso'] = $this->CI->input->post('taso');
            $class['aste'] = $this->CI->input->post('aste');
            $class['minheight'] = $this->CI->input->post('minheight');
         }else {
            $class['porrastettu'] = 0;
         }
         $class['jarjnro'] = $this->CI->input->post('jarjnro');
         if($this->CI->input->post('kaytossa')){
            $class['kaytossa'] = $this->CI->input->post('kaytossa');
         }else {
            $class['kaytossa'] = 0;
         }
    }
    
    function validate_class_form(){
      $this->CI->load->library('form_validation');
      $this->CI->form_validation->set_rules('nimi', 'Nimi', 'min_length[1]|max_length[45]|required');
      $this->CI->form_validation->set_rules('jarjnro', 'Järjestysnumero', 'min_length[1]|max_length[3]|numeric|required');
      $this->CI->form_validation->set_rules('taso', 'Taso', 'min_length[1]|max_length[3]|numeric');
      $this->CI->form_validation->set_rules('aste', 'Aste', 'min_length[1]|max_length[3]|numeric');
      $this->CI->form_validation->set_rules('minheight', 'Minimikorkeus', 'min_length[1]|max_length[3]|numeric');
      
      return $this->CI->form_validation->run();
      
    }
    
    function validate_class($type="add", $admin, $class, $msg){
      return true;
    }
    
    function delete_class($id, $jaos_id, &$msg){
      //TODO: Katso kisaluokat
      return $this->CI->Jaos_model->delete_class($id, $jaos_id);
      
    }
    
   ////////////////////////////////////////////////////////
    // TAPAHTUMAT
    ///////////////////////////////////////////////////////
    
     function get_event_form($url, $event=array(), $osallistujat = true){
      $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna tapahtuma'));
      $this->CI->load->library("vrl_helper");
      
      if(isset($event['pv'])){
         $event['pv'] = $this->CI->vrl_helper->sql_date_to_normal($event['pv']);
      }

      $fields = array();
      $fields['otsikko'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['otsikko'] ?? "");
      $fields['pv'] = array('type' => 'text', 'label'=>'Päivämäärä', 'class'=>'form-control', 'required' => TRUE, 'value' => $event['pv'] ?? "",
                            'after_html'=>'<span class="form_comment">Muodossa pp.kk.vvvv</span>');
      if($osallistujat){
      $fields['osallistujat'] = array('label'=>"Lisää osallistujia", 'type' => 'textarea', 'cols' => 80, 'rows' => 5, 'class' => 'wysiwyg',
                                      'value' => $event['osallistujat'] ?? "VH00-000-00000;70;KERJ-III;16 + 24 + 16,5 + 5,5 = 70 p. Esimerkkikommentti.	",
                                      'after_html'=>'<span class="form_comment">Yksi hevonen per rivi. Erottele puolipisteellä seuraavasti.  VH-numero; pisteet; palkinto; kommentti. <br>Kaikki viimeisen puolipisteen jälkeen kirjattu lasketaan kommentiksi. Jos kommenttia ei ole, jätä tyhjäksi (mutta rivin pitää silti sisältää kolme puolipistettä (;).</span>');
      }

      $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
      return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    
    function add_event($user, $jaos, $date, $title, $participant_data){
      return $this->CI->Jaos_model->add_event($date, $title, $user, $jaos, $participant_data);
    }
    
    function edit_event($id, $jaos, $title, $date){
      return $this->CI->Jaos_model->edit_event($id, $jaos, $title, $date);
    }

    
   function add_participants($id, $participant_data){
      return $this->CI->Jaos_model->add_event_participants($id, $participant_data);
    }

    
   ////////////////////////////////////////////////////////
    // OMINAISUUDET
    ///////////////////////////////////////////////////////
    
    function get_trait_form($url, $ominaisuudet = array()){
            $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

      		$fields['ominaisuudet'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE, 'options' => $this->CI->Trait_model->get_trait_option_list(), 'value'=>$ominaisuudet, 'class'=>'form-control', 'wrapper_tag' => 'li');
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
            return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);


    }
    
   function read_trait_input(&$traits){
      $traits = $this->CI->input->post('ominaisuudet');
    }
    
    function validate_trait_form($id, $traits, $jaos, &$msg){
      if($jaos['toiminnassa'] && $jaos['s_salli_porrastetut']){
         $msg = "Et voi muokata ominaisuuksia jos jaos on toiminnassa ja porrastetut ovat käynnissä!";
         return false;
      }
      if(sizeof($traits) < 2){
         $msg = "Ominaisuuksia pitää olla vähintään 2!";
         return false;
      }
      if(sizeof($traits) > 4){
         $msg = "Ominaisuuksia saa valita korkeintaan 4!";
         return false;
      }
      
      foreach ($traits as $trait){
         if(!$this->CI->Trait_model->trait_exists($trait)){
            $msg = "Ominaisuutta ei ole.";
            return false;
            break;
         }
      }
      
      return true;
    }
}
    
    
    
?>