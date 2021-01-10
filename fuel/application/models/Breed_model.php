<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Breed_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_breed_list(){
        
        $this->db->select('rotunro, rotu, lyhenne, IFNULL(roturyhma,0) AS roturyhma, harvinainen');
        $this->db->from('vrlv3_lista_rodut');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    function update_pulju_breeds($id, $breeds, &$msg){
        
        $insert_batch_batch = array();
        foreach ($breeds as $breed){
            $insert_batch = array();
            $insert_batch['pulju'] = $id;
            $insert_batch['rotu'] = $breed;
            
            $insert_batch_batch[] = $insert_batch;
        }
        
        $this->db->trans_start();

        $data = array('pulju' => $id);
        $this->db->delete('vrlv3_puljut_rodut', $data);
        $this->db->insert_batch('vrlv3_puljut_rodut', $insert_batch_batch);

        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE)
        {
            $msg = "Jokin meni vikaan. ";
            return false;
        }
        else return true;
        
    }
    
    
    
    public function get_breed_array_by_pulju($id){
        $data = array();
        
        $this->db->select('rotu');
        $this->db->from('vrlv3_puljut_rodut');
        $this->db->where('pulju', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[] = $row['rotu'];
            }
        }
        
        return $data;
    }
    
    public function get_breed_info($rotunro){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_rodut');
        $this->db->where('rotunro', $rotunro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array();
        }
        
        return array();
                
    }
    
    
    ////functions for form option lists
    
    
    public function get_breed_option_list(){
        $data = array();
        
        $this->db->select('rotunro, rotu');
        $this->db->from('vrlv3_lista_rodut');
        $this->db->order_by("rotu", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['rotunro']] = $row['rotu'];
            }
        }
        
        return $data;
    }
    
    
    public function get_breed_group_option_list(){
        $data = array();
        
        $this->db->select('id, ryhma');
        $this->db->from('vrlv3_lista_roturyhmat');
        $this->db->order_by("ryhma", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['id']] = $row['ryhma'];
            }
        }
        
        $data[0] = "Ei roturyhmää";
        
        return $data;
    }
    
    

    
    
    public function lisaa_rotu(&$msg, $rotu, $lyhenne, $roturyhma = null, $harvinainen = 0){
        $this->db->select('rotunro, rotu, lyhenne');
        $this->db->where('rotu', $rotu);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_rodut');
        
        $query = $this->db->get();
        //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $msg = "Rotua ei voitu lisätä: ";
            foreach ($query->result_array() as $roturivi){
                if ($roturivi['rotu'] == $rotu){
                    $msg .= "annettu rotunimi on jo käytössä. ";
                }
                if ($roturivi['lyhenne'] == $lyhenne){
                    $msg .= "annettu rotulyhenne on jo käytössä. ";
                }
            }
            return false;
        }
        

        $insert_data = array();
        $insert_data['rotu'] = $rotu;
        $insert_data['lyhenne'] = $lyhenne;
        if(isset($roturyhma) && $roturyhma != "0" ){
            $insert_data['roturyhma'] = $roturyhma;
        }
        $insert_data['harvinainen'] = $harvinainen;
      
        $this->db->insert('vrlv3_lista_rodut', $insert_data);
        $id = $this->db->insert_id();
        
        return $id;
        
        
    }
    
    
    public function muokkaa_rotu(&$msg, $id, $rotu, $lyhenne, $roturyhma = null, $harvinainen = 0){
        $this->db->select('rotunro, rotu, lyhenne');
        $this->db->where('rotu', $rotu);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_rodut');
        
        $query = $this->db->get();
        
         //onko rotut jo lisatty
        if ($query->num_rows() > 0)
        {
            $tulos = $query->result_array();
             if (!(sizeof($tulos) == 1 && $tulos[0]['rotunro'] == $id)) {

                $msg = "Rotua ei voitu lisätä: ";
                foreach ($tulos as $roturivi){
                    if ($roturivi['rotu'] == $rotu && $roturivi['rotunro'] != $id){
                        $msg .= "annettu rotunimi on jo käytössä. ";
                    }
                    if ($roturivi['lyhenne'] == $lyhenne  && $roturivi['rotunro'] != $id){
                        $msg .= "annettu rotulyhenne on jo käytössä. ";
                    }
                }
                return false;
             }
        }
        


        $insert_data = array();
        $insert_data['rotu'] = $rotu;
        $insert_data['lyhenne'] = $lyhenne;
        $insert_data['roturyhma'] = $roturyhma;
        $insert_data['harvinainen'] = $harvinainen;
        
            $this->db->where('rotunro', $id);
            $this->db->update('vrlv3_lista_rodut', $insert_data);
        
        return $id;
        
        
    }
    
    
    public function delete_rotu($id){
        
        $this->db->select('reknro');
        $this->db->where('rotu', $id);
        $this->db->from('vrlv3_hevosrekisteri');
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return false;
        
        } else {     
            $data = array('rotunro' => $id);
            $this->db->delete('vrlv3_lista_rodut', $data);
        }
    }

    //rotuen_periytyminen
    

    
    
}

