<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Jaos
{
    var $CI;
    
    public function __construct(){
        $this->CI =& get_instance();
         $this->CI->load->model('Jaos_model');
         $this->CI->load->model('Sport_model');

    }
    
    function jaostaulukko($url_poista, $url_muokkaa, $url_tapahtumat, $pulju = false){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Lyhenne', 'key' => 'lyhenne');
      $vars['headers'][4] = array('title' => 'Toiminnassa', 'key' => 'toiminnassa');
      if($pulju){
               $vars['headers'][5] = array('title' => 'Tyyppi', 'key' => 'tyyppi');

      }else {
         $vars['headers'][5] = array('title' => 'Tyyppi', 'key' => 'nayttelyt');
      }
      $vars['headers'][6] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      $vars['headers'][7] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png'));
      $vars['headers'][8] = array('title' => 'Tapahtumat', 'key' => 'id', 'key_link' => site_url($url_tapahtumat), 'image' => site_url('assets/images/icons/award_star_gold_2.png'));

		$vars['headers'] = json_encode($vars['headers']);
		if($pulju){
         $vars['data'] = json_encode($this->CI->Jaos_model->get_pulju_list());

      }
      else {
         $vars['data'] = json_encode($this->CI->Jaos_model->get_jaos_list(false, false, false));
      }
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
    

    
      
    
   function delete_jaos($id, &$msg){
      $jaos = $this->CI->Jaos_model->get_jaos($id);
      if(sizeof($jaos) == 0){
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
    
      function delete_pulju($id, &$msg){
      $jaos = $this->CI->Jaos_model->get_pulju($id);
      if(sizeof($jaos) == 0){
         $msg = "Yhdistystä ei ole olemassa.";
         return false;
      }else if($jaos['toiminnassa'] === "1"){
          $msg = "Et voi poistaa toiminnassa olevaa yhdistystä.";
          return false;
      }else {
         //todo: tsekkaa onko kisoja jne
         return $this->CI->Jaos_model->delete_pulju($id, $msg);
      }
    }
    
    
	public function get_jaos_form ($url, $mode = "new", $admin = false, $jaos = array(), $pulju = false) {

        
		$this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));
		if($mode == "new" || $admin){
            $fields['nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE,'value' => $jaos['nimi'] ?? "" );
            $fields['lyhenne'] = array('type' => 'text', 'class'=>'form-control','required' => TRUE, 'value' => $jaos['lyhenne'] ?? "");
            
            if(!$pulju){
               $sport_options = $this->CI->Sport_model->get_sport_option_list();
               $sport_options[-1] = "";
               $fields['laji'] = array('type' => 'select', 'options' => $sport_options, 'value' => $jaos['laji'] ?? -1, 'class'=>'form-control');
               $fields['nayttelyt'] = array('label'=> 'Näyttelyjaos', 'type' => 'checkbox', 'checked' => $jaos['nayttelyt'] ?? false, 'class'=>'form-control');
            }else {
               $tyypit_options = $this->CI->Jaos_model->get_pulju_type_option_list();
               $tyypit_options[-1] = "";
               $fields['tyyppi'] = array('type' => 'select', 'options' => $tyypit_options, 'value' => $jaos['tyyppi'] ?? -1, 'class'=>'form-control');

            }


        }
            
        
        $fields['url'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $jaos['url'] ?? "http://");
        $fields['kuvaus'] = array('type' => 'textarea', 'value' => $jaos['kuvaus'] ?? "",'required' => TRUE, 'cols' => 40, 'rows' => 3, 'class'=>'form-control', 'after_html' => '<span class="form_comment">Kuvaus näkyy jaoslistassa VRL:n sivuilla.</span>');
		  $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
	}
    
    
   function validate_jaos_form($type = 'new', $admin = false, $pulju = false){

        $this->CI->load->library('form_validation');
        
        if($type == 'new' || $admin){
            $this->CI->form_validation->set_rules('nimi', 'Nimi', 'min_length[1]|max_length[45]|required');
            $this->CI->form_validation->set_rules('lyhenne', 'Lyhenne', 'min_length[1]|max_length[10]|required');
        }
      
		$this->CI->form_validation->set_rules('url', 'Url', 'min_length[1]|max_length[360]|required');
        $this->CI->form_validation->set_rules('kuvaus', 'Kuvaus', 'min_length[1]|max_length[740]|required');
       

        return $this->CI->form_validation->run();        
    }
    
    function validate_jaos($type = "new", $admin = false, $jaos, &$msg, $pulju = false, $id = null){
                  $this->CI->load->model('Jaos_model');
                  $this->CI->load->model('Sport_model');

       
        if(isset($jaos['lyhenne']) && $this->CI->Jaos_model->is_lyhenne_in_use($jaos['lyhenne'], $id, $pulju)){
            $msg = "Lyhenne on jo käytössä.";
            return false;
        }
        if(isset($jaos['nimi']) && $this->CI->Jaos_model->is_name_in_use($jaos['nimi'], $id, $pulju)){
            $msg = "Nimi on jo käytössä.";
            return false;
        }
        
        if(!$pulju && isset($jaos['laji']) && !$this->CI->Sport_model->sport_exists($jaos['laji'])){
                $msg = "Valittua lajia ei ole olemassa";
                return false;
        }
        
        if($type == "edit"){
         if(!$pulju){
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

            
        }
        return true;
    }
    
   function read_jaos_input(&$jaos, $pulju = false){
      if($this->CI->input->post("nimi")){
          $jaos['nimi'] = $this->CI->input->post("nimi");
      }
      if(!$pulju && $this->CI->input->post("nayttelyt")){
          $jaos['nayttelyt'] = $this->CI->input->post("nayttelyt");
      }else if(!$pulju){
         $jaos['nayttelyt'] = 0;
      }
      if($this->CI->input->post("lyhenne")){
         $jaos['lyhenne'] = $this->CI->input->post("lyhenne");
      }
      if(!$pulju && $this->CI->input->post("laji")){
         $jaos['laji'] = $this->CI->input->post("laji");
      }
      if($pulju && $this->CI->input->post("tyyppi")){
         $jaos['tyyppi'] = $this->CI->input->post("tyyppi");
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
    
    function get_toiminnassa_form($url, $jaos, $pulju = false){
         $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

         $fields = array();
         $fields['toiminnassa'] = array('type' => 'checkbox', 'label'=> "Toiminnassa", 'checked' => $jaos['toiminnassa'] ?? false, 'class'=>'form-control',
                                        'after_html' => '<span class="form_comment">Jos jaos ei ole toiminnassa, sen alaisia kilpailuja ei voi järjestää. Tarkasta säännöt ja sallitut luokat ennen jaoksen merkitsemistä toimivaksi.</span>');
         
         if($pulju == false && (!isset($jaos['nayttelyt']) || $jaos['nayttelyt'] == 0)){
            $fields['s_salli_porrastetut'] = array('type' => 'checkbox', 'label'=> "Salli porrastetut", 'checked' => $jaos['s_salli_porrastetut'] ?? false, 'class'=>'form-control',
                                                'after_html' => '<span class="form_comment">Jos jaos ei salli porrastettuja, niitä ei voi järjestää. Tarkasta säännöt, sallitut luokat ja vaikuttavat ominaisuudet ennen porrastettujen sallimista.
                                                <b>Vaikuttavia ominaisuuksia ei voi enää muokata kun porrastetut on asetettu sallituksi!</b></span>');
         }
          $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);

    }
    
   function read_toiminnassa_input(&$jaos, $pulju = false){
      $jaos['toiminnassa'] = 0;
      $jaos['s_salli_porrastetut'] = 0;
      
      
      if($this->CI->input->post('toiminnassa')){
            $jaos['toiminnassa'] = 1;
         }
      if($this->CI->input->post('s_salli_porrastetut')){
            $jaos['s_salli_porrastetut'] = 1;
         }
         
      if($pulju){
         unset($jaos['s_salli_porrastetut']);
      }
      
      return $jaos;
      
    }
    
    function validate_toiminnassa_form($id, $toiminnassa, &$msg, $pulju = false){
      $jaos = array();
      
      if($pulju){
         $jaos = $this->CI->Jaos_model->get_pulju($id);
      }else {
         $jaos = $this->CI->Jaos_model->get_jaos($id);
      }
      
      $t_muuttuu = false;
      $p_muuttuu = false;
      
      if (isset($jaos['toiminnassa']) && $jaos['toiminnassa'] != $toiminnassa['toiminnassa']){
         $t_muuttuu = true;
      }
      
      if (!$pulju && $jaos['nayttelyt'] != 1 &&
          isset($jaos['s_salli_porrastetut']) && $jaos['s_salli_porrastetut'] != $toiminnassa['s_salli_porrastetut']){
         $p_muuttuu = true;
      }
      
      
      
      if(!($t_muuttuu || $p_muuttuu)){
         $msg = "Et muuttanut asetuksia.";
         return false;
      }
      //samaksi jättäminen tai pois päältä ottaminen on aina ok
      else if((!$t_muuttuu || ($t_muuttuu && $toiminnassa['toiminnassa'] == 0))
              &&  ((!$p_muuttuu || ($p_muuttuu && $toiminnassa['s_salli_porrastetut'] == 0)))){
         //pois päältä on aina ok heittää
         return true;
      }
      //jos merkitään toiminnassa olevaksi, mutta jaos ei ole valmis
      else if($t_muuttuu && $toiminnassa['toiminnassa'] == '1' && !$this->jaos_ready($id, $msg, $pulju)){
         //jos_ready funktio päivittää msg:n
         return false;
      }
      //jos merkitään porrastetut toimintaan, mutta jaos ei ole valmis
      else if($p_muuttuu  && $toiminnassa['s_salli_porrastetut'] == '1' && !$this->jaos_porr_ready($id, $msg)){
         return false;
      }
      else {
         return true;
      }
      
    }
    
    function jaos_ready($id, &$msg, $pulju = false){
      $ok = true;
      $owners = array();
      if($pulju){
         $owners = $this->CI->Jaos_model->get_pulju_owners($id);
      }else {
         $owners = $this->CI->Jaos_model->get_jaos_owners($id);
      }
          if(sizeof($owners) < 1){
            $msg .= " Jaoksella ei ole vielä ylläpitäjiä!";
            $ok = false;
         }
         return $ok;
    }
    
    function jaos_porr_ready($id, &$msg){
      $ok = true;
      $jaos = $this->CI->Jaos_model->get_jaos($id);     
      $luokat = $this->CI->Jaos_model->get_class_list($id, true, true);
      
      if(sizeof($luokat) < 1){
            $msg .= " Jaokselle ei ole vielä asetettu porrastettuja luokkia!";
            $ok = false;
      } 
            
      $traits = $this->CI->Trait_model->get_trait_list($id);
      if(sizeof($traits) < 1){
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
      $fields['s_luokkia_per_kisa_max'] = array('label' => 'Porrastetut: Luokkamäärä/kisa (max)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_kisa_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_luokkia_per_kisa_max_norm'] = array('label' => 'Perinteiset: Luokkamäärä/kisa (max)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_kisa_max_norm'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $fields['s_hevosia_per_luokka_min'] = array('label' => 'Hevosia/luokka/ratsastaja (min)', 'type' => 'number', 'value' => $jaos['s_hevosia_per_luokka_min'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_hevosia_per_luokka_max'] = array('label' => 'Hevosia/luokka/ratsastaja (max)', 'type' => 'number', 'value' => $jaos['s_hevosia_per_luokka_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $fields['s_luokkia_per_hevonen_min'] = array('label' => 'Luokkia/hevonen/kisa (min)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_hevonen_min'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
      $fields['s_luokkia_per_hevonen_max'] = array('label' => 'Luokkia/hevonen/kisa (max)', 'type' => 'number', 'value' => $jaos['s_luokkia_per_hevonen_max'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);

      $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    var $rules_array = array("s_luokkia_per_kisa_max" => 0,
                             "s_luokkia_per_kisa_max_norm" => 0,
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
         ||($jaos['s_luokkia_per_hevonen_max']<$jaos['s_luokkia_per_hevonen_min'])
         || ($jaos['s_luokkia_per_kisa_max_norm']<$jaos['s_luokkia_per_kisa_min'])){
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
      $this->CI->form_validation->set_rules('nimi', 'Nimi', 'min_length[1]|max_length[100]|required');
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
    // PALKINNOT
    ///////////////////////////////////////////////////////
    
    
    
   function palkintotaulukko($id, $url_poista, $url_muokkaa){
      //start the list		
		$vars['headers'][2] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][1] = array('title' => 'Järj. nro', 'key' => 'jarjnro');
		$vars['headers'][3] = array('title' => 'Palkinto', 'key' => 'palkinto');
      $vars['headers'][4] = array('title' => 'Kuvaus', 'key' => 'kuvaus');
      $vars['headers'][5] = array('title' => 'Käytössä', 'key' => 'kaytossa');
      $vars['headers'][6] = array('title' => '&nbsp;', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      $vars['headers'][7] = array('title' => '&nbsp;', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png')); 
		$vars['headers'] = json_encode($vars['headers']);				
		$vars['data'] = json_encode($this->CI->Jaos_model->get_reward_list($id, false));
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);       
    }
    
    
    function get_reward_form($url, $class){
      $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

      $fields = array();
      $fields['palkinto'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $class['palkinto'] ?? "");
      $fields['kuvaus'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'value' => $class['kuvaus'] ?? "");

      $fields['jarjnro'] = array('label' => 'Järjestysnumero', 'type' => 'number', 'value' => $class['jarjnro'] ?? 999, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE,
                                 'after_html' => '<span class="form_comment">Palkinnot listataan tuloksissa ja tuloslomakkeella järjestyksessä tämän mukaan (pienin ylimpänä).
                                 Jos kahdella palkinnolla on sama numero, esitetään aakkosjärjestyksessä.</span>');    
      $fields['kaytossa'] = array('type' => 'checkbox', 'label'=> "Käytössä", 'checked' => $class['kaytossa'] ?? true, 'class'=>'form-control',
                                  'after_html' => '<span class="form_comment">Vain käytössä oleviksi merkityt palkinnot näkyvät tuloslähetyslomakkeella. </span>');

      $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
         return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    function read_reward_input(&$class){
         $class['palkinto'] = $this->CI->input->post('palkinto');
         $class['kuvaus'] = $this->CI->input->post('kuvaus');
         $class['jarjnro'] = $this->CI->input->post('jarjnro');
         if($this->CI->input->post('kaytossa')){
            $class['kaytossa'] = $this->CI->input->post('kaytossa');
         }else {
            $class['kaytossa'] = 0;
         }
    }
    
    function validate_reward_form(){
      $this->CI->load->library('form_validation');
      $this->CI->form_validation->set_rules('palkinto', 'Palkinto', 'min_length[1]|max_length[32]|required');
      $this->CI->form_validation->set_rules('jarjnro', 'Järjestysnumero', 'min_length[1]|max_length[3]|numeric|required');
      
      return $this->CI->form_validation->run();
      
    }
    
    function validate_reward($type="add", $admin, $class, &$msg){
      //todo onko jo olemassa
      return true;
    }
    
    function delete_reward($id, $jaos_id, &$msg){
      //TODO: Katso kisaluokat
      return $this->CI->Jaos_model->delete_reward($id, $jaos_id);
      
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
    
    
    
      ////////////////////////////////////////////////////////
    //   RODUT
    ///////////////////////////////////////////////////////
    
    function get_breed_form($url, $rodut = array(), $pulju = false){
            $this->CI->load->library('form_builder', array('submit_value' => 'Tallenna'));

      		$fields['rodut'] = array('type' => 'multi', 'mode' => 'checkbox', 'required' => TRUE,
                                     'options' => $this->CI->Breed_model->get_breed_option_list(), 'value'=>$rodut, 'class'=>'form-control', 'wrapper_tag' => 'li');
            $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
            return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);


    }
    
   function read_breed_input(&$breeds, $pulju = false){
      $breeds = $this->CI->input->post('rodut');
    }
    
    function validate_breed_form($id, $breeds, $jaos, &$msg, $pulju = false){
      if(sizeof($breeds) < 1){
         $msg = "Rotuja pitää olla vähintään 1!";
         return false;
      }
      if(sizeof($breeds) > 15){
         $msg = "Rotuja saa valita korkeintaan 15!";
         return false;
      }
      
      return true;
    }
}
    
    
    
?>