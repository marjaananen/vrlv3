<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Jaos_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    function add_jaos(&$msg, $jaos){
            $this->db->insert('vrlv3_kisat_jaokset', $jaos);
            $id = $this->db->insert_id();
            return $id;
        }
    
    function edit_jaos($id, $jaos){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_kisat_jaokset', $jaos);
    }
    function set_jaos_online($id, $online = true){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_kisat_jaokset', array('toiminnassa' => $online));
    }
    
    function add_owner_to_jaos($id, $tunnus)
    {
        $data = array('jid' => $id, 'tunnus' => $tunnus);      
        $this->db->insert('vrlv3_kisat_jaokset_omistajat', $data);
    }

    function delete_jaos($id, &$msg){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id);
        $this->db->delete('vrlv3_kisat_jaokset', $data);
        return true;
    }
    
    //names
    function get_users_jaos($pinnumber)
    {
        $this->db->select('vrlv3_kisat_jaokset.id, nimi, toiminnassa');
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->join('vrlv3_kisat_jaokset_omistajat', 'vrlv3_kisat_jaokset.id = vrlv3_kisat_jaokset_omistajat.jid');
        $this->db->where('vrlv3_kisat_jaokset_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
        //tapahtumat
    function get_event_list($jaos)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_tapahtumat');
        $this->db->where('vrlv3_tapahtumat.jaos_id', $jaos);
        $this->db->where('vrlv3_tapahtumat.tulos', 1);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_event($id, $jaos = null)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_tapahtumat');
        $this->db->where('vrlv3_tapahtumat.id', $id);
        if(isset($jaos)){
            $this->db->where('vrlv3_tapahtumat.jaos_id', $jaos);

        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
    
    function get_event_horses($event){
        $this->db->select('oid, vh, tulos, palkinto, kommentti');
        $this->db->from('vrlv3_tapahtumat_osallistujat');
        $this->db->where('vrlv3_tapahtumat_osallistujat.tapahtuma', $event);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();

    }
    
    function delete_event($id, $jaos_id){
        $data = array('id' => $id, 'jaos_id'=>$jaos_id);
        $this->db->delete('vrlv3_tapahtumat', $data);
        return true;
    }
    
    function delete_event_horse($id, $event_id){
        $data = array('oid' => $id, 'tapahtuma'=>$event_id);
        $this->db->delete('vrlv3_tapahtumat_osallistujat', $data);
        return true;
    }
    
    function add_event($pvm, $title, $user, $jaos, $participant_data = array()){
        $event_data['otsikko'] = $title;
        $event_data['pv'] = $this->CI->vrl_helper->normal_date_to_sql($pvm);
        $event_data['vastuu'] = $user;
        $event_data['jaos_id'] = $jaos;
        
        $jaos_info = $this->get_jaos($jaos);
        
        $event_data['jaos'] = $jaos_info['lyhenne'];


    
     $this->db->insert('vrlv3_tapahtumat', $event_data);
     $id = $this->db->insert_id();
        
     $this->add_event_participants($id, $participant_data);
        
        
        return $id;   
    }
    
    function edit_event($id, $jaos, $title, $date){
        $where = array('id'=> $id, 'jaos_id' => $jaos);
        $this->db->where($where);
        $this->db->update('vrlv3_tapahtumat', array("otsikko"=>$title, "pv" => $this->CI->vrl_helper->normal_date_to_sql($date)));
    
        return true;
        }
    
    
    
    function add_event_participants($id, $participant_data = array()){
        
     foreach($participant_data as $horse){
        $horse['tapahtuma'] = $id;
        $horse['hyv'] = 1;
        $this->db->insert('vrlv3_tapahtumat_osallistujat', $horse);

        
     }
        
        
        return true;
    }
    
    function get_event_horse_prizes($reknro){
        $this->db->select('t.pv, t.otsikko, o.tulos, o.palkinto, o.kommentti, t.jaos');
        $this->db->from('vrlv3_tapahtumat_osallistujat as o');
        $this->db->join('vrlv3_tapahtumat as t', 't.id = o.tapahtuma');
        $this->db->where('o.vh', $reknro);
        $this->db->where("o.hyv", 1);
        $this->db->where("t.tulos", 1);
        $this->db->order_by("t.pv", 'desc');
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    
    function get_jaos_list()
    {
        $this->db->select("id, nimi, lyhenne, kuvaus, url, IF(toiminnassa='1', 'toiminnnassa', 'ei toiminnassa') as toiminnassa");
        $this->db->from('vrlv3_kisat_jaokset');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaos_option_list(){
        $options = array();
        $options[0] = "";
        
        $jaos_list = $this->get_jaos_list();
        
        foreach ($jaos_list as $jaos){
            $options[$jaos['id']] = $jaos['lyhenne'];
        }
        
        return $options;
    }
    function get_jaos_porr_list()
    {
        $this->db->select("id, nimi, lyhenne, laji, kuvaus, url, IF(toiminnassa='1', 'toiminnnassa', 'ei toiminnassa') as toiminnassa");
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->where("s_salli_porrastetut", 1);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaokset_all()
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_jaokset');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaokset_full()
    {
        $jaokset = $this->get_jaokset_all();
        foreach ($jaokset as &$jaos){
            $jaos['yllapito'] = $this->get_jaos_handlers($jaos['id']);
            $jaos['luokat_porr'] = $this->get_class_list($jaos['id'], true, true);
        }
        
        return $jaokset;
    }
    
    function get_jaos_handlers($id){
        return $this->get_jaos_owners($id, true);
        
    }
    
    function get_jaos($id)
    {
        $jaos = array();
        $this->db->select('*');
        $this->db->from('vrlv3_kisat_jaokset as j');
        //$this->db->join('vrlv3_kisat_jaokset_omistajat', 'vrlv3_kisat_jaokset.id = vrlv3_kisat_jaokset_omistajat.jid');
        //$this->db->join('vrlv3_lista_painotus as l', 'j.laji = l.pid', 'left');
        $this->db->where('j.id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $result = $query->result_array();
            $jaos = $result[0];
            
            
            return $jaos;
        }
        
        return array();
    }
    
    
    function get_jaos_owners($id, $only_yp = false)
    {
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, nimimerkki, taso, CONCAT(vrlv3_kisat_jaokset_omistajat.tunnus, "/", vrlv3_kisat_jaokset_omistajat.jid) as id');
        $this->db->from('vrlv3_kisat_jaokset_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_kisat_jaokset_omistajat.tunnus');
        if($only_yp){
          $this->db->where('vrlv3_kisat_jaokset_omistajat.taso', 1);

        }
        $this->db->where('jid', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }

    function is_name_in_use($name, $id = null)
    {
        $this->db->select('id');
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->where('nimi', $name);
        if (isset($id)){
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function is_lyhenne_in_use($name, $id = null)
    {
        
        $this->db->select('id');
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->where('lyhenne', $name);
        if (isset($id)){
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function is_jaos_owner($pinnumber, $id)
    {
        $this->db->select('omistaja');
        $this->db->from('vrlv3_kisat_jaokset_omistajat');
        $this->db->where('id', $id);
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function get_class_list($jaos, $only_usable = true, $only_porrastetut = false)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_luokat');
        $this->db->where("jaos", $jaos);
        $this->db->order_by("jarjnro");
        if($only_porrastetut){
            $this->db->where("porrastettu", 1);
        }
        if($only_usable){
            $this->db->where("kaytossa", 1);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_class($id, $jaos_id)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_luokat');
        $this->db->where("id", $id);
        $this->db->where("jaos", $jaos_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            
            $array = $query->result_array();
            return $array[0];
        }
        
        return array();
    }
    
    
    function add_class($jaos_id, $laji_id, $class){
        $class['jaos'] = $jaos_id;
        $class['laji'] = $laji_id;
        $this->db->insert('vrlv3_kisat_luokat', $class);
        $id = $this->db->insert_id();
        return $id;
    }
    
     function edit_class($class_id, $class){        
        unset($class['jaos']);
        unset($class['laji']);
        $this->db->where('id', $class_id);
        $this->db->update('vrlv3_kisat_luokat', $class);
    }
    
    function delete_class($id, $jaos_id){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id, 'jaos'=>$jaos_id);
        $this->db->delete('vrlv3_kisat_luokat', $data);
        return true;
    }
    
    
    
    
    

 
}

