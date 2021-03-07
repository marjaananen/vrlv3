<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Ownership
{

    
public function __construct()
	{
        $this->CI =& get_instance();
	}
    
    public $taulut = array("hev"=>array("t"=>"vrlv3_hevosrekisteri", "id"=>"reknro",
										"ot"=>array("om"=>"omistaja", "id"=> "reknro", "t"=>"taso"), "tm"=>true),
                           "tal"=>array("t"=>"vrlv3_tallirekisteri", "id"=>"tnro",
										"ot"=>array("om"=>"omistaja", "id"=> "tnro", "t"=>"taso"),"tm"=>false),
                           "kasv"=>array("t"=>"vrlv3_kasvattajanimet", "id"=>"id",
										 "ot"=>array("om"=>"tunnus", "id"=> "kid", "t"=>"taso"),"tm"=>false),
						    "jaos"=>array("t"=>"vrlv3_kisat_jaokset", "id"=>"id",
										  "ot"=>array("om"=>"tunnus", "id"=> "jid", "t"=>"taso"),"tm"=>false),
							"pulju"=>array("t"=>"vrlv3_puljut", "id"=>"id",
										  "ot"=>array("om"=>"tunnus", "id"=> "jid", "t"=>"taso"),"tm"=>false)

                          );
                           
                                       
    
    private $CI = NULL;
	
	private function _owner_names($taulu, $level){
			$om = "pääomistaja";
			$om2 = "haltija";
		
		if($taulu['t'] == 'vrlv3_kisat_jaokset'){
			$om = "ylläpitäjä";
			$om2 = "kalenterityöntekijä";
		} else if ($taulu['t'] == 'vrlv3_puljut'){
			$om = "ylläpitäjä";
			$om2 = "työntekijä";
		}
		
		if($level == 1){
			return $om;
		}else {
			return $om2;
		}
	}
    
//handling the owners

    public function handle_horse_ownerships($mode, $tapa, $adder, $owner, $item, &$fields){
        return $this->_handle_owners ($this->taulut['hev'], $mode, $tapa, $adder, $owner, $item, $fields);
    }
    
    public function handle_stable_ownerships($mode, $tapa, $adder, $owner, $item, &$fields){
        return $this->_handle_owners  ($this->taulut['tal'],$mode, $tapa, $adder, $owner, $item, $fields);
    }
    
    public function handle_name_ownerships($mode, $tapa, $adder, $owner, $item, &$fields){
        return $this->_handle_owners  ($this->taulut['kasv'], $mode, $tapa, $adder, $owner, $item, $fields);
    }
	
	public function handle_jaos_ownerships($mode, $tapa, $adder, $owner, $item, &$fields){
        $ok = $this->_handle_owners  ($this->taulut['jaos'], $mode, $tapa, $adder, $owner, $item, $fields);
		$this->CI->load->model("Jaos_model");
		$this->CI->Jaos_model->edit_jaos_user_rights($owner);
		return $ok;
	
    }
	
	public function handle_pulju_ownerships($mode, $tapa, $adder, $owner, $item, &$fields){
        $ok = $this->_handle_owners  ($this->taulut['pulju'], $mode, $tapa, $adder, $owner, $item, $fields);
		$this->CI->load->model("Jaos_model");
		$this->CI->Jaos_model->edit_pulju_user_rights($owner);
		return $ok;
	
    }

private function _handle_owners($taulu, $mode, $tapa, $adder, &$owner, $item, &$fields){
    $msg ="";
    $type="";
	
	$om = $this->_owner_names($taulu, 1);
	$om2 = $this->_owner_names($taulu, 2);
	
    
    if($this->CI->input->server('REQUEST_METHOD') == 'POST'){

        if(strlen($this->CI->input->post("tunnus")) > 0){
                        
            $this->CI->load->library('vrl_helper');
            $tunnus = $this->CI->input->post("tunnus");
            $taso = $this->CI->input->post("taso");
            
            if (!($mode == 'admin' || $this->_editor_permission($taulu, $adder, $item))){
                $msg = "Sinulla ei ole oikeuksia muuttaa omistussuhteita.";
                $ok = false;
            }
            else if($this->CI->vrl_helper->check_vrl_syntax($tunnus) && ($taso == 1 || $taso == 0)){
				$owner = $this->CI->vrl_helper->vrl_to_number($tunnus);
                $ok = $this->_add($taulu, $owner, $item, $taso);
                if ($ok){ $type = "success"; $msg = "Lisäys onnistui.";}
                else {$type = "warning"; $msg = "Lisäys epäonnistui. Tarkasta, että tunnus on kirjoitettu oikein, ja ettei käyttäjä ole jo listalla!";}
            }
            else {
                $type = "warning"; $msg = "Virheellinen syöte!";
            }
        }
        else {
            $type = "warning"; $msg = "Virheellinen syöte!";
        }
            
            $fields['info'] = $this->CI->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);

    }
    

    else if ($tapa == "muokkaa"){
        if (!($mode == 'admin' || $this->_editor_permission($taulu, $adder, $item))){
            $msg = "Sinulla ei ole oikeuksia muuttaa omistussuhteita.";
            $ok = false;
        }else {
            $this->CI->load->library("vrl_helper");
            $this->CI->vrl_helper->check_vrl_syntax($owner);
            $ok = $this->_edit($taulu, $this->CI->vrl_helper->vrl_to_number($owner), $item);
            if ($ok){ $type = "success"; $msg = "Tason muutos onnistui.";}
            else {$type = "danger"; $msg = "Tason muutos epäonnistui. Ainutta ".$om."a ei saa muuttaa ".$om2."ksi.";}

            
        }
        
        $fields['info'] = $this->CI->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);

    }
    
    else if ($tapa == "poista"){
		$this->CI->load->library("vrl_helper");


		if($this->CI->vrl_helper->vrl_to_number($owner) == $this->CI->ion_auth->user()->row()->tunnus){
			$ok = $this->remove_me_from_horse($item, $msg);
			if ($ok){ $type = "success"; $msg = "Poisto onnistui!";}
            else {$type = "danger"; $msg = "Poisto epäonnistui. Ainutta ".$om."a ei saa poistaa.";}
		}
        else if (!($mode == 'admin' || $this->_editor_permission($taulu, $adder, $item))){
            $msg = "Sinulla ei ole oikeuksia muuttaa omistussuhteita.";
            $ok = false;
        }else {
            $this->CI->load->library("vrl_helper");
            $this->CI->vrl_helper->check_vrl_syntax($owner);
            $ok = $this->_del($taulu, $this->CI->vrl_helper->vrl_to_number($owner), $item);
            if ($ok){ $type = "success"; $msg = "Poisto onnistui!";}
            else {$type = "danger"; $msg = "Poisto epäonnistui. Ainutta ".$om."a ei saa poistaa.";}

            
        }
         $fields['info'] = $this->CI->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);

    
    }
    
}

	public function remove_me_from_horse($item, &$fields){
        return $this->_remove_me ($item, $this->taulut['hev'], $fields);
    }
	
	public function remove_me_from_stable($item, &$fields){
        return $this->_remove_me ($item, $this->taulut['tal'], $fields);
    }
	
	public function remove_me_from_name($item, &$fields){
        return $this->_remove_me ($item, $this->taulut['kasv'], $fields);
    }
	
	public function remove_me_from_jaos($item, &$fields){
        $ok = $this->_remove_me ($item, $this->taulut['jaos'], $fields);	
		$this->CI->load->model("Jaos_model");
		$this->CI->Jaos_model->edit_jaos_user_rights($this->CI->ion_auth->user()->row()->tunnus);
		return $ok;
    }
	
	public function remove_me_from_pulju($item, &$fields){
        $ok = $this->_remove_me ($item, $this->taulut['pulju'], $fields);	
		$this->CI->load->model("Jaos_model");
		$this->CI->Jaos_model->edit_pulju_user_rights($this->CI->ion_auth->user()->row()->tunnus);
		return $ok;
    }
    
private function _remove_me($item, $taulu, &$msg){
	
	$om = $this->_owner_names($taulu, 1);
	$om2 = $this->_owner_names($taulu, 2);
	
	$owner = $this->CI->ion_auth->user()->row()->tunnus;
	$this->CI->load->library("vrl_helper");
    if(!$this->_del($taulu, $this->CI->vrl_helper->vrl_to_number($owner), $item)){
		 $msg = "Poisto epäonnistui. Ainutta ".$om."a ei saa poistaa.";
		return false;
	}
	
	return true;
}
 
//Owners listing    
    
    public function horse_ownerships($item, $edit, $url){
        return $this->_listing ('horse', $item, $edit, $url);
    }
    
    public function stable_ownerships($item, $edit, $url){
        return $this->_listing ('stable', $item, $edit, $url);
    }
    
    public function name_ownerships($item, $edit, $url){
        return $this->_listing ('name', $item, $edit, $url);
    }
	
	public function jaos_ownerships($item, $edit, $url){
        return $this->_listing ('jaos', $item, $edit, $url);
    }
	
	public function pulju_ownerships($item, $edit, $url){
        return $this->_listing ('pulju', $item, $edit, $url);
    }
    
             
    private function _listing ($type, $item, $edit, $url){
        
                                                      
		$vars['title'] = "";	
		$vars['msg'] = '';
		$vars['text_view'] = "";
	
		
		$vars['headers'][1] = array('title' => 'Tunnus', 'key' => 'omistaja', 'key_link' => site_url('tunnus/'), 'type'=>'VRL');
		$vars['headers'][2] = array('title' => 'Nimimerkki', 'key' => 'nimimerkki');
		$vars['headers'][3] = array('title' => 'Taso', 'key' => 'taso');
    
    	if($edit){
			$vars['headers'][4] = array('title' => 'Editoi', 'key' => 'omistaja', 'key_link' => site_url($url . 'omistajat/muokkaa/'), 'image' => site_url('assets/images/icons/update.png'));
			$vars['headers'][5] = array('title' => 'Poista', 'key' => 'omistaja', 'key_link' => site_url($url . 'omistajat/poista/'), 'image' => site_url('assets/images/icons/delete.png'));
				
		}
            
        $vars['data'] = array();
            
        if ($type == 'horse') {
             $this->CI->load->model('hevonen_model');
			$vars['data'] = json_encode($this->CI->hevonen_model->get_horse_owners($item));
        }
        else if ($type == 'stable') {
            $this->CI->load->model('tallit_model');
			$vars['data'] = json_encode($this->CI->tallit_model->get_stables_owners($item));
        }
        else if ($type == 'name') {
            $this->CI->load->model('kasvattajanimi_model');
			$vars['data'] = json_encode($this->CI->kasvattajanimi_model->get_names_owners($item));
        }
		 else if ($type == 'jaos') {
            $this->CI->load->model('jaos_model');
			$vars['data'] = json_encode($this->CI->jaos_model->get_jaos_owners($item));
        }
		else if ($type == 'pulju') {
            $this->CI->load->model('jaos_model');
			$vars['data'] = json_encode($this->CI->jaos_model->get_pulju_owners($item));
        }
		$vars['headers'] = json_encode($vars['headers']);
		
		return $this->CI->load->view('misc/taulukko', $vars, TRUE);
    }
    
//check owner status
    public function is_horses_main_owner($adder, $item){
        return $this->_editor_permission($this->taulut['hev'],$adder, $item);
    }
    
    public function is_stables_main_owner($adder, $item){
        return $this->_editor_permission($this->taulut['tal'],$adder, $item);
    }
    
    public function is_names_main_owner($adder, $item){
        return $this->_editor_permission($this->taulut['kasv'],$adder, $item);
    }
    public function is_jaos_main_owner($adder, $item){
        return $this->_editor_permission($this->taulut['jaos'],$adder, $item);
    }
	public function is_pulju_main_owner($adder, $item){
        return $this->_editor_permission($this->taulut['pulju'],$adder, $item);
    }


// Add Ownership    
    
    public function add_horse_ownerships($owner, $item, $level=1){
        return $this->_add($this->taulut['hev'], $owner, $item, $level);
    }
    
    public function add_stable_ownerships($owner, $item, $level=1){
        return $this->_add($this->taulut['tal'],  $owner, $item, $level); 
    }
    
    public function add_name_ownerships( $owner, $item, $level=1){
        return $this->_add($this->taulut['kasv'], $owner, $item, $level);             

    }
	
	public function add_jaos_ownerships( $owner, $item, $level=1){
        return $this->_add($this->taulut['jaos'], $owner, $item, $level);             

    }
	public function add_pulju_ownerships( $owner, $item, $level=1){
        return $this->_add($this->taulut['pulju'], $owner, $item, $level);             

    }
    
    
    private function _add($taulu, $owner, $item, $taso = 1){
        $taulunimi = $taulu['t'] . "_omistajat";
        $id = $taulu['ot']['id'];
        $om = $taulu['ot']['om'];
        $t = $taulu['ot']['t'];
        $this->CI->load->model('tunnukset_model');
        
        //Onko item ja tunnukset olemassa ja jos on, onko omistussuhde olemassa?
        if ($this->CI->tunnukset_model->onko_tunnus($owner)
            && $this->it_is_there_already($taulu['t'], array($taulu['id']=>$item))
            && !$this->it_is_there_already ($taulunimi, array($id => $item, $om=> $owner))){
            
            $data = array($id => $item, $om => $owner, $t => $taso);
            $this->CI->db->insert($taulunimi, $data);
            return true;
        }
        
        else {
            return false;
        }
          
    }
    
    
    // Delete Ownership    
    
    public function del_horse_ownerships($owner, $item){
        return $this->_del($this->taulut['hev'], $owner, $item);
    }
    
    public function del_stable_ownerships( $owner, $item){
        return $this->_del($this->taulut['tal'], $owner, $item); 
    }
    
    public function del_name_ownerships( $owner, $item){
        return $this->_del($this->taulut['kasv'],  $owner, $item);             

    }
	
	public function del_jaos_ownerships( $owner, $item){
        return $this->_del($this->taulut['jaos'],  $owner, $item);             

    }
	
	public function del_pulju_ownerships( $owner, $item){
        return $this->_del($this->taulut['pulju'],  $owner, $item);             

    }
    
    
    private function _del($taulu, $owner, $item){
        $taulunimi = $taulu['t'] . "_omistajat";
        $id = $taulu['ot']['id'];
        $om = $taulu['ot']['om'];
        $t = $taulu['ot']['t'];
        
        $this->CI->load->model('tunnukset_model');
        
        //Onko item ja tunnukset olemassa ja jos on, onko omistussuhde olemassa?
        if ($this->CI->tunnukset_model->onko_tunnus($owner)
            && $this->it_is_there_already($taulu['t'], array($taulu['id']=>$item))
            && $this->it_is_there_already ($taulunimi, array($id => $item, $om=> $owner))){
            
            //onko poistettava pääomistaja ja ainoa sellainen
            if ($this->_get_level($taulu, $owner, $item) === 1 && $this->how_many_owners($taulu, $item, 1) === 1){
                return false;
            }else{
                $data = array($id => $item, $om => $owner);
                $this->CI->db->where($data);
                $this->CI->db->delete($taulunimi);
				
				if($taulu['tm']){

					$data['muokkasi'] = $this->CI->ion_auth->user()->row()->tunnus;
					$data['aika'] = date('Y-m-d H.i.s');
					$taulunimi = $taulu['t'] . "_omistajamuutokset";
					
					$this->CI->db->insert($taulunimi, $data);

				}
                
            }
            return true;

        }
        
        else {
            return false;
        }
          
    }
    
        // Switch Ownership    
    
    public function edit_horse_ownerships($owner, $item){
        return $this->_edit($this->taulut['hev'], $owner, $item);
    }
    
    public function edit_stable_ownerships($owner, $item){
        return $this->_edit($this->taulut['tal'], $owner, $item); 
    }
    
    public function edit_name_ownerships($owner, $item){
        return $this->_edit($this->taulut['kasv'], $owner, $item);             

    }
	
	public function edit_jaos_ownerships($owner, $item){
        return $this->_edit($this->taulut['jaos'], $owner, $item);             

    }
    
	public function edit_pulju_ownerships($owner, $item){
        return $this->_edit($this->taulut['pulju'], $owner, $item);             

    }
    
    
    private function _edit($taulu, $owner, $item){
        $taulunimi = $taulu['t'] . "_omistajat";
        $id = $taulu['ot']['id'];
        $om = $taulu['ot']['om'];
        $t = $taulu['ot']['t'];
        
        $this->CI->load->model('tunnukset_model');
        
        //Onko item ja tunnukset olemassa ja jos on, onko omistussuhde olemassa?
        if ($this->CI->tunnukset_model->onko_tunnus($owner)
            && $this->it_is_there_already($taulu['t'], array($taulu['id']=>$item))
            && $this->it_is_there_already ($taulunimi, array($id => $item, $om=> $owner))){
            
            //onko poistettava pääomistaja ja ainoa sellainen
            $level = $this->_get_level($taulu, $owner, $item);
            if ($level === 1 && $this->how_many_owners($taulu, $item, 1) === 1){
                return false;
            }else{
                $newlevel = 0;
                if ($level === 1) { $newlevel = 0;}
                else { $newlevel = 1;}
                
                $data_where = array($id => $item, $om => $owner);
                $data = array($t => $newlevel);
                
                $this->CI->db->where($data_where);
                $this->CI->db->update($taulunimi, $data);                
            }
            return true;

        }
        
        else {
            return false;
        }
          
    }
    
    private function _get_level($taulu, $owner, $item){
        $taulunimi = $taulu['t'] . "_omistajat";
        $id = $taulu['ot']['id'];
        $om = $taulu['ot']['om'];
        $t = $taulu['ot']['t'];
        
        if($this->it_is_there_already ($taulunimi, array($id => $item, $om=> $owner, $t => 1))){
        
           return 1;
        }
        
        else{
            return 0;
        }
        
    }
    
    private function _editor_permission($taulu, $adder, $item){
        
        //onko lisääjä ok
        if($this->_get_level($taulu, $adder, $item) === 1){
           return true;
        }
        
        else {
            return false;
        }
            
    
    }

    
    
    
     public function it_is_there_already ($taulu, $array){
        $this->CI->db->where($array);
        $this->CI->db->from($taulu);
        $amount = $this->CI->db->count_all_results();
            
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
        
    }
    
    public function how_many_owners($taulu, $item, $level=1){
        
        $taulunimi = $taulu['t'] . "_omistajat";
        $id = $taulu['ot']['id'];
        $om = $taulu['ot']['om'];
        $t = $taulu['ot']['t'];
    
        $this->CI->db->where(array($id => $item, $t => $level));
        $this->CI->db->from($taulunimi);
        return $this->CI->db->count_all_results();
    
    }
    
    
    public function get_owner_adding_form($url, $om = "Omistaja", $om2 = "Haltija"){

        $this->CI->load->library('form_builder', array('submit_value' => 'Lisää omistaja'));
		
		$fields['tunnus'] = array('type' => 'text', 'class'=>'form-control');
		$fields['taso'] = array('type' => 'select', 'options' => array(1=>$om, 0=>$om2), 'value' => '1', 'class'=>'form-control');
	
		$this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url("".$url."omistajat/lisaa"));
		return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }


}