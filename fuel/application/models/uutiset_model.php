<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Uutiset_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    

    
     //Applications
    function tiedotus_tag_cloud_json()
    {
        $json = array();
        $this->db->select("kid, COUNT(tid) as weight");
        $this->db->group_by("kid"); 
        $query = $this->db->get('vrlv3_tiedotukset_kategoriat');
        
        foreach ($query->result() as $row){
            
            $json_row = array();
            $this->db->where('kid', $row->kid);
            $kid_query = $this->db->get('vrlv3_lista_tiedotuskategoriat');
            $json_row['text'] = $kid_query->row()->kategoria;
            $json_row['weight'] = $row->weight;
            $json_row['link'] = site_url('tiedotukset/kategoria/' . $row->kid);
            
            $json[] = $json_row;           
            
        }
        
        return json_encode($json);     
        
    }
    
    function muokkaa_tiedotus ($tid, $otsikko, $teksti, $tagit = array()){
        $insert_data = array();            
        $date = new DateTime();
        $date->setTimestamp(time());
        
        $insert_data['muokpvm'] = $date->format('Y-m-d H:i:s');
        $insert_data['otsikko'] = $otsikko;
        $insert_data['teksti'] =  nl2br($teksti);
        $insert_data['muokkaaja'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->where('tid', $tid);
        $this->db->update('vrlv3_tiedotukset', $insert_data);
        
        //poistetaan vanhat kategoriat
        $this->db->where('tid', $tid);
        $this->db->delete('vrlv3_tiedotukset_kategoriat');
        
        //lisätään uudet kategoriat
        foreach ($tagit as $tag)
        {            
            $data_tiedotus_kategoria = array();
            $data_tiedotus_kategoria['kid'] = $tag;
            $data_tiedotus_kategoria['tid'] = $tid;
            $this->db->insert('vrlv3_tiedotukset_kategoriat', $data_tiedotus_kategoria);        
        }
        
        return $tid;
        
        
    }
    
    function laheta_tiedotus($otsikko, $teksti, $tagit=array()){
        $insert_data = array();            
        $date = new DateTime();
        $date->setTimestamp(time());
                    

        $insert_data['muokpvm'] = $date->format('Y-m-d H:i:s');
        $insert_data['otsikko'] = $otsikko;
        $insert_data['teksti'] =  nl2br($teksti);
        $insert_data['aika'] = $date->format('Y-m-d H:i:s');
        $insert_data['lahettaja'] = $this->ion_auth->user()->row()->tunnus;
        $insert_data['julkinen'] = 1;
        $insert_data['muokkaaja'] = $this->ion_auth->user()->row()->tunnus;
            
        
      
        $this->db->insert('vrlv3_tiedotukset', $insert_data);
            
      
        //haetaan sen käsitellyn tid
        $this->db->select("tid");
        $this->db->where($insert_data);
        $this->db->order_by("aika", "desc"); 
        $tid_query = $this->db->get('vrlv3_tiedotukset');
        $tid = $tid_query->row()->tid;
                
                
        foreach ($tagit as $tag)
        {            
            $data_tiedotus_kategoria = array();
            $data_tiedotus_kategoria['kid'] = $tag;
            $data_tiedotus_kategoria['tid'] = $tid;
            $this->db->insert('vrlv3_tiedotukset_kategoriat', $data_tiedotus_kategoria);        
        }
        
        return $tid;
                                       
    }
    
    
    
    function hae_kategoria($kid, $limit, $offset)
    {
        $this->db->select("vrlv3_tiedotukset.tid, vrlv3_tiedotukset.otsikko, vrlv3_tiedotukset.teksti, vrlv3_tiedotukset.lahettaja, vrlv3_tiedotukset.aika");
        $this->db->from("vrlv3_tiedotukset");
        $this->db->join('vrlv3_tiedotukset_kategoriat', 'vrlv3_tiedotukset.tid = vrlv3_tiedotukset_kategoriat.tid');
        $this->db->where('vrlv3_tiedotukset_kategoriat.kid', $kid);
        $this->db->order_by("aika", "desc");
        $this->db->limit($limit, $offset);    
        $kat_query = $this->db->get();
           
        return $this->_kasittele_tiedotukset($kat_query->result_array());
        
    }
    
    
    function hae_kategoria_kpl($kid)
    {
        $this->db->select("vrlv3_tiedotukset.tid, vrlv3_tiedotukset.otsikko, vrlv3_tiedotukset.teksti, vrlv3_tiedotukset.lahettaja, vrlv3_tiedotukset.aika");
        $this->db->from("vrlv3_tiedotukset");
        $this->db->join('vrlv3_tiedotukset_kategoriat', 'vrlv3_tiedotukset.tid = vrlv3_tiedotukset_kategoriat.tid');
        $this->db->where('vrlv3_tiedotukset_kategoriat.kid', $kid);
           
        return $this->db->count_all_results();
        
    }
    
    function hae_tiedotukset($limit, $offset, $y = -1, $m = -1)
    {
        $json = array();
        $this->db->select("tid, otsikko, teksti, lahettaja, aika");        
        if($y > 0){
            $this->db->where('YEAR(aika)', $y); 
            if($m > 0){
                $this->db->where('MONTH(aika)', $m); 
            }
        }
        
        $this->db->order_by("aika", "desc");
        if($limit > 0){
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get('vrlv3_tiedotukset');
        
        return  $this->_kasittele_tiedotukset($query->result_array());
        
    }
    
    function hae_tiedotukset_kpl($y = -1, $m = -1)
    {
        $json = array();
        $this->db->select("tid, otsikko, teksti, lahettaja, aika");
         $this->db->from("vrlv3_tiedotukset");

        if($y > 0){
            $this->db->where('YEAR(aika)', $y); 
            if($m > 0){
                $this->db->where('MONTH(aika)', $m); 
            }
        }
                
        return $this->db->count_all_results();
        
    }
    
    function delete_tiedotus($id)
    {        
        $this->db->delete('vrlv3_tiedotukset', array('tid' => $id));
    }
    
    
    function hae_tiedotus($tid)
    {
        $json = array();
        $this->db->select("tid, otsikko, teksti, lahettaja, aika");        
        $this->db->where('tid', $tid); 
        $this->db->order_by("aika", "desc");  
        $query = $this->db->get('vrlv3_tiedotukset');
        
        return  $this->_kasittele_tiedotukset($query->result_array());
        
    }
    

    function hae_kategoriat_options(){
        $this->db->select("vrlv3_lista_tiedotuskategoriat.kid as kid, vrlv3_lista_tiedotuskategoriat.kategoria as kat");
        $this->db->order_by("kat");
            $this->db->from("vrlv3_lista_tiedotuskategoriat");
            $kat_query = $this->db->get();
            $kategoriat = array();
            foreach ($kat_query->result_array() as $kat_row){
                $kategoriat[$kat_row['kid']] = $kat_row['kat'];
            }
            
            return $kategoriat;
    }
    
    private function _kasittele_tiedotukset($tiedotuslista){
        $tiedotukset = array();
        foreach ($tiedotuslista as $row){
            
            $tiedotus = $row;
                       
            $this->db->select("vrlv3_lista_tiedotuskategoriat.kid as kid, vrlv3_lista_tiedotuskategoriat.kategoria as kat");
            $this->db->from("vrlv3_lista_tiedotuskategoriat");
            $this->db->join('vrlv3_tiedotukset_kategoriat', 'vrlv3_lista_tiedotuskategoriat.kid = vrlv3_tiedotukset_kategoriat.kid');
            $this->db->where('vrlv3_tiedotukset_kategoriat.tid', $row['tid']);           
            $kat_query = $this->db->get();
            $tiedotus['kategoriat'] = array();
            foreach ($kat_query->result_array() as $kat_row){
                $tiedotus['kategoriat'][] = $kat_row;
            }
            
            $tiedotukset[] = $tiedotus;
            
        }
        
        return $tiedotukset;
    
    }
    
    
    


        
        
 
}