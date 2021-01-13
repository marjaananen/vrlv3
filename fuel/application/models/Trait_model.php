<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Trait_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_trait_list($jaos = null){
        
        $this->db->select('id, vrlv3_lista_ominaisuudet.ominaisuus');
        $this->db->from('vrlv3_lista_ominaisuudet');
        if(isset($jaos)){
            $this->db->join('vrlv3_kisat_jaokset_ominaisuudet', 'vrlv3_lista_ominaisuudet.id =vrlv3_kisat_jaokset_ominaisuudet.ominaisuus' );
            $this->db->where('vrlv3_kisat_jaokset_ominaisuudet.jaos', $jaos);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {

            return $query->result_array();
        }
        
        return array();
    

                
    }
    
    
    public function get_trait_info($trait){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_ominaisuudet');
        $this->db->where('id', $trait);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        
        return array();
                
    }
    
    
    ////functions for form option lists
    
    
    public function get_trait_option_list(){
        $data = array();
        
        $this->db->select('id, ominaisuus');
        $this->db->from('vrlv3_lista_ominaisuudet');
        $this->db->order_by("ominaisuus", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['id']] = $row['ominaisuus'];
            }
        }
        
        return $data;
    }
    
    
    public function get_trait_array_by_jaos($id){
        $data = array();
        
        $this->db->select('ominaisuus');
        $this->db->from('vrlv3_kisat_jaokset_ominaisuudet');
        $this->db->where('jaos', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[] = $row['ominaisuus'];
            }
        }
        
        return $data;
    }
    
    
    function update_jaos_traits($id, $traits, &$msg){
        
        $insert_batch_batch = array();
        foreach ($traits as $trait){
            $insert_batch = array();
            $insert_batch['jaos'] = $id;
            $insert_batch['ominaisuus'] = $trait;
            
            $insert_batch_batch[] = $insert_batch;
        }
        
        $this->db->trans_start();

        $data = array('jaos' => $id);
        $this->db->delete('vrlv3_kisat_jaokset_ominaisuudet', $data);
        $this->db->insert_batch('vrlv3_kisat_jaokset_ominaisuudet', $insert_batch_batch);

        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE)
        {
            $msg = "Jokin meni vikaan. ";
            return false;
        }
        else return true;
        
    }
    
    

    
    
    public function lisaa_trait(&$msg, $ominaisuus){
        $this->db->select('id, ominaisuus');
        $this->db->where('ominaisuus', $ominaisuus);
        $this->db->from('vrlv3_lista_ominaisuudet');

        
        $query = $this->db->get();
        //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $msg = "Ominaisuusa ei voitu lisätä: ";
            foreach ($query->result_array() as $roturivi){
                if ($roturivi['ominaisuus'] == $ominaisuus){
                    $msg .= "annettu ominaisuusnimi on jo käytössä. ";
                }
            }
            return false;
        }
        

        $insert_data = array();
        $insert_data['ominaisuus'] = $ominaisuus;
      
        $this->db->insert('vrlv3_lista_ominaisuudet', $insert_data);
        $id = $this->db->insert_id();
        
        return $id;
        
        
    }
    
    
    public function muokkaa_trait(&$msg, $id, $ominaisuus){
        $this->db->select('id, ominaisuus');
        $this->db->where('ominaisuus', $ominaisuus);
        $this->db->from('vrlv3_lista_ominaisuudet');
        
        $query = $this->db->get();
        
         //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $tulos = $query->result_array();
             if (!(sizeof($tulos) == 1 && $tulos[0]['id'] == $id)) {

                $msg = "Ominaisuutta ei voitu lisätä: ";
                foreach ($tulos as $roturivi){
                if ($roturivi['ominaisuus'] == $ominaisuus){
                    $msg .= "annettu ominaisuusnimi on jo käytössä. ";
                }

                }
                return false;
             }
        }
        


        $insert_data = array();
        $insert_data['ominaisuus'] = $ominaisuus;
        
            $this->db->where('id', $id);
            $this->db->update('vrlv3_lista_ominaisuudet', $insert_data);
        
        return $id;
        
        
    }
    
    
    public function delete_trait($id){
        $this->db->select('*');
        $this->db->where('ominaisuus', $id);
        $this->db->from('vrlv3_kisat_jaokset_ominaisuudet');
    
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            //ominaisuus käytössä jollain jaoksella
            return false;
        
        } else {
            $data = array('id' => $id);
            $this->db->delete('vrlv3_lista_ominaisuudet', $data);
        }
        
    }

      public function trait_exists($trait){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_ominaisuudet');
        $this->db->where('id', $trait);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
                
    }  

    
    
}

