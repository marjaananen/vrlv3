<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Jaos_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_sport_option_list(){
        $data = array();
        
        $this->db->select('pid, painotus');
        $this->db->from('vrlv3_lista_painotus');
        $this->db->order_by("painotus", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['pid']] = $row['painotus'];
            }
        }
        
        return $data;
        
    }
    
    
    function add_jaos(&$msg, $jaos){
            $this->db->insert('vrlv3_kisat_jaokset', $jaos);
            $id = $this->db->insert_id();
            return $id;
        }
    
    function edit_jaos($id, $jaos){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_jaokset', $rules);
    }
    function set_jaos_online($id, $online = true){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_jaokset', array('toiminnassa' => $online));
    }
    
    function add_owner_to_jaos($id, $tunnus)
    {
        $data = array('jid' => $id, 'tunnus' => $tunnus);      
        $this->db->insert('vrlv3_jaokset_omistajat', $data);
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
        $this->db->select('vrlv3_jaokset.id, nimi, toiminnassa');
        $this->db->from('vrlv3_jaokset');
        $this->db->join('vrlv3_jaokset_omistajat', 'vrlv3_jaokset.id = vrlv3_jaokset_omistajat.jid');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_jaokset_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    function get_jaos_list()
    {
        $this->db->select("id, nimi, lyhenne,  IF(toiminnassa='1', 'toiminnnassa', 'ei toiminnassa') as toiminnassa");
        $this->db->from('vrlv3_kisat_jaokset');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaokset_search($all = true, $online = null)
    {
        $this->db->select('j.id, j.nimi, j.toiminnassa, j.lyhenne, j.url, j.kuvaus, ');
        $this->db->from('vrlv3_jaokset as j');
        $this->db->join('vrlv3_jaokset_omistajat', 'vrlv3_jaokset.id = vrlv3_jaokset_omistajat.jid');
        $this->db->join('vrlv3_lista_painotus as l', 'vrlv3_jaokset.laji = vrlv3_lista_painotus', 'left');
        $this->db->join('vrlv3_lista_painotus', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $pinnumber);
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
    
    

 
}

