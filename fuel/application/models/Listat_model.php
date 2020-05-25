<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Listat_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    public $listat = array("rodut"=>array("taulu"=> "vrlv3_lista_rodut", "id"=>"rotunro"),
                           "varit"=>array("taulu"=> "vrlv3_lista_varit", "id"=>"vid"),
                           "painotus"=>array("taulu"=> "vrlv3_lista_painotus", "id"=>"pid"),
                           "maat"=>array("taulu"=> "vrlv3_lista_maat", "id"=>"id"));
    
    
    private function _exists_in_list($id, $lista){
        $this->db->select($lista['id']);
        $this->db->where($lista['id'], $id);
        $this->db->from($lista['taulu']);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;      
    }
    
    public function colour_exists($id){
        return $this->_exists_in_list($id, $this->listat["varit"]);
    }
    
    public function breed_exists($id){
        return $this->_exists_in_list($id, $this->listat["rodut"]);
    }
    
    public function skill_exists($id){
        return $this->_exists_in_list($id, $this->listat["painotus"]);
    }
    public function country_exists($id){
        return $this->_exists_in_list($id, $this->listat["maat"]);
    }
        
    
    
    
}

