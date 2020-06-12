<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Oikeudet_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
     function does_user_group_exist_by_name($name){
        $this->db->where('name', $name);
        $this->db->from('vrlv3_groups');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;      
    }
    
    
    
    function users_in_group_id($group_id){
        $this->db->select('tunnus, nimimerkki');
        $this->db->from('vrlv3_tunnukset');
        $this->db->join('vrlv3_users_groups', 'vrlv3_users_groups.user_id = vrlv3_tunnukset.id');
        $this->db->where('vrlv3_users_groups.group_id', $group_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function users_in_group_name($group_name){
        $this->db->select('vrlv3_tunnukset.tunnus, vrlv3_tunnukset.nimimerkki');
        $this->db->from('vrlv3_users_groups');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_users_groups.user_id = vrlv3_tunnukset.id');
        $this->db->join('vrlv3_groups', 'vrlv3_groups.id = vrlv3_users_groups.group_id');

        $this->db->where('vrlv3_groups.name', $group_name);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    
    function get_groups(){
        $this->db->select('vrlv3_groups.id, name, description, count(user_id) as kpl');
        $this->db->from('vrlv3_groups');
        $this->db->join('vrlv3_users_groups', 'vrlv3_users_groups.group_id = vrlv3_groups.id');
        $this->db->group_by('vrlv3_groups.id');
        $this->db->order_by('vrlv3_groups.id', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();     
    }
        
        
    
    
    
}

