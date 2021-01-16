<?php
class Kilpailutoiminta extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
	
        $this->load->model('Jaos_model');
        $this->load->library('Jaos');
        $this->load->library('Porrastetut');
        $this->load->library('Kisajarjestelma');
        $this->load->model('Kisakeskus_model');



    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
	
	function index ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('kilpailutoiminta/index', $vars);
    }
	
	function kilpailujaokset ()
    {
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full();     
        $this->fuel->pages->render('jaokset/jaoslista', $vars);
    }
    
    function kilpailusaannot(){
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full(); 
        $vars['jaoskohtaiset'] = $this->load->view('kilpailutoiminta/jaoskohtaiset_rajaukset', $vars, TRUE);
        $this->fuel->pages->render('kilpailutoiminta/kilpailusaannot', $vars);
    }
    
    function kilpailujarjestaminen(){
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full(false, true); 
        $vars['jaoskohtaiset'] = $this->load->view('kilpailutoiminta/jaoskohtaiset_rajaukset', $vars, TRUE);
        $this->fuel->pages->render('kilpailutoiminta/kilpailusaannot_kilpailut', $vars);
    }
    
    function nayttelysaannot(){
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full(true, false); 
        $vars['jaoskohtaiset'] = $this->load->view('kilpailutoiminta/jaoskohtaiset_rajaukset', $vars, TRUE);
        $this->fuel->pages->render('kilpailutoiminta/kilpailusaannot_nayttelyt', $vars);
    }
    
    function omat($category = null, $type = null, $msg = array()){
        $url = "kilpailutoiminta/omat/".$category;
        if(!($this->ion_auth->logged_in()))
        {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään tarkastellaksesi omia tietojasi!'));
        }else {
            
            $vars = array();
            $data = array();
            $user = $this->ion_auth->user()->row()->tunnus;
            
     
            
            if($category == "etuuspisteet"){
                 $jaosdata = array();
                $tunnus = $this->ion_auth->user()->row()->tunnus;
                $jaokset = $this->Jaos_model->get_jaos_list();
                foreach ($jaokset as $jaos){
                    $info = array();
                    $info['pisteet'] = $this->Jaos_model->getEtuuspisteet($jaos['id'], $tunnus);
                    $info['avoimet'] = $this->Jaos_model->usersOpenCompetitions($jaos['id'], $tunnus);
                    $info['jaos'] = $jaos;
                    $jaosdata[$jaos['id']] = $info;
                }
                $data['pisterivit'] = $jaosdata;
                $data['kisajarjestelma'] = & $this->kisajarjestelma;
                $this->fuel->pages->render('kilpailutoiminta/omat_etuuspisteet', $data);

            }
            
            else {
                if($category == "nayttelyt"){
                    $data['nayttelyt'] = $this->_omat_nayttelyt_list($type, $user, $url);
  
                }
                else {
                    $data['kisat'] = $this->_omat_kisat_list($type, $user, $url);

                }
                
                $data['sivu'] = $type;
                $data['category'] = $category;

                $data['msg'] = $msg;
                
                $this->fuel->pages->render('kilpailutoiminta/omat_kilpailut', $data);

            }
            
            

        }
    }
    
    private function _omat_kisat_list($type, $user, $url){
        $vars = array();
        
        if($type == "porrastetut"){
            $vars['headers'][1] = array('title' => 'ID', 'key' => 'kisa_id', 'prepend_text' => "#");    
            $vars['headers'][2] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
            $vars['headers'][3] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
            $vars['headers'][4] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
            $vars['headers'][5] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli/'));
            $vars['headers'][6] = array('title'=> 'Kutsu', 'key'=>'kisa_id', 'type'=>'url', 'static_text'=>'Kutsu', 'key_link' => site_url('kilpailutoiminta/k/'));
            $vars['headers'][7] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
            $vars['headers'][8] = array('title' => 'Hyväksytty', 'key' => 'hyvaksytty', 'type'=>'date');
            
            $vars['data'] = json_encode($this->Kisakeskus_model->get_users_competitions($user, false, true, false, false, true));
    
                    
            }
            else {
                $vars['headers'][1] = array('title' => 'ID', 'key' => 'kisa_id', 'prepend_text' => "#");
                $vars['headers'][2] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
                $vars['headers'][3] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
                $vars['headers'][4] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
                $vars['headers'][5] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli'));
                $vars['headers'][6] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
                $vars['headers'][7] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
                
                if($type == "jonossa"){
                   $vars['headers'][8] = array('title' => 'Anottu', 'key' => 'kisailmoitettu', 'type'=>'date');
                   $vars['headers'][9] = array('title' => 'Poista', 'key' => 'kisa_id', 'key_link' => site_url($url) . "/".$type. "/delete/", 'image' => site_url('assets/images/icons/delete.png'));           
                   $vars['data'] = json_encode($this->Kisakeskus_model->get_users_competitions($user, true, false, false, false));

                }else if($type == "tulosjonossa"){
                    $vars['headers'][8] = array('title' => 'Ilmoitettu', 'key' => 'tulosilmoitettu', 'type'=>'date');
                    $vars['headers'][9] = array('title' => 'Poista', 'key' => 'kisa_id', 'key_link' => site_url($url) ."/". $type. "/delete/", 'image' => site_url('assets/images/icons/delete.png'));  
                    $vars['data'] = json_encode($this->Kisakeskus_model->get_users_competitions($user, false, false, true, false));

                }
                else if($type == "avoimet"){
                    $vars['headers'][8] = array('title' => 'Ilmoita tulokset', 'key' => 'kisa_id', 'key_link' => site_url('kilpailutoiminta/ilmoita_tulokset/'), 'image' => site_url('assets/images/icons/medal_gold_add.png'));
                    $vars['data'] = json_encode($this->Kisakeskus_model->get_users_competitions($user, false, true, false, false, false));

                
                }
                else if($type == "menneet"){
                    $vars['headers'][6] = array('title' => 'Porrastettu', 'key' => 'porrastettu');
                    $vars['headers'][7] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
                    $vars['headers'][8] = array('title' => 'Tulos', 'key' => 'tulos_id', 'key_link'=> site_url('kilpailutoiminta/tulosarkisto/tulos/'));
                    $vars['data'] = json_encode($this->Kisakeskus_model->get_users_competitions($user, false, false, false, true));

                }
            }
        
            
            $vars['headers'] = json_encode($vars['headers']);        
            return $this->load->view('misc/taulukko', $vars, TRUE);
    }
    
    
    private function _omat_nayttelyt_list($type, $user, $url){
        $vars = array();

        $vars['headers'][1] = array('title' => 'ID', 'key' => 'kisa_id', 'prepend_text' => "#");
        $vars['headers'][2] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
        $vars['headers'][3] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
        $vars['headers'][4] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
        $vars['headers'][5] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli'));
        $vars['headers'][6] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
        $vars['headers'][7] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
        
        if($type == "jonossa"){
           $vars['headers'][8] = array('title' => 'Anottu', 'key' => 'kisailmoitettu', 'type'=>'date');
           $vars['headers'][9] = array('title' => 'Poista', 'key' => 'kisa_id', 'key_link' => site_url($url) . "/".$type. "/delete/", 'image' => site_url('assets/images/icons/delete.png'));           
           $vars['data'] = json_encode($this->Kisakeskus_model->get_users_shows($user, true, false, false, false));

        }else if($type == "tulosjonossa"){
            $vars['headers'][8] = array('title' => 'Ilmoitettu', 'key' => 'tulosilmoitettu', 'type'=>'date');
            $vars['headers'][9] = array('title' => 'Poista', 'key' => 'kisa_id', 'key_link' => site_url($url) ."/". $type. "/delete/", 'image' => site_url('assets/images/icons/delete.png'));  
            $vars['data'] = json_encode($this->Kisakeskus_model->get_users_shows($user, false, false, true, false));

        }
        else if($type == "avoimet"){
            $vars['headers'][8] = array('title' => 'Ilmoita tulokset', 'key' => 'kisa_id', 'key_link' => site_url('kilpailutoiminta/ilmoita_nayttelytulokset/'), 'image' => site_url('assets/images/icons/medal_gold_add.png'));
            $vars['data'] = json_encode($this->Kisakeskus_model->get_users_shows($user, false, true, false, false, false));

        
        }
        else if($type == "menneet"){
            $vars['headers'][8] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
            $vars['headers'][9] = array('title' => 'Tulos', 'key' => 'tulos_id', 'key_link'=> site_url('kilpailutoiminta/tulosarkisto/bis/'));
            $vars['data'] = json_encode($this->Kisakeskus_model->get_users_shows($user, false, false, false, true));

        }
        
            
        $vars['headers'] = json_encode($vars['headers']);        
        return $this->load->view('misc/taulukko', $vars, TRUE);
    }
    
    function etuuspisteet(){
        $data['kisajarjestelma'] = & $this->kisajarjestelma;
        $this->fuel->pages->render('kilpailutoiminta/etuuspisteet', $data);

        
    }
    
    function porrastetut($sivu = null){
        $vars = array();
        $vars['levels'] = $this->porrastetut->get_levels();
        $vars['traits'] = $this->porrastetut->get_traits();
        $vars['aste'] = $this->porrastetut->get_asteet();
        $vars['jaokset'] = $this->porrastetut->get_all_porrastettu_info();
        
        if($sivu == "luokat"){
           $this->fuel->pages->render('kilpailutoiminta/porrastetut_luokat', $vars); 
        }else if($sivu == "kilpailulistat"){
            if(!($this->ion_auth->logged_in()))
            {
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään katsoaksesi kilpailulistaasi!'));
            } else {
                $this->_porrastetut_kisalistat('kilpailutoiminta/porrastetut/kilpailulistat');
            }
        }
        else {
            $this->fuel->pages->render('kilpailutoiminta/porrastetut_saannot', $vars);
        }
    }
    
    public function omat_delete($showtype = "kisat", $type = "tulos", $id){
        if(!($this->ion_auth->logged_in()))
           {
               $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Vain rekisteröityneet käyttäjät voivat käsitellä kilpailuja!'));
           } else {
                $tulokselliset = false;
                $nayttelyt = false;
                $kisa = array();
                $tulos = array();
                
                if($showtype == "nayttelyt"){
                    $nayttelyt = true;
                }
                if($type == "tulos"){
                 $tulokselliset = true;
                }
                
                $user = $this->ion_auth->user()->row()->tunnus;

                $this->db->trans_start();
                $kisa = $this->Kisakeskus_model->hae_kutsutiedot($id, null, $tulokselliset, $nayttelyt);
                
                if($tulokselliset){
                    
                 $tulos = $this->Kisakeskus_model->get_result(null, $id, false, $nayttelyt);

                }
               
                //jos kisaa ei löydy, loppuu siihen
                if(sizeof($kisa) == 0){
                    $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kilpailua ei ole olemassa, tai se ei ole enää jonossa.'));
                    $this->db->trans_complete();
                }
                //jos ollaan poistamassa kisaa ja se on jo hyväksytty kalenteriin
                else if( !$tulokselliset && isset($kisa['hyvaksytty'])){
                     $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Hyväksyttyjä kilpailuja ei voi poistaa itse. Ole yhteydessä jaoksen ylläpitoon.'));
                     $this->db->trans_complete();
                }
                //jos ollaan poistamassa tulosta, ja sitä ei löydy, tai se on jo hyväksytty
                else if($tulokselliset && sizeof($tulos) == 0){
                     $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Tulosta ei ole olemassa, tai se ei ole enää jonossa.'));
                     $this->db->trans_complete();
                
                }else if(!($kisa['tunnus'] == $user || $kisa['takaaja'] == $user)){
                    $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kilpailu ei ole sinun järjestämäsi etkä ole sen takaaja.'));
                    $this->db->trans_complete();

                }else if($nayttelyt){
                    if($tulokselliset){
                         //jos tarkoitus oli poistaa vain tulos, merkitään kisa tuloksettomaksi
                        $this->db->delete('vrlv3_kisat_nayttelytulokset', array('nayttely_id' => $id));
                        $this->db->set('tulokset', 0);
                        $this->db->where('kisa_id', $id);
                        $this->db->update('vrlv3_kisat_nayttelykalenteri');
                        $this->db->trans_complete();
                        $this->omat("nayttelyt", "tulosjonossa", array("msg_type"=>"success", "msg"=>"Poisto onnistui."));
                    }
                    
                    else{
                        $this->db->delete('vrlv3_kisat_nayttelytulokset', array('nayttely_id' => $id));
                        $this->db->delete('vrlv3_kisat_nayttelykalenteri', array('kisa_id' => $id));
                        $this->db->trans_complete();
                        $this->omat("nayttelyt", "jonossa", array("msg_type"=>"success", "msg"=>"Poisto onnistui."));
   
                   }
                }
               //vanhat porrastetut ja taviskisat toimivat samalla tavalla
               else if(($kisa['porrastettu'] == 0) || ($kisa['porrastettu'] == 1 && (sizeof($kisa['luokat']) == 0))){
                    //jos tarkoitus oli poistaa kisa jonosta, poistetaan se (ja tulos varmuuden vuoksi)
                    if($tulokselliset){
                        //jos tarkoitus oli poistaa vain tulos, merkitään kisa tuloksettomaksi
                        $this->db->delete('vrlv3_kisat_tulokset', array('kisa_id' => $id));
                        $this->db->set('tulokset', 0);
                        $this->db->where('kisa_id', $id);
                        $this->db->update('vrlv3_kisat_kisakalenteri');
                        $this->db->trans_complete();
                        $this->omat("kisat", "tulosjonossa", array("msg_type"=>"success", "msg"=>"Poisto onnistui."));
                    }
                    else {
                        $this->db->delete('vrlv3_kisat_tulokset', array('kisa_id' => $id));
                        $this->db->delete('vrlv3_kisat_kisakalenteri', array('kisa_id' => $id));
                        $this->db->trans_complete();
                        $this->omat("kisat", "jonossa", array("msg_type"=>"success", "msg"=>"Poisto onnistui."));

                        }
                    
                
               } 
               
           }
    }
    
    
    private function _porrastetut_kisalistat($url){
        $vars['jaokset'] = $this->porrastetut->get_porrastetut_jaokset();
        $full_porrastettu_info = $this->porrastetut->get_all_porrastettu_info();
        
        $this->load->model("Tallit_model");
        $this->load->model("Hevonen_model");
        $this->load->library("Vrl_helper");
        
        $tunnus = $this->ion_auth->user()->row()->tunnus;
        $nick = $this->ion_auth->user()->row()->nimimerkki;
        $sort = array();
        $sort = $this->_read_kisalista_input();

        $vars['tallit']  = $this->Tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
        $vars['rodut'] = $this->Hevonen_model->get_owners_breeds($this->ion_auth->user()->row()->tunnus);
        $vars['form'] = $this->_kisalista_form($sort, $vars['rodut'],$vars['tallit'],$url);

        $empty_trait_list = $this->porrastetut->get_empty_trait_array();
        
        $vars['printArrayWaitlist'] = array();
        $vars['printArrayReadylist'] = array();
        $vars['printArray'] = array();
    
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            
            $vars['hevoset'] = array();

            $rotu = null;
            $talli = null;
            $minheight = null;
            if($sort['rotu'] > 0){
                $rotu = $sort['rotu'];
            }if($sort['talli'] != "-1"){
                $talli = $sort['talli'];
            }if($sort['minheight'] > 10){
                $minheight = $sort['minheight'];
            }
            
            $vars['hevoset'] = $this->Hevonen_model->get_owners_porrastettu_horses($this->ion_auth->user()->row()->tunnus, $rotu, $talli,$minheight);
            
            
            

            
            foreach ($vars['hevoset'] as $poni){
                if($poni['porr_kilpailee'] === 0){
                    continue;
                }
                //Haetaan ikä
                $levelByAge = $this->porrastetut->level_by_age($poni);
                $full_traits = $this->porrastetut->get_horses_full_level_list($poni['reknro'], $empty_trait_list, $vars['jaokset']);
                $full_sport_info = $this->Hevonen_model->get_horse_sport_info_by_jaos($poni['reknro']);
                //On ikää kisata, ja säkäkorkeus merkitty
                if( $levelByAge > 2 && $poni['sakakorkeus'] > 10) {
                    
                
                    foreach ($vars['jaokset'] as $jaos){
                        $level = 0;
            
                        if ($sort['painotus'] == 1 && (!isset($poni['painotus']) || $jaos['laji'] != $poni['painotus'])){
                            //echo "Pois painotuksen takia: " . $poni['nimi'] . ": " . $poni['painotus'] ."vs. ". $jaos['laji']."<br>";
                            continue;
                        }
                        
                        //ei tarvitse enää sortata rotujen ja tallien mukaan kun ne ei pääse tänne asti
                                                      
                        $level = $full_traits[$jaos['lyhenne']]['level'];
                        
                        $vh = $this->vrl_helper->get_vh($poni['reknro']);
                        
                        $rajoitus = $full_sport_info[$jaos['id']]['taso_max'] ?? 10;
                
                        //Jos hevonen ei ole tarpeeksi vanha nousemaan tasolta, se ei nouse, vaikka pisteet riittÃ¤isi
                        if ($levelByAge < $level && $level <= $rajoitus){
                            $vars['printArrayWaitlist'][$jaos['lyhenne']][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . " (Odottaa ikääntymistä päästäkseen seuraavalle tasolle vt." . $level .")<br />";
                        }
                                        
                        else if ($level <= $rajoitus){
                            $vars['printArray'][$jaos['lyhenne']][$level][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . "<br />";
                        }
                        
                        else if ($rajoitus > -1){
                            $vars['printArrayReadylist'][$jaos['lyhenne']][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . " (vt." . $level .")<br />";
                        }

                        
                    }	
                }
                
            }
        }
        
         $this->fuel->pages->render('kilpailutoiminta/porrastetut_kisalistat', $vars);


        
    }
    
    private function _kisalista_form($result, $rodut, $tallit, $url){
        $this->load->library('form_builder', array('submit_value' => 'Suodata'));
      
        $rodut_options = array(-1 =>"Kaikki rodut");
    
        foreach ($rodut as $rotu){
            $rodut_options[$rotu['rotunro']] = $rotu['lyhenne'];
        }
        
        $tallit_options = array(-1 =>"Kaikki tallit");
    
        foreach ($tallit as $talli){
            $tallit_options[$talli['tnro']] = $talli['nimi'];
        }
        
        
        $fields = array();
        $fields['rotu'] = array('type' => 'select', 'options' => $rodut_options, 'value' => $result['rotu'] ?? -1, 'class'=>'form-control');
        $fields['painotus'] = array('type' => 'enum', 'mode' => 'radios',
                                    'options' => array("0"=>"Ei jaottelua painotuksen mukaan", "1"=> "Jaottele painotuksen mukaan"),
                                    'value' => $result['painotus'] ?? 0);
        $fields['talli'] = array('type' => 'select', 'options' => $tallit_options, 'value' => $result['talli'] ?? -1, 'class'=>'form-control');
        $fields['minheight'] = array('label' => 'Minimisäkäkorkeus', 'type' => 'number', 'value' => $result['minheight'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);    

  
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    
    private function _read_kisalista_input(){
        $sort_values = array();
        $sort_values['rotu'] = -1;
        $sort_values['painotus'] = 0;
        $sort_values['talli'] = -1;
        $sort_values['minheight'] = 10;
        
        if($this->input->post('rotu')){
            $sort_values['rotu'] =  $this->input->post('rotu');
        }
        if($this->input->post('painotus')){
            $sort_values['painotus'] =  $this->input->post('painotus');
        }
        if($this->input->post('talli')){
            $sort_values['talli'] =  $this->input->post('talli');
        }
        if($this->input->post('minheight')){
            $sort_values['minheight'] =  $this->input->post('minheight');
        }
        
        return $sort_values;

    }
    
/////////////////////////////////////////////////////////////////////////////////////////////////////77
// KILPAILUKALENTERI
///////////////////////////////////////////////////////////////////////////////////////////////////////

function nayttelykalenteri (){

	$vars['msg'] = '';
		
	$vars['text_view'] = "";
        
    $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
    $vars['headers'][2] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
    $vars['headers'][3] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
    $vars['headers'][4] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli'));
    $vars['headers'][5] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
    $vars['headers'][6] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
    $vars['headers'][7] = array('title' => 'Hyväksytty', 'key' => 'hyvaksytty', 'type'=>'date');
        
   
    $vars['headers'] = json_encode($vars['headers']);
                
    $vars['data'] = json_encode(    $this->Kisakeskus_model->get_calendar_show());

	$vars['kalenteri'] = $this->load->view('misc/taulukko', $vars, TRUE);
	
    $vars['title'] = "Näyttelykalenteri";

    $this->fuel->pages->render('kilpailutoiminta/nayttelykalenteri', $vars);
    
    
}

function kilpailukalenteri ($type = "perinteiset"){

	$vars['msg'] = '';
		
	$vars['text_view'] = "";
    $vars['sivu'] = $type;
	
	$porrastettu = null;
    $arvontatapa = null;
        
    if($type == "perinteiset" || $type == "tarinalliset"){

        $porrastettu = 0;
        if($type == "tarinalliset"){
            $arvontatapa = 4;
        }
        $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
        $vars['headers'][2] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
        $vars['headers'][3] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
        $vars['headers'][4] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli'));
        $vars['headers'][5] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
        $vars['headers'][6] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
        $vars['headers'][7] = array('title' => 'Hyväksytty', 'key' => 'hyvaksytty', 'type'=>'date');
        
    }else if($type == "porrastetut"){
        $porrastettu = 1;

        $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
        $vars['headers'][2] = array('title' => 'VIP', 'key' => 'vip', 'type'=>'date');
        $vars['headers'][3] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
        $vars['headers'][4] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli/'));
        $vars['headers'][5] = array('title'=> 'Kutsu', 'key'=>'kisa_id', 'type'=>'url', 'static_text'=>'Kutsu', 'key_link' => site_url('kilpailutoiminta/k/'));
        $vars['headers'][6] = array('title' => 'Info', 'key' => 'info', 'type'=>'small');
        $vars['headers'][7] = array('title' => 'Hyväksytty', 'key' => 'hyvaksytty', 'type'=>'date');
    }
    
    $vars['headers'] = json_encode($vars['headers']);
                
    $vars['data'] = json_encode(    $this->Kisakeskus_model->get_calendar($porrastettu, $arvontatapa));

	$vars['kalenteri'] = $this->load->view('misc/taulukko', $vars, TRUE);
	
    $vars['title'] = "Kilpailukalenteri";

    $this->fuel->pages->render('kilpailutoiminta/kilpailukalenteri', $vars);
    
    
}

public function k($id = -1, $error = array()){
    
    if ($id == -1){
        
        redirect('kisakeskus', 'refresh');
        
    }
    else {
        $this->load->model('Kisakeskus_model');
        $this->load->helper('form');
        
        $data = $this->Kisakeskus_model->hae_kutsutiedot($id);
        $data['id'] = $id;
        $data['error'] = $error;
        $data['vip_sql'] = $data["vip"];
        $data['jaos'] = $this->Jaos_model->get_jaos($data['jaos']);
        $data['kp'] = date('d.m.Y', strtotime($data['kp']));
        $data['vip'] = date('d.m.Y', strtotime($data['vip']));
        $this->load->model('Sport_model');
        $data['laji'] = $this->Sport_model->get_sport_info($data['laji'])['painotus'];
        $this->load->model("Tallit_model");
        $data['talli'] = $this->Tallit_model->get_stable($data['jarj_talli']);
        
        $data['s_max_hevo_luokka_per_user'] = $this->kisajarjestelma->max_hevosia_per_luokka_per_ratsastaja($data['s_hevosia_per_luokka']); 
        $this->load->model("Tunnukset_model");
        $user = $this->ion_auth->user($this->Tunnukset_model->get_users_id($data['tunnus']))->row();
        $data['username'] = $user->nimimerkki;
        $data['user_email'] = $user->email;
        $data['user_vrl'] = $this->vrl_helper->get_vrl($user->tunnus);
        
        $data['tulos'] = $this->Kisakeskus_model->get_result(null, $data['kisa_id']);
        
        $this->fuel->pages->render('kisakeskus/kutsu', $data);
    }
}

	public function osallistu($id){
				
		$this->load->model('Kisakeskus_model');
		$errors = array();			
			
		$luokat = $this->Kisakeskus_model->hae_kisaluokat($id);
		$luokkanro = 1; 
		//jokainen luokka kerrallaan
		foreach ($luokat as $luokka){
			$osallistujalista = "";
			$osallistujalista = $this->input->post($luokka['kisaluokka_id'], TRUE);
			
			
			//jos oli osallistujia...
			if (strlen($osallistujalista) > 0){
				$osallistujalista = trim($osallistujalista);
				$osallistujat = explode("\n", $osallistujalista);
				$osallistujat = array_filter($osallistujat, 'trim');
				
				//Jokainen rivi/luokka käydään läpi
				foreach ($osallistujat as $rivi){
					$vh = "";
					$vrl = "";
					$vh_sijainti = 0;
					$vrl_sijainti = 0;
					
					$tunnuksia = preg_match_all('/\VRL\-[0-9]{5}/', $rivi, $vrl_osumat);
					preg_match('/\VRL\-[0-9]{5}/', $rivi, $vrl_osumat);

					//VRL-tunnus-tarkistelu
					if ($tunnuksia == 0){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Ei VRL tunnusta)";
						continue;
						
					}
					
					else if ($tunnuksia > 1){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Useampi kuin yksi VRL-tunnus)";
						continue;
					}
					
					else {						
						$vrl = $vrl_osumat[0];
						$vrl_sijainti = strpos($rivi, $vrl, 0);
					}
					
					//VH-tunnus-tarkistelu
					$tunnuksia = preg_match_all('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $rivi , $osumat);
					preg_match('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $rivi , $osumat);
					
					if ($tunnuksia == 0){
						
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Ei VH tunnusta)";
						continue;
					}
					
					else if ($tunnuksia > 1){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Useampi kuin yksi VH-tunnus)";
						continue;
					}
					
					
					else {
						
						$vh = $osumat[0];
						$vh_sijainti = strpos($rivi, $vh, 0);
					}
					
					//Löytyykö tageja
					$tageja = preg_match('/<[^>]*>/', $rivi, $tags);
                    //Onko VRL-tunnuksessa sulut?
					$sulkumaara = preg_match_all('/\(\VRL\-[0-9]{5}\)/', $rivi, $vrl_sulut);
                    
                    
					if ($tageja != 0){
						$errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Osallistumisessa on ylimääräisiä tageja)";
						continue;
						
					}	
				
					else if ($vrl_sijainti < 2 // VRL-tunnus ei saa olla alussa
					    OR $vh_sijainti < 15 //Ennen sitä pitää olla vähintään X(VRL-0000)-X verran kirjaimia
					    OR $vrl_sijainti > $vh_sijainti //VRL pitää olla ennen VH:ta
					    OR sizeof($vrl_sulut) == 0){
						
					    $errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (Jotain vikaa osallistumismuodossa)";
						continue;
					} else {
									
                        $osallistumistulos['result'] = false;
                        $osallistumistulos = $this->Kisakeskus_model->osallistuminen($this->vrl_helper->vh_to_number($vh), $this->vrl_helper->vrl_to_number($vrl), $id, $luokka['kisaluokka_id'], $rivi);
                        
                        if($osallistumistulos['result'] === false){
                            
                            $errors[] = "Luokka " . $luokkanro . ", " . $rivi . " (" . $osallistumistulos['message'].")";
                        }
                    }
				}
							
			}
			
			
			$luokkanro++;
			
		}
		
		$this->k($id, $errors);
	}


function tulosarkisto ($type = "perinteiset", $id = null, $id_type = null){
    
    if($this->ion_auth->logged_in())
    {
        $vars['msg'] = '';
            
        $vars['text_view'] = "";
        $vars['sivu'] = $type;
        
        $porrastettu = null;
        $arvontatapa = null;
        $nayttelyt = false;
            
        if($this->input->server('REQUEST_METHOD') == 'POST' && !isset($id))
        {
            if($this->input->post('id_type')){
                if($this->input->post('type') == '1'){
                    $this->tulosarkisto("bis", $this->input->post('id'), $this->input->post('id_type'));
                    $nayttelyt = true;
                }else {
                    $this->tulosarkisto("tulos", $this->input->post('id'), $this->input->post('id_type'));

                }
            }
        }
        
        else if($type == "tulos" || $type == "bis"){
            if($type == "bis"){
                $nayttelyt = true;
            }
            $tulos_info = array();
            if($id_type == "kisa_id"){
                $tulos_info = $this->Kisakeskus_model->get_result(null, $id, true, $nayttelyt);
                
            }else {
                $tulos_info = $this->Kisakeskus_model->get_result($id, null, true, $nayttelyt);
            }
                    
            if(sizeof($tulos_info) == 0){
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Antamallasi ID:llä ei löytynyt hyväksyttyjä tuloksia.'));
            }else {
                $tulos_info['jaos_info'] = $this->Jaos_model->get_jaos($tulos_info['jaos']);
                $this->load->model('Tallit_model');
                $tulos_info['talli_info'] = $this->Tallit_model->get_stable($tulos_info['jarj_talli']);
                $this->load->library('Kisajarjestelma');
                $this->load->model("Tunnukset_model");
                $tulos_info['tunnus'] =  $this->ion_auth->user($this->Tunnukset_model->get_users_id($tulos_info['tunnus']))->row();
                $tulos_info['tulosten_lah'] = $this->ion_auth->user($this->Tunnukset_model->get_users_id($tulos_info['tulosten_lah']))->row();
                if(isset($tulos_info['takaaja']) && $tulos_info['takaaja'] != 00000){
                    $tulos_info['takaaja'] =  $this->ion_auth->user($this->Tunnukset_model->get_users_id($tulos_info['takaaja']))->row();
                }
                if(isset($tulos_info['hyvaksyi']) && $tulos_info['hyvaksyi'] != 00000){
                    $tulos_info['hyvaksyi'] = $this->ion_auth->user($this->Tunnukset_model->get_users_id($tulos_info['hyvaksyi']))->row();
                }
                $tulos_info['jarjestelma']= & $this->kisajarjestelma;
                
                if($nayttelyt){
                    $tulos_info['tulos_id'] = $tulos_info['bis_id'];
                    $tulos_info['kisa_id'] = $tulos_info['nayttely_id'];
                    $tulos_info['porrastettu'] = false;
                    
                }
                $info = $this->load->view('kilpailutoiminta/tulos_info', array("tulos" => $tulos_info), TRUE);
                $luokat = "";
                if($nayttelyt){
                    
                    $bis_rivit = $this->Kisakeskus_model->get_showresult_rewards($tulos_info['bis_id']);
                    $taulu['headers'][1] = array('title' => 'Palkinto', 'key' => 'palkinto');
                    $taulu['headers'][2] = array('title' => 'Hevonen', 'key' => 'vh_nimi');
                    $taulu['headers'][3] = array('title' => 'Reknro', 'key' => 'vh_id', 'type'=>'VH', 'key_link' => site_url('virtuaalihevoset/hevonen/'));        
                    $taulu['headers'] = json_encode($taulu['headers']);
                            
                    $taulu['data'] = json_encode($bis_rivit);
                    $bis_tulokset = $this->load->view('misc/taulukko', $taulu, TRUE);
                    $luokat = $this->load->view('kilpailutoiminta/tulos_nayttelyt', array("tulokset" => $tulos_info, "bistulokset"=>$bis_rivit, "bistaulu"=>$bis_tulokset), TRUE);

                    

                }else {
                    
                    
                    $luokat = $this->load->view('kilpailutoiminta/tulos_luokat', array("tulos" => $tulos_info), TRUE);

                }

                $this->fuel->pages->render('kilpailutoiminta/tulos', array("tulos" => $tulos_info, "tulos_info"=>$info, "luokat_info"=>$luokat));
    
            }
            
        }
        
        else {
            
            if($type == "perinteiset" || $type == "tarinalliset"){
        
                $porrastettu = 0;
                if($type == "tarinalliset"){
                    $arvontatapa = 4;
                }
                $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
                $vars['headers'][2] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
                $vars['headers'][3] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli/'));
                $vars['headers'][4] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>'Kutsu');
                $vars['headers'][5] = array('title' => 'Tulos', 'key' => 'tulos_id', 'key_link'=> site_url('kilpailutoiminta/tulosarkisto/tulos/'));
                
            }else if($type == "porrastetut"){
                $porrastettu = 1;
        
                $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
                $vars['headers'][2] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
                $vars['headers'][3] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli/'));
                $vars['headers'][4] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
                $vars['headers'][5] = array('title' => 'Tulos', 'key' => 'tulos_id', 'key_link'=>site_url('kilpailutoiminta/tulosarkisto/tulos/'));
            }else if($type == "nayttelyt"){
                $nayttelyt = true;
        
                $vars['headers'][1] = array('title' => 'KP', 'key' => 'kp', 'type'=>'date');
                $vars['headers'][2] = array('title' => 'Jaos', 'key' => 'jaoslyhenne');
                $vars['headers'][3] = array('title' => 'Järjestäjä', 'key' => 'jarj_talli', 'reknro', 'key_link' => site_url('virtuaalitallit/talli/'));
                $vars['headers'][4] = array('title'=> 'Kutsu', 'key'=>'url', 'type'=>'url', 'static_text'=>"Kutsu");
                $vars['headers'][5] = array('title' => 'Tulos', 'key' => 'tulos_id', 'key_link'=>site_url('kilpailutoiminta/tulosarkisto/bis/'));
            }
        
            $vars['headers'] = json_encode($vars['headers']);
            
            $vars['id_form'] = $this->_result_id_search_form();
        
            if($nayttelyt){
                $vars['data'] = json_encode($this->Kisakeskus_model->get_showresults());
            }else {
                $vars['data'] = json_encode($this->Kisakeskus_model->get_results($porrastettu, $arvontatapa));
            }
        
            $vars['kalenteri'] = $this->load->view('misc/taulukko', $vars, TRUE);
            
            $vars['title'] = "Tulosarkisto";
        
            $this->fuel->pages->render('kilpailutoiminta/tulosarkisto', $vars);
        }
    }else {
        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Vain rekisteröityneet käyttäjät voivat tarkastella tulosarkistoa!'));

    }
    
    
}


private function _result_id_search_form($data = array()){
    $this->load->library('form_builder', array('submit_value' => 'Hae'));
    $fields['type'] = array('label'=>"Tyyppi", 'type'=>'select', 'options'=>array("0"=>"Kilpailut", "1"=>"Näyttelyt"), 'value'=>$data['type'] ?? 0, 'class'=>'form-control');
    $fields['id_type'] = array('label'=>"Hae id:llä", 'type' => 'select', 'options' => array("kisa_id"=>"Kilpailun id", "tulos_id"=>"Tuloksen id"), 'value' => $data['id_type'] ?? 'tulos_id', 'class'=>'form-control');
    $fields['id'] = array('label' => 'ID', 'type' => 'number', 'value' =>  $data['id_type'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);    
	$this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/kilpailutoiminta/tulosarkisto'));
    
    return $this->form_builder->render_template('_layouts/basic_form_template', $fields);

}


///////////////////////////////////////////////////////////////////////////////////////
// ANO KILPAILUJA
/////////////////////////////////////////////////////////////////////////////////////////


public function ilmoita_kilpailut($type, $jaos_id = null){
    $porrastettu = false;
    $nayttelyt = false;
    if(!($this->ion_auth->logged_in()))
    {
        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Vain rekisteröityneet käyttäjät voivat ilmoittaa kilpailuja kalenteriin!'));
    }
    
    
    else if ($type == "porrastetut"){
        $url_begin = "kilpailutoiminta/ilmoita_kilpailut/porrastetut/";
        if($jaos_id == null){
            //jos jaosta ei ole annettu, valitaan se*
            //luetaan se joko lomakkeelta tai näytetään lomake
            if($this->input->post('jaos')){
                $jaos_id = $this->input->post('jaos');
                redirect($url_begin.$jaos_id, 'refresh');
            }else {       
                $this->load->library('form_builder', array('submit_value' => 'Hae'));
                //haetaan listaan vain toiminnassa olevat ja porrastettuja tarjoavat kisat
                $jaos_options = $this->Jaos_model->get_jaos_option_list(true, true);
        
                $fields = array();
                $fields['jaos'] = array('type' => 'select', 'options' => $jaos_options, 'value' => 0, 'class'=>'form-control');
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url_begin));
                $data['form'] =  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
                
                $data['title'] = "Ilmoita porrastetut kilpailut.";
                $data['text_view'] = "<p>Valitse jaos</p>";
                $data['list'] = "";
                $this->fuel->pages->render('misc/haku', $data);
            }
        //jos jaos on tiedossa, luetaan lomake jos se on tarjolla, jos ei, vain nöytetään lomake
        }else {
            $kisa = array();
            $data = array();
            $porrastettu = true;

            if ($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->_handle_competition_application($porrastettu, $nayttelyt, $kisa, $data, $jaos_id);
                
            }
            
            $data['title'] = "Ilmoita porrastetut kilpailut";
            $data['form'] = $this->kisajarjestelma->get_competition_application ('add', 'kilpailutoiminta/ilmoita_kilpailut/porrastetut/'.$jaos_id, $porrastettu, $nayttelyt, $kisa, $jaos_id);
            $this->fuel->pages->render('misc/haku', $data);
        }
        
        
        
        

    }
    
    else if ($type == "perinteiset"){
        $data = array();
        $kisa = array();
        
        if ($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->_handle_competition_application($porrastettu, $nayttelyt,  $kisa, $data);
                
        }
            
        $data['title'] = "Ilmoita perinteiset kilpailut";
        $data['form'] = $this->kisajarjestelma->get_competition_application ('add', 'kilpailutoiminta/ilmoita_kilpailut/perinteiset', $porrastettu, $nayttelyt, $kisa);
        $this->fuel->pages->render('misc/haku', $data);

    }
    
    else if ($type == "nayttelyt"){
        $data = array();
        $kisa = array();
        $nayttelyt = true;
        
        if ($this->input->server('REQUEST_METHOD') == 'POST'){
                $this->_handle_competition_application($porrastettu, $nayttelyt, $kisa, $data);
                
        }
            
        $data['title'] = "Ilmoita näyttelyt";
        $data['form'] = $this->kisajarjestelma->get_competition_application ('add', 'kilpailutoiminta/ilmoita_kilpailut/nayttelyt', $porrastettu, $nayttelyt, $kisa);
        $this->fuel->pages->render('misc/haku', $data);

    }
    else
    {
        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Vain rekisteröityneet käyttäjät voivat ilmoittaa kilpailuja kalenteriin!'));
    }
    
}

private function _handle_competition_application($porrastettu, $nayttelyt, &$kisa, &$data, $jaos_id = null){
    $kisa = $this->kisajarjestelma->parse_competition_application();
    
    //jos kisa on porrastettu, jaos ei tule lomakkeelta ja lisätään erikseen
    if($porrastettu){
        $kisa['jaos'] = $jaos_id;
        $kisa['porrastettu'] = true;
    }else {
        $kisa['porrastettu'] = false;
    }
    
    //varmistetaan loput tiedot
                
    if(!$this->kisajarjestelma->validate_competition_application($porrastettu, $nayttelyt)){
        $data['msg_type'] = 'danger';
        $data['msg'] = "Virhe syötetyissä tiedoissa.";
        
    }else {
            //lisätään tunnus
        $kisa['tunnus'] = $this->ion_auth->user()->row()->tunnus;
        $onnollattu = false;
        $direct = false;
        $sallitut = 0;
        if(!$kisa['porrastettu'] && !$nayttelyt){
        
            //tsekataan etuuspisteet ihan ekana
            $etuuspisteet = $this->Jaos_model->GetEtuuspisteet($kisa['jaos'], $kisa['tunnus']);
            $ep = $etuuspisteet['pisteet'] ?? 0;
            $avoimet_kisat = $this->Jaos_model->usersOpenCompetitions($kisa['jaos'], $kisa['tunnus'], $kisa['porrastettu']);
            $jarjestettavia = $this->kisajarjestelma->sallitutKisamaarat($ep, $kisa['jaos']);
            if (isset($etuuspisteet['pisteet'])){
                $direct = $this->kisajarjestelma->directlyCalender($ep, $kisa['jaos']);
                if( $etuuspisteet['pisteet'] < 1 AND !empty($etuuspisteet['nollattu']) ) { $onnollattu = true; }

            }
   
            
            $sallitut = $jarjestettavia - $avoimet_kisat;

        }
        
        
        if(!$kisa['porrastettu'] && !$nayttelyt && $sallitut < 1){
            $data['msg_type'] = 'danger';
            $data['msg'] = "Etuuspisteesi (".$ep."p) eivät riitä. Sinulla on kalenterissa ". $avoimet_kisat . " avointa kilpailua.";
        }                
        else if(!$this->kisajarjestelma->check_competition_info('add', $kisa, $msg, $direct, $onnollattu)){
            $data['msg_type'] = 'danger';
            $data['msg'] = $msg;
        }else if (!$nayttelyt && !$this->kisajarjestelma->add_new_competition($kisa, $msg, $direct)){
            $data['msg_type'] = 'danger';
            $data['msg'] = $msg;
        }else if ($nayttelyt && !$this->kisajarjestelma->add_new_show($kisa, $msg)){
            $data['msg_type'] = 'danger';
            $data['msg'] = $msg;
        }else {
            $data['msg_type'] = 'success';
            $data['msg'] = "Kilpailun lisääminen onnistui";
        }
    }
}


//////////////////////////////////////////////////////////////////////////////
// ILMOITA TULOKSET
/////////////////////////////////////////////////////////////////////////////

public function ilmoita_nayttelytulokset($id){
    $this->ilmoita_tulokset($id, true);
}

public function ilmoita_tulokset($id, $nayttelyt = false){
    if(!($this->ion_auth->logged_in()))
    {
        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Vain rekisteröityneet käyttäjät voivat käsitellä kilpailuja!'));
    } else {
        
        $kisa = $this->Kisakeskus_model->hae_kutsutiedot($id, null, false, $nayttelyt);

        $user = $this->ion_auth->user()->row()->tunnus;
        if(sizeof($kisa) == 0 || !isset($kisa['hyvaksytty'])){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kilpailua ei ole olemassa, tai siitä on jo ilmoitettu tulokset.'));
        }
        else if(!$nayttelyt && $kisa['porrastettu'] == 1 && (sizeof($kisa['luokat']) > 0)){
            if($kisa['kp'] >= date('Y-m-d')){
                $this->db->trans_start();
                $this->porrastetut->ilmoita_tulokset_porrastetut($kisa, $user);
                $this->db->trans_complete();

            } else {
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type'=>'danger', 'msg' => 'Porrastettujen tuloksia ei voi arpoa ennen kilpailupäivää!'));
            }
        }
        else if(!($kisa['tunnus'] == $user || $kisa['takaaja'] == $user)){
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kilpailu ei ole sinun järjestämäsi etkä ole sen takaaja.'));
        } else if($nayttelyt){
            $this->_ilmoita_tulokset_nayttelyt($kisa, $user);
        }
        else {
             $this->_ilmoita_tulokset_perinteiset($kisa, $user);

        }
        
    }
}


private function _alusta_kutsun_tiedot($kisa){
    $data = $kisa;
        $data['jaos'] = $this->Jaos_model->get_jaos($data['jaos']);

        $data['kp'] = date('d.m.Y', strtotime($data['kp']));
        $data['vip'] = date('d.m.Y', strtotime($data['vip']));
        $this->load->model('Sport_model');
        $data['laji'] = $this->Sport_model->get_sport_info($data['laji'])['painotus'];
        $this->load->model("Tallit_model");
        $data['talli'] = $this->Tallit_model->get_stable($data['jarj_talli']);
                $this->load->model("Tunnukset_model");
        $jarj = $this->ion_auth->user($this->Tunnukset_model->get_users_id($data['tunnus']))->row();
        $data['username'] = $jarj->nimimerkki;
        $data['user_email'] = $jarj->email;
        $data['user_vrl'] = $this->vrl_helper->get_vrl($jarj->tunnus);
        
        return $data;
}


private function _ilmoita_tulokset_perinteiset($kisa, $user){
    //haetaan kutsun tiedot
     $data = $this->_alusta_kutsun_tiedot($kisa);   
        
    //jos luokat annettu, tee lähetysformi
    if($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('luokat')){
        $data['form'] = $this->_make_result_form($this->input->post('luokat'), $data['jaos']['id']);
        $this->fuel->pages->render('kilpailutoiminta/ilmoita_tulokset', $data);
    
    }else     if($this->input->server('REQUEST_METHOD') == 'POST' && $this->input->post('class_amount')){
        $data['form'] = $this->_make_result_form($this->input->post('class_amount'), $data['jaos']['id'], true);
        $this->fuel->pages->render('kilpailutoiminta/ilmoita_tulokset', $data);
    
    }
    //jos ei luokkia, käsittele tulokset
    else if($this->input->server('REQUEST_METHOD') == 'POST'){
        if($this->_send_result($kisa, $user, $send_msg)){
            $this->omat("kisat", 'avoimet', array('msg_type' => 'success', 'msg' => 'Tulos lähetetty käsittelyjonoon!'));
        }else {
            $this->fuel->pages->render('misc/naytaviesti', $send_msg);
        }
    }
    //muussa tapauksessa kysy luokat
    else  {
        $data['luokat'] = true;	        
        $data['form'] =  $this->_make_classes_form($kisa, $user);
        $this->fuel->pages->render('kilpailutoiminta/ilmoita_tulokset', $data);

    }
        
}

private function _ilmoita_tulokset_nayttelyt($kisa, $user){
    //haetaan kutsun tiedot
     $data = $this->_alusta_kutsun_tiedot($kisa);   
        
    if($this->input->server('REQUEST_METHOD') == 'POST'){
        if($this->_send_show_result($kisa, $user, $send_msg)){
            $this->omat('nayttelyt', 'tulosjonossa',  array('msg_type' => 'success', 'msg' => 'Tulos lähetetty käsittelyjonoon!'));
        }else {
            $this->fuel->pages->render('misc/naytaviesti', $send_msg);
        }
    }
    else  {
        $data['form'] =  $this->_make_show_result_form($data['jaos']['id']);
        $this->fuel->pages->render('kilpailutoiminta/ilmoita_tulokset', $data);

    }
        
}


private function _make_classes_form($kisa, $user){
    if($kisa['porrastettu'] == 0){
        $classes = $this->Kisakeskus_model->get_latest_result_classes($kisa['jaos'], $user);
        $this->load->library('form_builder', array('submit_value' => 'Ilmoita'));
        $fields['luokat'] = array('type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'no_editor', 'class'=>'form-control','value'=>$classes ?? "",
                                  'after_html'=>'<span class="form-comment">Ilmoita luokat numeroituna (1. 2. 3. jne.) sekä luokan taso esim. 1. Helppo A 2. Vaativa A jokainen luokka omalle riville. Älä lisää luokkanumeron eteen ylimääräisiä merkkejä tai tekstiä, kuten "Luokka". Jotkin jaokset edellyttävät myös avoimuuksien yms. kirjaamista. Noudata jaoksen ohjeita!</span>'

);
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('kilpailutoiminta/ilmoita_tulokset/'.$kisa['kisa_id']));		        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    
    }
    
    else {
        $this->load->library('form_builder', array('submit_value' => 'Ilmoita'));
        $fields['class_amount'] = array('label' => 'Luokkien määrä', 'type' => 'number', 'required'=>TRUE,
                                                'min' => 1, 'max'=>100,
                                                'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);
                $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('kilpailutoiminta/ilmoita_tulokset/'.$kisa['kisa_id']));		        
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
}

private function _make_result_form($luokat, $jaos_id, $porr = false){

    
    
    $this->load->library('form_builder', array('submit_value' => 'Lähetä'));
    
    
        if($porr){
            $class_options = $this->Jaos_model->get_class_options($jaos_id, true, true);
            $nro = 1;
             while ($nro <= $luokat){

                $fields['tulos_'.$nro.'_h'] = array('type' => 'section', 'tag' => 'h3', 'value' => 'Luokka '.$nro, 'display_label' => FALSE);
                $fields['tulos_'.$nro.'_luokka'] = array('label'=>'Luokka','type' => 'select', 'options' => $class_options, 'class'=>'form-control');
                $fields['tulos_'.$nro.'_os'] = array('label'=>'Osallistujat', 'type' => 'textarea', 'cols' => 40, 'rows' => 10, 'class' => 'form-control');

                
                $nro = $nro + 1;
            }
        }else {

        $luokat = explode("\n",$luokat);
        $luokat = preg_grep('/^\s*\z/', $luokat, PREG_GREP_INVERT);
        $luokat = array_values( array_filter($luokat) );
        
        $nro = 1;
            foreach ($luokat as $luokka){
                $fields['tulos_'.$nro.'_h'] = array('type' => 'section', 'tag' => 'h3', 'value' => $luokka, 'display_label' => FALSE);
                $fields['tulos_'.$nro.'_os'] = array('label'=>'Osallistujat', 'type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'form-control');
                $fields['tulos_'.$nro.'_hyl'] = array('label'=>'Hylätyt', 'type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'form-control');
                $fields['tulos_'.$nro.'_luokka'] = array('type'=>'hidden', 'value'=>$luokka);
                
                $nro = $nro + 1;
            }
        }
        
        echo "<br>luokkia on " . $nro;
        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
}


private function _make_show_result_form($jaos_id){
    $this->load->library('form_builder', array('submit_value' => 'Lähetä'));
    $palkinnot = $this->Jaos_model->get_reward_list($jaos_id);
    
    $fields['paatuomari_nimi'] = array('type' => 'text', 'class'=>'form-control', 'required' => TRUE, 'label'=>"Päätuomari");
    $fields['luokkatuomarit_nimi'] = array('type' => 'text', 'class'=>'form-control', 'label'=>"Muut tuomarit");


    
    $fields['tulokset'] = array('label'=>'Tulokset', 'required'=>TRUE, 'type' => 'textarea', 'cols' => 40, 'rows' => 20, 'class' => 'form-control',
                                 'after_html'=>'<span class="form-comment">Jaoksen sääntöjen mukaisessa muodossa!</span>');
    
    if(sizeof($palkinnot) > 0){
        $fields['palkinnot_otsikko'] = array('type' => 'section', 'tag' => 'h3', 'value' => 'Palkinnot', 'display_label' => FALSE);
        foreach ($palkinnot as $palkinto){
            $fields[$palkinto['id']] = array('label'=>$palkinto['palkinto'], 'type' => 'textarea', 'cols' => 40, 'rows' => 5, 'class' => 'form-control',
                                 'after_html'=>'<span class="form-comment">Vapaavalintaisessa muodossa, mutta vain yksi rekisterinumero per rivi!
                                 Vain tähän listatut hevoset saavat palkinnon rekisteriprofiilisivulle.</span>');
        }
    
    }else {
        $fields['palkinnot_otsikko'] = array('type' => 'section', 'tag' => 'h3', 'value' => 'Näistä näyttelyistä saadut palkinnot eivät tule näkyviin profiilisivulla!', 'display_label' => FALSE);

    }
    
    
        
        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
}

private function _send_result($kisa, $user, &$msg = array()){
        $this->db->trans_start();
      
            $kantaan_luokat = "";
            $kantaan_tulokset = "";
            $kantaan_hylsyt = "";
            $kantaan_op = array();
            
    
        if($kisa['porrastettu'] == 0){
            $nro = 1;
            while (true) {
                $luokka = $this->input->post('tulos_'.$nro.'_luokka');
                if(isset($luokka) && strlen($luokka) > 5){
                    $kantaan_luokat = $kantaan_luokat . $this->input->post('tulos_'.$nro.'_luokka') . "\n";
                    $kantaan_tulokset = $kantaan_tulokset . $this->input->post('tulos_'.$nro.'_os') . "~";
                    $kantaan_hylsyt = $kantaan_hylsyt . $this->input->post('tulos_'.$nro.'_hyl') . "~";
                    $nro = $nro+1;
                }else {
                    break;
                }
            }

            
            $tulos = array();
            $tulos['tunnus'] = $user;
            $tulos['ilmoitettu'] = date('Y-m-d H:i:s');
            $tulos['tulokset'] = $kantaan_tulokset;
            $tulos['hylatyt'] = $kantaan_hylsyt;
            $tulos['luokat'] = $kantaan_luokat;
            $tulos['kisa_id'] = $kisa['kisa_id'];
    
            $kutsu = $this->Kisakeskus_model->hae_kutsutiedot($kisa['kisa_id'], null, 0);
            if(sizeof($kutsu) > 0){
                $this->db->insert('vrlv3_kisat_tulokset', $tulos);
                $this->db->set('tulokset', 1);
                $this->db->where('kisa_id', $tulos['kisa_id']);
                $this->db->update('vrlv3_kisat_kisakalenteri');
            }
            
            
        }
        else {
            
            $this->porrastetut->ilmoita_tulokset_porrastetut($kisa, $user);
            
        }
        

            $this->db->trans_complete();
            return true;

}



private function _send_show_result($kisa, $user, &$msg = array()){
            $this->db->trans_start();
            $palkinnot = $this->Jaos_model->get_reward_list($kisa['jaos'], false);
            $all_rewarded = array();
            $all_vhs = array();

            foreach ($palkinnot as $palkinto){
                 $this->_sort_rewarded_horses($palkinto, $all_rewarded, $all_vhs);
            }
            
            
            
            $tulos = array();
            $tulos['tunnus'] = $user;
            $tulos['paatuomari_nimi'] = $this->input->post('paatuomari_nimi');
            $tulos['luokkatuomarit_nimi'] = $this->input->post('luokkatuomarit_nimi');
            $tulos['ilmoitettu'] = date('Y-m-d H:i:s');
            $tulos['tulokset'] =  $this->input->post('tulokset');
            $tulos['nayttely_id'] = $kisa['kisa_id'];
    
            $kutsu = $this->Kisakeskus_model->hae_kutsutiedot($kisa['kisa_id'], null, 0, true);
            if(sizeof($kutsu) > 0){
                
                //merkitään tulokselliseksi ja tallennetaan bis-tulos
                $this->db->insert('vrlv3_kisat_nayttelytulokset', $tulos);
                $bis_id = $this->db->insert_id();
                $this->db->set('tulokset', 1);
                $this->db->where('kisa_id', $tulos['nayttely_id']);
                $this->db->update('vrlv3_kisat_nayttelykalenteri');
                
                //lisätään tulosrivit
                $this->_check_horses_exists($all_vhs, $tested_vhs, $vh_names);

                
                //rakennetaan tulosrivit
                $tulosrivit = array();
                
                foreach($all_rewarded as $palkinto=>$vh_list){
                    
                    foreach($vh_list as $vh){
                        //jos vh on olemassa, laitetaan tulosrivi kasaan
                        if(in_array($vh, $tested_vhs)){
                            $tulosrivi['bis_id'] = $bis_id;
                            $tulosrivi['nayttely_id'] = $kisa['kisa_id'];
                            $tulosrivi['palkinto'] = $palkinto;
                            $tulosrivi['vh_nimi'] = $vh_names[$vh];
                            $tulosrivi['vh_id'] = $vh;
                            $tulosrivi['vh'] = $vh;
                            
                            $tulosrivit[] = $tulosrivi;
                        }
                    }
                }
                $this->db->insert_batch('vrlv3_kisat_bis_tulosrivit',$tulosrivit);
                
            }
            
            

            $this->db->trans_complete();
            return true;

}



private function _sort_rewarded_horses($palkinto, &$all_rewarded, &$all_vhs){
    $palkitut = $this->input->post($palkinto['id']);
    $palkitut = explode("\n",$palkitut);
    $palkitut = preg_grep('/^\s*\z/', $palkitut, PREG_GREP_INVERT);
    $palkitut = array_values( array_filter($palkitut));
    
    if (isset($palkitut)){
        foreach($palkitut as $rivi){
            $tunnuksia = preg_match_all('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $rivi , $osumat);
        
            if ($tunnuksia > 0){
                
                foreach($osumat[0] as $osuma){
                    // Otetaan se talteen.
                    $vh = $this->vrl_helper->vh_to_number($osuma);
                    $all_rewarded[$palkinto['palkinto']][] = $vh;
                    $all_vhs[] = $vh;
                }
            }

        }
    }
}

private function _check_horses_exists($all_vhs, &$tested_vhs, &$vh_names){
                    //haetaan vh:t hepparekasta ja tarkastetaan
                
    $this->db->select('reknro, nimi');
    $this->db->where_in('h.reknro', $all_vhs);
    $query = $this->db->get('vrlv3_hevosrekisteri as h');

    $vh_haku = $query->result_array();
    $tested_vhs = array();
    $vh_names = array();
    foreach($vh_haku as $vh){
        $tested_vhs [] = $vh['reknro'];
        $vh_names[$vh['reknro']] = $vh['nimi'];
    }   
}

    
    
    ////vanha kisajärjestelmö
    
    

	
	function getSport ( $id ) {
	
		$getDivisionNum = mysql_query("
								SELECT jaos 
								FROM kisat_kisakalenteri 
								WHERE kisa_id = '".$id."'
								");
					
		$division = mysql_fetch_array($getDivisionNum);
			
		return $division['jaos'];
		
	}
	
	function getDivisionName ( $id ) {
	
		$getDivisionName = mysql_query("
								SELECT lyhenne 
								FROM lista_jaokset 
								WHERE jaos_id = '".$id."'
								");
					
		$division = mysql_fetch_array($getDivisionName);
			
		return strtoupper($division['lyhenne']);
		
	}
	
	/**
		Tutkii, onko henkilöllä aiempia kisoja kys. jaokselta. Palauttaa kisojen määrän.
	*/
	function countPersonRaces($vrl, $division) {
			$getCount = mysql_query("
					SELECT count(kisa_id) AS maara 
					FROM kisat_kisakalenteri 
					WHERE 
						tunnus = '".$vrl."' AND 
						jaos = '".$division."' AND 
						vanha = 0 AND
						hyvaksytty IS NOT NULL AND
						tulokset IS NOT NULL
					");
				
			$count = mysql_fetch_array($getCount);
			
			return $count[0];
		
	}
	
	/**
		Tutkii, onko henkilöllä aiempia kisoja kys. jaokselta. Palauttaa kisojen määrän.
	*/
	function countResultApplications($division, $porrastettu = 0) {
	
		$howMany = mysql_query("
								SELECT count(tulos_id) FROM kisat_tulokset
								WHERE 
										(kisat_tulokset.hyvaksytty IS NULL OR 
										kisat_tulokset.hyvaksytty = '0000-00-00 00:00:00')
									AND
									kisa_id IN
										(
											SELECT kisa_id 
											FROM kisat_kisakalenteri 
											WHERE 
												jaos = ".$division."  AND 
												porrastettu = ".$porrastettu." AND 
												hyvaksytty IS NOT NULL AND (tulokset = 0 OR tulokset IS NULL)
										) 
								") or die("Problem at tulosjonohaku!".mysql_error());
		
		$count = mysql_fetch_array($howMany);	
		return $count[0];
		
	}
	
	/**
		Lisää henkilölle etuuspisteet
		$vrl		VRL-tunnus
		$points		Lisättävät pisteet
		$division	Jaos
	*/
	function updateRacePoints($vrl, $points, $division) {
	
		$getPoints = mysql_query("SELECT tunnus FROM tunnukset_etuuspisteet WHERE tunnus = ".$vrl." LIMIT 0,1");
		
		if( mysql_num_rows($getPoints) > 0 ) {
		
			$newPoints = mysql_query("
					UPDATE tunnukset_etuuspisteet 
					SET ".$division." = ".$division."+".$points."
					WHERE tunnus = ".$vrl.";");	
					
		} else {
			$newPoints = mysql_query("
											INSERT INTO tunnukset_etuuspisteet (".$division.", tunnus) 
											VALUES ('".$points."', ".$vrl.")"
											);
		}

		if($newPoints) {
			print 	'<p class="ok">'.$points.' '.$division.'-etuuspistettä annettiin tulosten lähettäjälle VRL-'.$vrl.'.</p>';
		} else {
			print '<p class="error">Etuuspisteitä ei voitu päivittää!.</p>';
		}	

		return TRUE;		
	}
	
	/**
		Palauttaa etuuspisteet php-arrayna kultakin jaokselta sekä yhteensä.
		$points['JAOS'] = jaoksen etuuspisteet | ERJ, KERJ, KRJ, ARJ, VVJ, WRJ, NJ
		$points['sum']	= etuuspisteet yhteensä
	*/
	function selectLotteryStress($type) {
		if( $type > 0 AND $type < 5 ) {
			switch($type) {
				case 1:
					$stress = 1;
					break;
				case 2:
					$stress = 1.5;
					break;
				case 3:
					$stress = 1.5;
					break;
				case 4:
					$stress = 2;
					break;
			}
		} else {
			$stress = 1;
		}
		
		return $stress;		
	}
	
	/**
		Palauttaa etuuspisteet php-arrayna kultakin jaokselta sekä yhteensä.
		$points['JAOS'] = jaoksen etuuspisteet | ERJ, KERJ, KRJ, ARJ, VVJ, WRJ, NJ
		$points['sum']	= etuuspisteet yhteensä
	*/
	function countRankingPoints($order, $stress) {
		
		if($order <= 3) {
			switch ($order) {
				case 1:
					$points = 6 * $stress;
					break;
				case 2:
					$points = 4 * $stress;
					break;
				case 3:
					$points = 2 * $stress;
					break;	
			}
		} elseif ($order >= 3 AND $order <= 10) {
			$points = 1 * $stress;
		} else {
			$points = 0;
		}
		
		return $points;		
	}
	
	/**
		Lisää rankinpisteet tietokantaan.
		$vrl		VRL-tunnus
		$points		Lisättävät pisteet
		$division	Jaos
	*/
	function addRankingPoints($vrl, $points, $division) {
		
		$year = date("y");
		$addPoints = mysql_query("
									INSERT INTO ranking_".$year." (tunnus, ".$division.") 
									VALUES (".$vrl.", +".$points.") 
									ON DUPLICATE KEY UPDATE ".$division." = ".$division."+".$points
								);
		
		if($addPoints) {
			// print 'Lisättiin ranking-pisteitä kantaan: '.$points.', tunnukselle: '.$vrl;
			return TRUE;
		} else {
			// print 'Ei lisätty ranking-pisteitä kantaan: '.$points.', tunnukselle: '.$vrl;
			return FALSE;
		}
	}
	
############### PORRASTETUT ALKAVAT TÄSTÄ
###############


/* Tarkistaa tietokannasta, mitä "porrastettu"-kenttään on laitettu. 1 = porrastettu, 2 = ei porrastettu */
function Leveled ( $id  ) {

	// Hae kilpailu tietokannasta
	$getLeveled = mysql_query("
				SELECT porrastettu
				FROM kisat_kisakalenteri
				WHERE kisa_id = '$id'
				") or die ( mysql_error() );
	
	$queryResult = mysql_fetch_assoc($getLeveled);
	$result = $queryResult['porrastettu']; 
	
	return $result;
}

/*  */
function getPropertiesSport ( $sport  ) {

	// 1. Hae tietokannasta lajin $sport tiedot
	$getSport = mysql_query("
				SELECT lista_ominaisuudet.alias
				FROM lista_ominaisuudet
				LEFT JOIN lista_vaikutukset
					ON lista_ominaisuudet.id = lista_vaikutukset.ominaisuus
				WHERE lista_vaikutukset.laji = $sport
				") or die ( mysql_error() ); // nrolla 3: kuuliaisuus_luonne, tahti_irtonaisuus

	$ominaisuudet = array();
	
	// 2. Työnnä ominaisuuksien aliakset taulukkoon, jotta voidaan käyttää 3. vaiheessa hyväksi
	while ( $ominaisuus = mysql_fetch_array($getSport, MYSQL_ASSOC) ) {
		array_push($ominaisuudet, $ominaisuus['alias']);
	}
	
	return $ominaisuudet;
}


/* Tarkasta hevosen ominaisuuspisteet lajissa */
/* checkPropertyPoints( '000350006', 3 )  */
function checkPropertyPoints( $vh, $sport ) {
	// $sport = nro, esim. "1"

	// 1. Hae tietokannasta lajin $sport tiedot
	// Haetaan lajin ID
	$getSport = mysql_query("
				SELECT lista_ominaisuudet.alias
				FROM lista_ominaisuudet
				LEFT JOIN lista_vaikutukset
					ON lista_ominaisuudet.id = lista_vaikutukset.ominaisuus
				WHERE lista_vaikutukset.laji = $sport
				") or die ( mysql_error() ); // nrolla 3: kuuliaisuus_luonne, tahti_irtonaisuus

	$ominaisuudet = array();
	
	// 2. Työnnä ominaisuuksien aliakset taulukkoon, jotta voidaan käyttää 3. vaiheessa hyväksi
	while ( $ominaisuus = mysql_fetch_array($getSport, MYSQL_ASSOC) ) {
		array_push($ominaisuudet, $ominaisuus['alias']);
	}
	
	// Siistitään VH
	$vh = clearVh( $vh );
			
	// 3. Tarkista, paljonko hevosella on ominaisuuksissa pisteitä
	$getPoints = mysql_query("
		SELECT ".implode(", ", $ominaisuudet)."
		FROM hevosrekisteri_ominaisuudet										
		WHERE reknro = '$vh'
		") or die ( "" );  
		
	$points = mysql_fetch_assoc($getPoints);

	$properties[0] = $points[$ominaisuudet[0]];
	$properties[1] = $points[$ominaisuudet[1]];
	
	if( empty ($properties[0]) ) { $properties[0] = 0; }
	if( empty ($properties[1]) ) { $properties[1] = 0; }
	
	// 4. Palauta hevosen ominaisuuspisteet taulukossa
	return $properties;
}

function checkHeight( $vh ) {
	$vh = clearVh( $vh );	
	
	$getHeight = mysql_query("
						SELECT sakakorkeus
						FROM hevosrekisteri_perustiedot
						WHERE reknro = $vh
						LIMIT 0,1
						");
	$gotHeight = mysql_fetch_assoc($getHeight);

	if ( empty ($gotHeight['sakakorkeus']) ) {
		$height = 0;
	} else {
		$height = $gotHeight['sakakorkeus'];
	}
	
	return $height;
}

function checkHorseInfo( $vh ) {
	$vh = clearVh( $vh );	
	
	$getInfo = mysql_query("
						SELECT sakakorkeus, rotu
						FROM hevosrekisteri_perustiedot
						WHERE reknro = $vh
						LIMIT 0,1
						");
	$information = mysql_fetch_assoc($getInfo);

	if ( empty ($information['sakakorkeus']) ) {
		$height = 0;
	} else {
		$height = $information['sakakorkeus'];
	}
	if ( empty ($information['rotu']) ) {
		$breed = 0;
	} else {
		$breed = $information['rotu'];
	}
	
	$information['breed'] = $breed;
	$information['height'] = $height;
	
	return $information;
}


	
	
/* Tarkastaa, mille tasolle hevonen kuuluu lajissa $sport */
function checkLevel ( $vh, $sport ) {

	// 1. Tarkista ikä
	$age = checkAge( $vh );
	// print '-'.$age.'-';
	
	// 2. Tarkista ominaisuuspisteet lajista
	$propertypoints = checkPropertyPoints( $vh, $sport );
	
	// 3. Laske ominaisuuspisteet yhteen
	$properties = $propertypoints[0] + $propertypoints[1];
	
	$levelByAge = levelByAge($age);
	$levelByProperties = levelByProperties($properties);
	
	//Oletus, että taso on se, mihin ominaisuuspisteillä päästään.
	
	$level = $levelByProperties;
	
	//Jos hevonen ei ole tarpeeksi vanha nousemaan tasolta, se ei nouse, vaikka pisteet riittäisi
	if ($levelByAge < $levelByProperties){
		$level = $levelByAge;
	}
	
	//Jos hevonen ei ole kisaikäinen
	if ($level == -1){
		$level = 'Hevonen on liian nuori kilpailemaan ('.$age.' vuotta)';
	}
	
	$information['points'] = $properties;
	$information['level'] = $level;
	
	return $information;
	
}



function levelByProperties ( $properties ){	
	if ( properties >= 0 AND $properties < 201) {
		$level = 0; 
	} elseif ( $properties >= 201 AND $properties < 601 ) {
		$level = 1; 
	} elseif ($properties >= 601 AND $properties < 1001) {
		$level = 2; 
	} elseif ( $properties >= 1001 AND $properties < 1401) {
		$level = 3; 
	} elseif ($properties >= 1401 AND $properties < 1801 ) {
		$level = 4;
	} elseif ($properties >= 1801 AND $properties < 2401) {
		$level = 5; 
	} elseif ($properties >= 2401 AND $properties < 3001 ) {
		$level = 6; 
	} elseif ($properties >= 3001 AND $properties < 3801 ) {
		$level = 7; 
	} elseif ($properties >= 3801 AND $properties < 4601) {
		$level = 8;
	} elseif ( $properties >= 4601 AND $properties < 5601 ) {
		$level = 9; 
	} elseif ($properties >= 6501) {
		$level = 10; 
	} else {
		$level = 11;	
	}
	
	return $level;
	
}

function allClasses ( $sport ) {

	$getClasses = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE laji = '$sport'
						ORDER BY taso ASC
						");
						
						
	$classes = array();
	$counter = 0;
	
	while($dbclass = mysql_fetch_assoc($getClasses)) {
		$classes[$counter]['id'] = $dbclass['id'];
		$classes[$counter]['nimi'] = $dbclass['nimi'];
		$classes[$counter]['taso'] = $dbclass['taso'];
		$counter++;
		
	}
						
	return $classes;
}

function getClassInfo ( $class ) {

	$getClass = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE id = '$class' OR nimi = '$class'
						");
						
						
	$class = array();
	$counter = 0;
	
	while($dbclass = mysql_fetch_assoc($getClass)) {
		$class[$counter]['id'] = $dbclass['id'];
		$class[$counter]['nimi'] = $dbclass['nimi'];
		$class[$counter]['taso'] = $dbclass['taso'];
		$class[$counter]['aste'] = $dbclass['aste'];
		$class[$counter]['minheight'] = $dbclass['minheight'];
		$counter++;
		
	}

	return $class;
}

function classesToPart ( $level, $sport, $height ) {

	if( $level == 1 ) {
		$tasot = 'taso = 0 OR taso = 1 OR taso = 2';
	} elseif ( $level == 2 ) {
		$tasot = 'taso = 1 OR taso = 2 OR taso = 3';
	} else {
		$tasot = 'taso = '.$level.' OR taso = '.($level+1);
	}

	$getClasses = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE 
							laji = $sport AND
							($tasot) AND
							(
								minheight IS NULL OR 
								minheight <= $height
							) 
						");
	$classes = array();
	
	while ( $class = mysql_fetch_array($getClasses, MYSQL_ASSOC) ) {
		$handled = $class['nimi'].', taso '.$class['taso'];
		
		if ( $class['minheight'] != NULL ) {
			$handled .= ' (sk. '.$class['minheight'].'cm )';
		}
		
		array_push($classes, $handled);
	}
	return $classes;
}

function generateResults ( $participants, $class, $sport  ) {
	
	$participants = array_diff( $participants, array('') );
	
	// print_r( $participants ); print '<hr />';
	
	for ($i = 0; $i < count($participants); $i++) {
		$horse = $participants[$i];
		
		// 1. Tarkistetaan, onko rivillä VH-tunnusta
		if( strpos( strtoupper($participants[$i]), "VH") !== FALSE ) {
			// 1.0 Otetaan VH-tunnus talteen ja jatketaan
			$vh = substr($participants[$i], strpos($participants[$i], "VH"),13);
			$vh = str_replace('-', '', $vh);
			$vh = substr( $vh, (-9) );
			
			// 1.1. Tarkista hevosen ikä ja millä tasolla hevonen on 
			$age = checkAge( $vh ); 
			$horselevel = checkLevel ( $vh, $sport );
			
			// 1.2. Tarkasta hevosen säkäkorkeus ja rotu
			// $height = checkHeight( $vh );
			$information = checkHorseInfo( $vh );
			$height = $information['height'];
			$breed = $information['breed'];
			
			// HAE MINIMI-IKÄ JA SÄKÄ, MILLÄ HEVONEN VOI OSALLISTUA
			// $class = luokan ID
			$classinfo = getClassInfo ( $class );
			
			if ( $age >= 3 ) {
				// Hevonen saa kilpailla vaan omalla tasollaan ja yhtä ylemmällä tasolla. 			
				// Jos hevosen taso on 2 ja luokan taso 0
				// Jos hevosen taso on sama kuin luokan taso
				// Jos hevosen taso on yhtä isompi kuin luokan taso | hevonen lv 2 voi osallistua lk lv3
				if ( 
					( $horselevel['level'] == 1 && $classinfo[0]['taso'] == 0 ) OR
					( $horselevel['level'] == 2 && $classinfo[0]['taso'] == 1 ) OR
					$horselevel['level'] == $classinfo[0]['taso'] OR 
					($horselevel['level']+1) == $classinfo[0]['taso']
					) {
					
					/*
						horselevel['level']== 2 && clasinfo = 0
					*/
					
					// print $classinfo[0]['taso'].'vs'.$horselevel['level'];
				
					if ( $height >= $classinfo[0]['minheight'] ) {
					
						if ( $classinfo[0]['nimi'] == 'CIC2, avoin - ei sh (kansallinen)' AND $breed == '018' ) {
							$failed[] = array('horse' => $horse, 'reason' => 'Suomenhevonen ei voi osallistua tähän luokkaan');
							
						} else {
						
							// print $classinfo[0]['minheight'].'vs'.$height;
						
							// 1.3. Hae hevosen ominaisuuspisteet
							$propertypoints = checkPropertyPoints( $vh, $sport ); // Hae ominaisuudet
							$horsePropertyPoints = $propertypoints[0] + $propertypoints[1]; // Laske ominaisuuspisteet yhteen
							
							// 1.4. Laske hevoselle pistemäärä ja lisää se taulukkoon $accepted
							
							
							$pointsInClass = ( $horsePropertyPoints / 3 )+(rand(0,100)/100) * ( (rand(0,100)/100) + (rand(0,100)/100) + (rand(0,100)/100) + 1.00);
							$accepted[] = array('vh' => $vh, 'horse' => $participants[$i], 'points' => $pointsInClass);
						}
						
					} else {
						$failed[] = array('horse' => $horse, 'reason' => 'Hevosella ei ole riittävää säkäkorkeutta');
					}
					
				} else {
					$failed[] = array('horse' => $horse, 'reason' => 'Hevosen taso ei ole riittävä tai se on liian korkea: lk. '.$classinfo[0]['taso'].' vs. hevonen '.$horselevel['level']);
				}
			} else {
				$failed[] = array('horse' => $horse, 'reason' => 'Hevonen on liian nuori kilpailemaan');
			}
			
			
		} else {
			if ( !empty($horse) AND $horse != '' ) {
				$failed[] = array('horse' => $horse, 'reason' => 'Hevosella ei ole VH-tunnusta');
			} else {
				print '';
			}
		}
	}
	
	// 2.0 Järjestä kaikki osallistujat taulukossa $accepted
	foreach ($accepted as $key => $row) {
		$points[$key] = $row['points'];
		$vhIdentification[$key]  = $row['vh'];
	}
	
	array_multisort($points, SORT_DESC, $vhIdentification, SORT_DESC, $accepted);
	
	// print_r($accepted);
	
	// 2.5 Järjestä failed-taulussa kaikki osallistujat
	foreach ($failed as $key => $row) {
		$horseIdentification[$key]  = $row['horse'];
		$reason[$key] = $row['reason'];
	}
	
	array_multisort($horseIdentification, SORT_DESC, $reason, SORT_DESC, $failed);
	
	print '<hr />';
	
	$results = array($accepted, $failed);
	
	// 3. Palauta $results
	return $results;
}
	
/* Lisää hevoselle ominaisuuspisteet */
function addPropertyPoints ( $vh, $classinfo ) {
	//	$vh = hevosen VH-tunnus | $classinfo = taulukko luokan tiedoille
	//		$classinfo['participants']	Osallistujien yhteismäärä
	//		$classinfo['rank']			Hevosen sijoitus
	//		$classinfo['difficulty']	Luokan vaikeustaso 0-10
	//		$classinfo['property1']		Kerrytettävä ominaisuus 1
	//		$classinfo['property2']		Kerrytettävä ominaisuus 2
	
	$vh = clearVh( $vh );	
	
	// 1. Etsi hevonen tietokannasta, tarkista että on olemassa
	$getHorse = mysql_query("SELECT * FROM hevosrekisteri_perustiedot WHERE reknro = ".$vh);
	
	if ( mysql_num_rows ( $getHorse ) < 0 ) {
		return FALSE;
	}

	// 2. Laske  annettavat ominaisuuspisteet
	
	if ( $classinfo['difficulty'] >= 0 AND $classinfo['difficulty'] <= 3 ) {
		// Tasot 0-3
		$max = 0.5; // max. 15p
	
	} elseif ( $classinfo['difficulty'] >= 4 AND $classinfo['difficulty'] <= 6 ) {
		// Tasot 4-6
		$max = 1; // max. 20p
	
	} elseif ( $classinfo['difficulty'] >= 7 AND $classinfo['difficulty'] < 9 ) {
		// Tasot 7-9
		$max = 1.5; // max. 25p
	
	} else {
		// Tasot 10-
		$max = 2; // max. 30p
	
	}
	
	$points = 100 / $classinfo['participants'] * ( ($classinfo['participants'] -  $classinfo['rank'] + 0.4 ) / 10);
	$points = ($points * ( 1 + $max / $classinfo['rank'] ) );
	$points =  $points - ( $points / 10 );
	$points =  round($points, 2);
	
	//Taikakerroin on kerroin jolla säädellään porrastettujen pistesaantitasoa kulloisenkin virtuaalimaailmantilanteen mukaan.
	$taikakerroin = 8.7;
							
	$points =  $taikakerroin * $points;
	
	// 3. Jaa ominaisuuspisteet ominaisuuksien kesken
	$percent = rand (15, 75 ); // 34
	$property1 = ($percent/100) * $points; // (0,34) * $points
	$property2 = (1 - $percent/100) * $points; // (1-0,34) * $points
	$properties = round($property1,2).'p. / '.round($property2,2).'p. ';
	
	// 4. Lisää ominaisuuspisteet tietokantaan
	$addPoints = mysql_query("
							INSERT INTO hevosrekisteri_ominaisuudet 
							(reknro, ".$classinfo['property1'].", ".$classinfo['property2'].") VALUES ($vh, '".$property1."', '".$property2."') 
							ON DUPLICATE KEY UPDATE 
								".$classinfo['property1']." = ".$classinfo['property1']."+'".$property1."', 
								".$classinfo['property2']." = ".$classinfo['property2']."+'".$property2."';
							");
							
	// print ' --- '.$property1."+".$property2." = ".$points." <br />";
	
	// 5. Return false/true	
	if ( !$addPoints ) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function checkAllLevels ( $vh ) {

	$sports = array (1,2);
	$levels = array();
	
	$height = checkHeight( $vh );
			
	if ( $height > 0 ) {

		foreach ($sports as &$sportnumber) {
		
			$checklevel =  checkLevel( $vh, $sportnumber );
			
			if( $checklevel['level'] != (-1) ) {
				$levels = 'laji'.$sportnumber.': taso '.$checklevel['level'];
				array_push ($result, $levels);
				
			} else {
			
			}
		}
		
		if( $checklevel['level'] == (-1) ) {
			print '<span style="font-size: 90%; color: ##E8E8E8;">Hevonen on liian nuori kilpailemaan tai siltä puuttuu 3-vuotispäivämäärä profiilistaan.</span>';
		}
		
	} else {
		print '<span style="font-size: 90%; color: ##E8E8E8;">Hevoselta puuttuu säkäkorkeus profiilista eikä se voi kilpailla ennen sen lisäämistä.</span>';
	}
	
	
	return $result;

}
   
}
?>






