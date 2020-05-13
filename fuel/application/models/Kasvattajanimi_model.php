<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Kasvattajanimi_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    //names
    function get_users_names($pinnumber)
    {
        $this->db->select('vrlv3_kasvattajanimet.id, kasvattajanimi, rekisteroity, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
        //names
    function get_users_names_amount($pinnumber)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
        $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    function get_names_owners($pinnumber)
    {
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, nimimerkki, taso, CONCAT(vrlv3_kasvattajanimet_omistajat.tunnus, "/", vrlv3_kasvattajanimet_omistajat.kid) as id');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_kasvattajanimet_omistajat.tunnus');
        $this->db->where('vrlv3_kasvattajanimet.id', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_names_breeds($pinnumber)
    {
        $this->db->select('rotunro, vrlv3_lista_rodut.rotu, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_kasvattajanimet.id', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_stables_names($pinnumber)
    {
        $this->db->select('vrlv3_kasvattajanimet.id, kasvattajanimi, rekisteroity, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_kasvattajanimet.tnro', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    function get_name($nro)
    {
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->where('id', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
    
    function get_names_by_foal_count($type = "DESC"){
        $this->db->select('vrlv3_kasvattajanimet.id, vrlv3_kasvattajanimet.kasvattajanimi, vrlv3_kasvattajanimet.rekisteroity, count(reknro) as amount');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_hevosrekisteri', 'vrlv3_kasvattajanimet.id = vrlv3_hevosrekisteri.kasvattajanimi_id', 'left');
        $this->db->group_by('vrlv3_kasvattajanimet.id');
        $this->db->limit(500);

        
        if ($type == "ASC"){
             $this->db->order_by('amount', "ASC");

        }
        
        else   {      
             $this->db->order_by('amount', "DESC");

        }
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();

        
    }

    
    function search_names($name, $breed)
    {
        $this->db->select('vrlv3_kasvattajanimet.id, kasvattajanimi, rekisteroity, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        
        if(!empty($name))
        {
            if(strpos($name, '*') !== false)
                $this->db->where('kasvattajanimi LIKE "' . str_replace('*', '%', $name) . '"');
            else
                $this->db->where('kasvattajanimi', $name);
        }
            
        if($breed != '-1')
            $this->db->where('vrlv3_kasvattajanimet_rodut.rotu', $breed);
        
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function add_name($insert_data, $breed, $owner){
            $this->db->insert('vrlv3_kasvattajanimet', $insert_data);
            $id = $this->db->insert_id();            
            $this->add_owner_to_name($id, $owner);
            $this->add_breed_to_name($id, $breed);
            $this->add_horses_to_name($id, $insert_data['kasvattajanimi']);
        }
        
        
        
    function add_owner_to_name($id, $applicant)
    {
        $data = array('kid' => $id, 'tunnus' => $applicant);
        
        $this->db->insert('vrlv3_kasvattajanimet_omistajat', $data);
    }
    
    function add_breed_to_name($id, $breed)
    {
        $data = array('kid' => $id, 'rotu' => $breed);
        
        $this->db->select("*");
        $this->db->from('vrlv3_kasvattajanimet_rodut');
        $this->db->where($data);     
        $query = $this->db->get();
        
        if ($query->num_rows() == 0){     
            $this->db->insert('vrlv3_kasvattajanimet_rodut', $data);
        }
    }
    
    function delete_breed_from_name($id, $breed)
    {
        $data = array('kid' => $id, 'rotu' => $breed);
        
        $this->db->delete('vrlv3_kasvattajanimet_rodut', $data);
    }
    
    function add_horses_to_name($id, $name){
        
        $this->db->select("DISTINCT(rotu) as rotu");
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi_id', NULL);
        $this->db->where('kasvattajanimi', $name);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row){
                $this->add_breed_to_name($id, $row['rotu']);
            }
        }
        
        $this->db->where('kasvattajanimi_id', NULL);
        $this->db->where('kasvattajanimi', $name);
        $this->db->update('vrlv3_hevosrekisteri', array("kasvattajanimi_id"=>$id));
        
    }
    
    function delete_name($id){
        $this->db->where('id', $id);
        $this->db->delete('vrlv3_kasvattajanimet');
    }
    
    function update_breeds($id, $name, &$msg = ""){
        $deleted = 0;
        $added = 0;
        $horses = 0;
        
        //add id's to horses with a name
        $this->add_horses_to_name($id, $name);
        
        //add un-added breeds
        $query = $this->db->query('SELECT distinct(rotu) FROM vrlv3_hevosrekisteri as hepparekka where kasvattajanimi_id = '.$id.' AND 
            NOT EXISTS (SELECT distinct(rotu) from vrlv3_kasvattajanimet_rodut as rodut where kid = '.$id.' AND rodut.rotu = hepparekka.rotu)');        
         
         $added = $query->num_rows();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row){
                $this->add_breed_to_name($id, $row['rotu']);
            }
        }
        
        //remove removed breeds
        $this->db->select("rotu");
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi_id', $id);
        
        $query = $this->db->get();
        
        //remove only if there are any breeds left
        $horses = $query->num_rows();
        if ($query->num_rows() > 0)
        {
             $query = $this->db->query('SELECT distinct(rotu) from vrlv3_kasvattajanimet_rodut as rodut where kid = '.$id.' AND
                              NOT EXISTS (SELECT distinct(rotu) FROM vrlv3_hevosrekisteri as hepparekka where kasvattajanimi_id = '.$id.' and rodut.rotu = hepparekka.rotu)');
        
           $deleted = $query->num_rows();
           if ($query->num_rows() > 0)
           {
               foreach ($query->result_array() as $row){
                   $this->delete_breed_from_name($id, $row['rotu']);
               }
           }
        }
        
        if ($horses == 0){ $msg = "Kasvattajanimellä ei ollut yhtään kasvattia, joten rotuja ei käsitelty."; return false;}
        else { $msg = $added . " rotu(a) lisätty, " .  $deleted . " rotu(a) poistettu."; return true;}
        
        
    }
    

    function is_name_owner($pinnumber, $tnro)
    {
        $this->db->select('tunnus');
        $this->db->from('vrlv3_kasvattajanimet_omistajat');
        $this->db->where('kid', $tnro);
        $this->db->where('tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    
    function is_name_id_in_use($tnro)
    {
        $this->db->select('id');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->where('id', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function is_name_in_use($tnro)
    {
        $this->db->select('kasvattajanimi');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->where('kasvattajanimi', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
 
}

