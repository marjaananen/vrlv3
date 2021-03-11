<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Events
{

    
public function __construct()
	{
        $this->CI =& get_instance();
	}
	
	public function handle_event($pulju, $id, $jaos, $tapa = null, $os_id = null, $edit_url, $data = array()){
		$this->CI->load->library('vrl_helper');
		$tapahtuma = array();
		if($pulju){
			 $tapahtuma = $this->CI->Jaos_model->get_event($id, null, $jaos['id']);

		}else {
			$tapahtuma = $this->CI->Jaos_model->get_event($id, $jaos['id']);

		}
        if (sizeof($tapahtuma)>0){
            //jos poistetaan palkittu 
            if (isset($tapa) && $tapa == "poista"){
                if($this->_delete_event_horse($os_id, $id, $data)){
					$data['msg_type'] = "success";
					$data['msg'] = "Hevosen poisto onnistui!";
				}
            //muokataan tapahtumaa
            } else {
                 if($this->_edit_event($id, $jaos, $data, $pulju)){

				 }
            }
            
            
        }else {
            $data = array('msg_type' => 'danger', 'msg' => "Tapahtumaa jota yrität muokata ei ole olemassa.");

        }
		$this->tapahtuma($id, true, $edit_url, $data);            

	}
    
    private function _parse_event_horses($osallistujalista_input, &$data, &$luetut_rivit){
        $this->CI->load->library("Vrl_helper");
        $this->CI->load->model("Hevonen_model");

        $osallistujalista = trim($osallistujalista_input);
		$osallistujat = explode("\n", $osallistujalista);
		$osallistujat = array_filter($osallistujat, 'trim');
        $kasitellyt_kopukat = array();
        $luetut_rivit = array();
		$virhe = array();		
		//Jokainen rivi/luokka käydään läpi
		foreach ($osallistujat as $rivi_input){
            $luettu = array();
            $rivi = explode(";", $rivi_input);
            if(sizeof($rivi) != 4){
                $virhe[] = "Virheellinen rivi, tarkistathan että riviltä löytyy kolme ; merkkiä! " . $rivi_input;

            }
            else {
                if(isset($rivi[0]) && $this->CI->vrl_helper->check_vh_syntax($rivi[0]) && $this->CI->Hevonen_model->onko_tunnus($this->CI->vrl_helper->vh_to_number($rivi[0]))){
                    $luettu['vh'] = $this->CI->vrl_helper->vh_to_number(trim($rivi[0]));
                    if(isset($kasitellyt_kopukat[$rivi[0]])){
                        $virhe[] = "Sama VH-tunnus toista kertaa rivillä: " . $rivi_input;

                    }
                    
                    else if(isset($rivi[2]) && strlen($rivi[2]) > 1){
                        $luettu['palkinto'] = $rivi[2];
                                        
                    
                        if(isset($rivi[1])){
                            $luettu['tulos'] = str_replace(",", ".", $rivi[1]);
                        }
                        
                        if(isset($rivi[3])){
                            $luettu['kommentti'] = htmlspecialchars($rivi[3]);
                        }
                        
                        $luetut_rivit[] = $luettu;
                        $kasitellyt_kopukat[$rivi[0]] = "ok";
                        }
                    
                    else {
                        $virhe[] = "Virheellinen palkinto rivillä: " . $rivi_input;
                    }
  
                }else {
                    $virhe[] = "Virheellinen VH-tunnus rivillä: " . $rivi_input;
                }
            }
            
        }
        
        if(sizeof($virhe)>0){
            $data['msg'] = "Virhe osallistujien lisäyksessä!";
            $data['msg_details'] = $virhe;
            $data['msg_type'] = 'danger';
            return false;
        }
        else {
            return true;
        }
    }
    
    
    function tapahtuma($id, $admin = false, $edit_url = null, $data = array()){
        
        $data['tapahtumatyyppi'] ="Muu arvostelutilaisuus";
        $data['tapahtuma'] = $this->CI->Jaos_model->get_event($id);

        if(sizeof($data['tapahtuma']) >0){
            
            if($admin){
                $data['delete_url'] = $edit_url."/poista/".$id;
                $data['palkitut'] = $this->tapahtumaosallistujat($id, $edit_url."/muokkaa/".$id."/poista/");
                $data['form'] = $this->get_event_form($edit_url."/muokkaa/".$id, $data['tapahtuma'], true);
                }
            else {
                $data['palkitut'] = $this->tapahtumaosallistujat($id, null);
        
                }
            
            
            if(isset($data['tapahtuma']['jaos_id'])){
                $data['jaos'] = $this->CI->Jaos_model->get_jaos($data['tapahtuma']['jaos_id']);
                $data['tapahtumatyyppi'] = "Kilpailujaoksen laatuarvostelutilaisuus";
            }else {
                $data['jaos'] = $this->CI->Jaos_model->get_pulju($data['tapahtuma']['pulju_id']);
                
                if($data['jaos']['tyyppi'] == 1){
                    $data['tapahtumatyyppi'] = "Kantakirjaustilaisuus";
                }else if($data['jaos']['tyyppi'] == 3){
                    $data['tapahtumatyyppi'] = "Laatuarvostelutilaisuus";
                }
                
            }
            
            $this->CI->fuel->pages->render('puljut/tapahtuma', $data);
        }
        else {
                $this->CI->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => "Valitsemaasi tapahtumaa ei löydy."));

        }
    }
  
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
    
    private function _edit_event($id, $jaos, &$data=array(), $pulju = false){
       if($this->CI->input->server('REQUEST_METHOD') == 'POST'){
		$data['otsikko'] = $this->CI->input->post('otsikko');
		$data['pv'] = $this->CI->input->post('pv');
		$data['osallistujat'] = $this->CI->input->post('osallistujat');
         //tapahtuman tiedot
        if($this->CI->input->post('otsikko') && $this->CI->input->post('pv')
           && strlen($this->CI->input->post('otsikko')) > 5
           && $this->CI->vrl_helper->validateDate($this->CI->input->post('pv'))){
            
                $this->CI->Jaos_model->edit_event($id, $jaos['id'], $this->CI->input->post('otsikko'), $this->CI->input->post('pv'), $pulju);
                
                //lisätäänkö osallistujia?
                if($this->CI->input->post('osallistujat')){
                    $osallistujat = array();
                    $osallistujat_ok = $this->_parse_event_horses($this->CI->input->post('osallistujat'), $data, $osallistujat);
                    if($osallistujat_ok){
                        $this->CI->Jaos_model->add_event_participants($id, $osallistujat);

                    }else {
						return false;
					}
					
					$data['msg'] = "Tapahtuman tiedot muokattu onnistuneesti.";
					$data['msg_type'] = 'success';
                }
            }
            
        else {
			$data['msg'] = "Tapahtuman tiedot virheelliset.";
			$data['msg_type'] = 'danger';
			return false;


        }
       }
	   return true;
        
        
    }

    function get_event_list_by_type($type){
        $kisajaos = false;
        $search_type = 0;
        if($type == "jaokset"){
            $kisajaos = true;
					
        }else if($type == "kantakirjat"){
            $search_type = 1;
        }else if($type == "laatuarvostelut"){
            $search_type = 3;
        }else if($type == "rotuyhdistykset"){
            $search_tyoe = 2;
        }
		return $this->CI->Jaos_model->get_event_list_by_type($kisajaos, $search_type);
    }
    
          function tapahtumataulukko_admin($jaos, $url_poista, $url_muokkaa, $pulju = false){
                                //start the list		
		$vars['headers'][1] = array('title' => 'ID', 'key' => 'id');
		$vars['headers'][2] = array('title' => 'Päivämäärä', 'key' => 'pv', 'type'=>'date');
		$vars['headers'][3] = array('title' => 'Otsikko', 'key' => 'otsikko');
        $vars['headers'][4] = array('title' => 'Poista', 'key' => 'id', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'id', 'key_link' => site_url($url_muokkaa), 'image' => site_url('assets/images/icons/edit.png'));

		$vars['headers'] = json_encode($vars['headers']);
		if($pulju){
            $vars['data'] = json_encode($this->CI->Jaos_model->get_event_list(null, $jaos));
        }else  {
            $vars['data'] = json_encode($this->CI->Jaos_model->get_event_list($jaos));

        }
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
        
    function print_event_list($data, $edit_url, $pulju = false, $event_data = array()){

                $data['form'] = $this->get_event_form($edit_url."/lisaa", $event_data);
                $data['title'] = "Tapahtumalista (järjestäjä ". $data['jaos']['nimi'] . ")";
                $data['text_view'] =  $this->CI->load->view('puljut/tapahtumat_text_admin', null, TRUE);
                $data['lista'] = $this->tapahtumataulukko_admin($data['jaos']['id'], $edit_url."/poista/", $edit_url."/muokkaa/", $pulju);
                $this->CI->fuel->pages->render('puljut/tapahtumat', $data);
    }
    
    
    public function select_event_organizer($id, $url_begin, $pulju = false){
        $type = 'jaos';
        if($pulju){
            $type = 'alayhdistys';
        }
        if($this->CI->input->post($type)){
                $id = $this->CI->input->post($type);
                redirect($url_begin.$id, 'refresh');
            }else {       
                $this->CI->load->library('form_builder', array('submit_value' => 'Hae'));
                $options = ARRAY();
                if($pulju){
                    $options = $this->CI->Jaos_model->get_pulju_option_list();
                }else {
                    $options = $this->CI->Jaos_model->get_jaos_option_list();
                }
        
                $fields = array();
                $fields[$type] = array('label'=>"Järjestäjä", 'type' => 'select', 'options' => $options, 'value' => 0, 'class'=>'form-control');
                $this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url_begin));
                $data['form'] =  $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
                $data['title'] = "Tapahtumahaku";
                $data['text_view'] =  $this->CI->load->view('puljut/tapahtumat_text_admin', null, TRUE);

                $data['lista'] = "";
                $this->CI->fuel->pages->render('puljut/tapahtumat', $data);
            }
    }
    

    
     public function add_event($id, &$data, $edit_url, $pulju = false){
        $jaos = $data['jaos'];
        $event_data = array();
        $this->CI->load->library("vrl_helper");
        if($this->CI->input->server('REQUEST_METHOD') == 'POST'){
            $event_data['pv'] = $this->CI->input->post('pv');
            $event_data['otsikko'] = $this->CI->input->post('otsikko');
            $event_data['osallistujat'] = $this->CI->input->post('osallistujat');
			
                        
            if(strlen($event_data['otsikko']) > 5
               && $this->CI->vrl_helper->validateDate($this->CI->input->post('pv'))){
                $osallistujat = array();
                $osallistujat_ok = $this->_parse_event_horses($this->CI->input->post('osallistujat'), $data, $osallistujat);
                if($osallistujat_ok){
                    $tid = $this->CI->Jaos_model->add_event($this->CI->input->post('pv'), $this->CI->input->post('otsikko'), $this->CI->ion_auth->user()->row()->tunnus,
                                                     $jaos['id'], $osallistujat, $pulju);
    
                    IF($tid == false){
                        $data['msg'] = "Virhe lisäyksessä! Sisältö ei mennyt tietokantaan asti. Ole yhteydessä ylläpitoon!";
                        $data['msg_type'] = 'danger';
                    }else {
                    
                        $data['msg'] = "Tapahtuman lisäys onnistui!";
                        $data['msg_type'] = 'success';
                        $event_data = array();
                    }
                }
                
                //parse_event_horses funkkari lisää virheilmot $data funkkariin, ei tarvitse käsitellä erikseen
            }
            else {
                
                $data['msg'] = "Virhe lisäyksessä! Otsikko tai päivämäärä puuttuu tai on liian lyhyt/virheellinen.";
                $data['msg_type'] = 'danger';
            }
                
                                
        }else {
            $event_data = array();
        }
        $data['event_data'] = $event_data;
    }
    
    public function delete_event($id, &$data, $pulju = false){
        $tapahtuma = null;
        if($pulju){
            $tapahtuma = $this->CI->Jaos_model->get_event($id, null, $data['jaos']['id']);
        }else {
            $tapahtuma = $this->CI->Jaos_model->get_event($id, $data['jaos']['id']);

        }
        if (sizeof($tapahtuma)>0){
            $osallistujat = $this->CI->Jaos_model->get_event_horses($id);
            if(sizeof($osallistujat)>0){
                $data['msg_type'] = 'danger';
                $data['msg'] = "Tapahtumassa on palkittuja hevosia. Poista ensin palkitut hevoset listalta.";
               
            }else {
                if($pulju){
                    $this->CI->Jaos_model->delete_event($id, null, $data['jaos']['id']);

                }else {
                    $this->CI->Jaos_model->delete_event($id, $data['jaos']['id'], null);
                }
                $data['msg_type'] = "success";
                $data['msg'] = "Poisto onnistui!";
            }
            
        }else {
            $data['msg_type'] = 'danger';
            $data['msg'] = "Tapahtumaa jota yrität poistaa ei ole olemassa.";

        }        
        
    }
    
    private function _delete_event_horse($os_id, $id, $data = array()){
        $this->CI->Jaos_model->delete_event_horse($os_id, $id);
		return true;

    }
    
    
    function tapahtumaosallistujat ($id, $url_poista){
                                //start the list		
		$vars['headers'][1] = array('title' => 'Rekisterinumero', 'key' => 'vh', 'type'=>'VH', 'key_link'=>site_url('virtuaalihevoset/hevonen/'));
        $vars['headers'][2] = array('title'=> 'Nimi', 'key' => 'nimi');
		$vars['headers'][3] = array('title' => 'Tulos', 'key' => 'tulos');
      $vars['headers'][4] = array('title' => 'Palkinto', 'key' => 'palkinto');
      $vars['headers'][5] = array('title' => 'Kommentti', 'key' => 'kommentti', 'type'=>'small');
      if(isset($url_poista)){
         $vars['headers'][6] = array('title' => 'Poista', 'key' => 'oid', 'key_link' => site_url($url_poista), 'image' => site_url('assets/images/icons/delete.png'));
      }
		$vars['headers'] = json_encode($vars['headers']);
					
		$vars['data'] = json_encode($this->CI->Jaos_model->get_event_horses($id));
        
        return  $this->CI->load->view('misc/taulukko', $vars, TRUE);
        
    }

    
    
    
}