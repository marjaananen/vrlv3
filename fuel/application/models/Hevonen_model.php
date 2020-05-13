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
                          kotitalli, t.url as t_url, t.nimi as t_nimi, kuollut, kuol_pvm, rotunro, maa, vid, pid, kasvattajanimi, kasvattajanimi_id, kasvattaja_tunnus, kasvattaja_talli");
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
    
    
    
    
    
    public function onko_tunnus($tunnus){
        $this->db->where('reknro', $tunnus);
        $this->db->from('vrlv3_hevosrekisteri');
        $amount = $this->db->count_all_results();
        
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
        
    }
    
    function get_horse_owners($reknro)
    {
        $this->db->select('omistaja, nimimerkki, taso, CONCAT(vrlv3_hevosrekisteri_omistajat.omistaja, "/", vrlv3_hevosrekisteri_omistajat.reknro) as id');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_hevosrekisteri_omistajat.omistaja');
        $this->db->where('reknro', $this->CI->vrl_helper->vh_to_number($reknro));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_owners_horses($nro)
    {
        $this->db->select('vrlv3_hevosrekisteri.reknro, nimi, rotu, sukupuoli');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->join('vrlv3_hevosrekisteri', 'vrlv3_hevosrekisteri.reknro = vrlv3_hevosrekisteri_omistajat.reknro');
        $this->db->where('omistaja', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_owners_horses_amount($nro, $alive = 1)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->join('vrlv3_hevosrekisteri', 'vrlv3_hevosrekisteri.reknro = vrlv3_hevosrekisteri_omistajat.reknro');
        $this->db->where('omistaja', $nro);
                $query = $this->db->get();

        return $query->num_rows();
    }
    
    function get_users_foals($user){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattaja_tunnus', $user);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_names_foals_by_id($id){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi_id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    function get_names_foals_by_name($name){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi', $name);
        $this->db->where('kasvattajanimi_id IS NULL', NULL, FALSE);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    
      function get_stables_horses($nro)
    {
        $this->db->select('vrlv3_hevosrekisteri.reknro, nimi, rotu, sukupuoli');
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kotitalli', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_stables_foals($user){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattaja_talli', $user);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    //functions for search
    function search_horse($reknro, $name, $rotu, $gender, $dead, $color, $birthyear){
        $this->db->select("reknro, nimi, rotu, vari, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli, syntymaaika");
        
        //jos haetaan rekisterinumerolla, millään muulla ei ole väliä
        if (strlen($reknro) > 5){
            $this->db->where("reknro", $reknro);
        }
        else {
            if ($dead == 1){
                $this->db->where("kuollut", 1);
            }
            else { $this->db->where("kuollut", 0);}
            
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
            
            if(isset($breeder)){
                $this->db->where("kasvattaja_tunnus", $breeder);
            }
        
            if(!empty($name))
            {
                if(strpos($name, '*') !== false)
                    $this->db->where('nimi LIKE "' . str_replace('*', '%', $name) . '"');
                else
                    $this->db->where('nimi', $name);
            }
        }
        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->limit(1000);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
        
    }
    
    
    function add_owner_to_horse($id, $applicant, $taso = 1)
    {
        $data = array('reknro' => $id, 'tunnus' => $applicant, 'taso' => $taso);
        
        $this->db->insert('vrlv3_hevosrekisteri_omistajat', $data);
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
    
    //stats
    function get_stats_breed($rotu){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("rotu", $rotu);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
    
    
    function get_stats_colour($color){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("vari", $color);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
    
     
    function count_breedingname_amount($id){
        
        $this->db->select("COUNT(kasvattajanimi_id) as amount");
        $this->db->where("kasvattajanimi_id", $id);
  
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $breedingnames = $query->row()->amount;

        return $breedingnames;
        
    }
    
    
        
    function get_stats_country($country){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("syntymamaa", $country);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
        
        
    private function sort_gender_stats($array){
        $genders = array();
        $genders['tammat'] = 0;
        $genders['orit'] = 0;
        $genders['ruunat'] = 0;
        
        foreach ($array as $row){
            if($row['sukupuoli'] == 1){
                $genders['tammat'] = $row['amount'];
            }
            if($row['sukupuoli'] == 2){
                $genders['orit'] = $row['amount'];
            }
            if($row['sukupuoli'] == 3){
                $genders['ruunat'] = $row['amount'];
            }
        }
        
        $genders['total'] = $genders['tammat'] + $genders['orit'] + $genders['ruunat'];
        
        return $genders;
        
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
    

       public function get_country_list(){
        
        $this->db->select('id, maa, lyh');
        $this->db->from('vrlv3_lista_maat');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    
    
    public function get_breed_list(){
        
        $this->db->select('rotunro, rotu, lyhenne');
        $this->db->from('vrlv3_lista_rodut');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
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

