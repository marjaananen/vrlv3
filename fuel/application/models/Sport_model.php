<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Sport_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_sport_list(){
        
        $this->db->select('pid, painotus, lyhenne');
        $this->db->from('vrlv3_lista_painotus');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    
    public function get_sport_info($sport){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_painotus');
        $this->db->where('pid', $sport);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        
        return array();
                
    }
    
    
    ////functions for form option lists
    
    
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
    
    

    
    
    public function lisaa_sport(&$msg, $painotus, $lyhenne){
        $this->db->select('pid, painotus, lyhenne');
        $this->db->where('painotus', $painotus);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_painotus');

        
        $query = $this->db->get();
        //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $msg = "Lajia ei voitu lisätä: ";
            foreach ($query->result_array() as $roturivi){
                if ($roturivi['painotus'] == $painotus){
                    $msg .= "annettu lajinimi on jo käytössä. ";
                }
                if ($roturivi['lyhenne'] == $lyhenne){
                    $msg .= "annettu lajilyhenne on jo käytössä. ";
                }
            }
            return false;
        }
        

        $insert_data = array();
        $insert_data['painotus'] = $painotus;
        $insert_data['lyhenne'] = $lyhenne;
      
        $this->db->insert('vrlv3_lista_painotus', $insert_data);
        $id = $this->db->insert_id();
        
        return $id;
        
        
    }
    
    
    public function muokkaa_sport(&$msg, $id, $painotus, $lyhenne){
        $this->db->select('pid, painotus, lyhenne');
        $this->db->where('painotus', $painotus);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_painotus');
        
        $query = $this->db->get();
        
         //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $tulos = $query->result_array();
             if (!(sizeof($tulos) == 1 && $tulos[0]['pid'] == $id)) {

                $msg = "Lajia ei voitu lisätä: ";
                foreach ($tulos as $roturivi){
                if ($roturivi['painotus'] == $painotus){
                    $msg .= "annettu lajinimi on jo käytössä. ";
                }
                if ($roturivi['lyhenne'] == $lyhenne){
                    $msg .= "annettu lajilyhenne on jo käytössä. ";
                }
                }
                return false;
             }
        }
        


        $insert_data = array();
        $insert_data['painotus'] = $painotus;
        $insert_data['lyhenne'] = $lyhenne;
        
            $this->db->where('pid', $id);
            $this->db->update('vrlv3_lista_painotus', $insert_data);
        
        return $id;
        
        
    }
    
    
    public function delete_sport($id){
        
        $this->db->select('reknro');
        $this->db->where('painotus', $id);
        $this->db->from('vrlv3_hevosrekisteri');
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return false;
        
        } else {
        
            $this->db->select('*');
            $this->db->where('pid', $id);
            $this->db->from('vrlv3_lista_painotus');
        
            $query = $this->db->get();
            if ($query->num_rows() == 0)
            {
                return false;
            
            } else {
                $data = array('pid' => $id);
                $this->db->delete('vrlv3_lista_painotus', $data);
            }
        }
    }

      public function sport_exists($sport){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_painotus');
        $this->db->where('pid', $sport);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
                
    }  

    
    
}

