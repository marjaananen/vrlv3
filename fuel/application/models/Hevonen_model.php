<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Hevonen_model extends Base_module_model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $genders = array(1=>'tamma', 2=>'ori', 3=>'ruuna');


    
    function get_hevonen($reknro)
    {
        
        $hevonen = array();
        $this->db->select("reknro, h.nimi as h_nimi, r.rotu as h_rotunimi, sukupuoli, sakakorkeus, syntymaaika, v.vari as h_varinimi, p.painotus as h_painotusnimi, syntymamaa, h.url as h_url, rekisteroity,
                          kotitalli, t.url as t_url, t.nimi as t_nimi, kuollut, kuol_pvm, rotunro, maa, vid, pid");
        $this->db->where("reknro", $this->CI->vrl_helper->vh_to_number($reknro));

        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_maat", "h.syntymamaa = vrlv3_lista_maat.id", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_lista_painotus as p", "h.painotus = p.pid", 'left outer');
        $this->db->join("vrlv3_tallirekisteri as t", "h.kotitalli = t.tnro", 'left outer');

        $query = $this->db->get();
        $hevonen = $query->row_array();

        
        $hevonen['reknro'] = $reknro;
        $hevonen['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($hevonen['syntymaaika']);
        $hevonen['rekisteroity'] = $this->vrl_helper->sql_date_to_normal($hevonen['rekisteroity']);
        $hevonen['kuol_pvm'] = $this->vrl_helper->sql_date_to_normal($hevonen['kuol_pvm']);
        $hevonen['sukupuoli'] = $this->genders[$hevonen['sukupuoli']];
        
        
        
        return $hevonen;        
        
       
    }
    
    function get_omistajat($reknro){
        

    }
    
    
    //functions for search
    function search_horse($name, $rotu, $gender, $dead, $color, $birthyear){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");

        if ($dead == 1){
            $this->db->where("kuollut", 1);
        }
        else $this->db->where("kuollut", 0);
        
        if($rotu > -1){
            $this->db->where("rotu", $rotu);
        }
        if($color > -1){
            $this->db->where("vari", $color);
        }
        if($gender > -1){
            $this->db->where("sukupuoli", $gender);
        }
        if(isset($birth_year) && $birth_year > 1000){
            $this->db->where("syntymaaika <", ($birth_year+1)."-01-01");
            $this->db->where("syntymaaika >", ($birth_year-1)."-12-31");
        }
        
    
        if(!empty($name))
        {
            if(strpos($name, '*') !== false)
                $this->db->where('nimi LIKE "' . str_replace('*', '%', $name) . '"');
            else
                $this->db->where('nimi', $name);
        }        
        
        $this->db->from('vrlv3_hevosrekisteri');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
        
    }
    
    //functions for pedigree
    
    function get_suku($reknro, $tunnus ="", &$suku){
        
        //turvamekanismi, jos on vahingossa joku asettanut hevosen isovanhemmakseen tms...
        if (strlen($tunnus) < 11){
            $hevonen = $this->_get_suku_info($reknro);
        
            if($tunnus != ""){
                $suku[$tunnus] = $hevonen;
            }
            
            if (isset($hevonen["i_nro"])){
                $this->get_suku($this->CI->vrl_helper->get_vh($hevonen["i_nro"]), $tunnus."i", $suku);
            }
            
            if (isset($hevonen["e_nro"])){
                $this->get_suku($this->CI->vrl_helper->get_vh($hevonen["e_nro"]), $tunnus."e", $suku);
            }
        }
        
    }
    
    

    private function _get_suku_info($reknro){
        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, v.lyhenne as vari, sukupuoli, sakakorkeus, i_nro, e_nro");
        $this->db->where("h.reknro", $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');
        
        $query = $this->db->get();
        $array = $query->row_array();
        $array['reknro'] = $this->CI->vrl_helper->get_vh($array['reknro']);
        return $array;
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
    

    
	public function get_gender_option_list(){
        return $this->genders;
    }
    
	public function get_color_option_list(){
        $data = array();
        
        $this->db->select('vid, vari');
        $this->db->from('vrlv3_lista_varit');
        $this->db->order_by("vari", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['vid']] = $row['vari'];
            }
        }
        
        return $data;
        
    }
	
    
    
    
}

