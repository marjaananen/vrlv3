<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Ownership
{

    
public function __construct()
	{
        $this->CI =& get_instance();
	}
    
    public $taulut = array("hev"=>array("t"=>"vrlv3_hevosrekisteri", "id"=>"reknro", "ot"=>array("om"=>"omistaja", "id"=> "reknro", "t"=>"taso")),
                           "tal"=>array("t"=>"vrlv3_tallirekisteri", "id"=>"tnro","ot"=>array("om"=>"omistaja", "id"=> "tnro", "t"=>"taso")),
                           "kasv"=>array("t"=>"vrlv3_kasvattajanimet", "id"=>"id","ot"=>array("om"=>"tunnus", "id"=> "kid", "t"=>"taso"))
                          );
                           
                                       
    
    private $CI = NULL;
    
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

private function _handle_owners($taulu, $mode, $tapa, $adder, $owner, $item, &$fields){
    $msg ="";
    $type="";
    
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
                $ok = $this->_add($taulu, $this->CI->vrl_helper->vrl_to_number($tunnus), $item, $taso);
                if ($ok){ $type = "success"; $msg = "Omistajan lisäys onnistui.";}
                else {$type = "warning"; $msg = "Omistajan lisäys epäonnistui. Tarkasta, että tunnus on kirjoitettu oikein, ja ettei käyttäjä ole jo omistaja!";}
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
            if ($ok){ $type = "success"; $msg = "Omistajan tason muutos onnistui.";}
            else {$type = "danger"; $msg = "Omistajan tason muutos epäonnistui. Ainutta pääomistajaa ei saa muuttaa haltijaksi.";}

            
        }
        
        $fields['info'] = $this->CI->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);

    }
    
    else if ($tapa == "poista"){
        if (!($mode == 'admin' || $this->_editor_permission($taulu, $adder, $item))){
            $msg = "Sinulla ei ole oikeuksia muuttaa omistussuhteita.";
            $ok = false;
        }else {
            $this->CI->load->library("vrl_helper");
            $this->CI->vrl_helper->check_vrl_syntax($owner);
            $ok = $this->_del($taulu, $this->CI->vrl_helper->vrl_to_number($owner), $item);
            if ($ok){ $type = "success"; $msg = "Omistajan poisto onnistui!";}
            else {$type = "danger"; $msg = "Omistajan poisto epäonnistui. Ainutta pääomistajaa ei saa poistaa.";}

            
        }
         $fields['info'] = $this->CI->load->view('misc/naytaviesti', array('msg_type' => $type, 'msg' => $msg), true);

    
    }
    
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
    
    
    public function get_owner_adding_form($url){

        $this->CI->load->library('form_builder', array('submit_value' => 'Lisää omistaja'));
		
		$fields['tunnus'] = array('type' => 'text', 'class'=>'form-control');
		$fields['taso'] = array('type' => 'select', 'options' => array(1=>"Omistaja", 0=>"Haltija"), 'value' => '1', 'class'=>'form-control');
	
		$this->CI->form_builder->form_attrs = array('method' => 'post', 'action' => site_url("".$url."omistajat/lisaa"));
		return $this->CI->form_builder->render_template('_layouts/basic_form_template', $fields);
    }


}