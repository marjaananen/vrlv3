<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Color_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    private $color_gen_array = array("gen_vkko"=>0,
                             "gen_tvkko"=>0,
                             "gen_hkko"=>0,
                             "gen_hp"=>0,
                             "gen_cha"=>0,
                             "gen_p"=>0,
                             "gen_km"=>0,
                             "gen_pais"=>0,
                             "gen_kirj"=>0,
                             "gen_kirj_t"=>0,
                             "gen_kirj_s"=>0,
                             "gen_kirj_spl"=>0,
                             "gen_kirj_fo"=>0,
                             "gen_kirj_tkirj"=>0,
                             "gen_mush" => 0,
                             "gen_savy"=>0);
    
    private $color_gen_name_norm_array = array("gen_vkko"=>"Voikko",
                             "gen_tvkko"=>"Tuplavoikko",
                             "gen_hkko"=>"Hallakko",
                             "gen_hp"=>"Hopea",
                             "gen_cha"=>"Shampanja",
                             "gen_p"=>"Pearl",
                             "gen_km"=>"Kimo",
                             "gen_pais"=>"Päistärikkö",
                             "gen_mush" => "Mushroom");
    private $color_gen_name_kirj_array = array(
                             "gen_kirj"=>"Kirjavuus",
                             "gen_kirj_t"=>"Kirjavuus (Tobiano)",
                             "gen_kirj_s"=>"Kirjavuus (Sabino)",
                             "gen_kirj_spl"=>"Kirjavuus (Splashed white)",
                             "gen_kirj_fo"=>"Kirjavuus (Frame Overo)",
                             "gen_kirj_tkirj"=>"Kirjavuus (Tiikerinkirjava)");
    
    
    private $color_base_name_array = array ("rn" => "ruunikko", 
                            "rt" => "rautias",
                            "m" => "musta", 
                            "emtpohja" => "Tuntematon pohjav&auml;ri");
    
    private $color_base = array("Ei tiedossa", "rn", "rt", "m");
    
    private $color_markings_name = array("merkit" => "Ei kirjava, mutta merkkej&auml;",
                                         "kantaasabino" => "Ei kirjava, mutta kantaa sabinoa",
                                         "kantaaspl" => "Ei kirjava, mutta kantaa splashed whitea");
    
    
    //info
    public function get_colour_info($id){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_varit');
        $this->db->where('vid', $id);
        $query = $this->db->get();
        return $query->row_array();
                
    }
    
    public function get_base_list(){
        return $this->color_base_name_array;
    }
    
    public function get_special_list(){
        return $this->color_gen_name_norm_array;
    }
    
    public function get_kirj_list(){
        return $this->color_gen_name_kirj_array;
    }
    
    public function get_colour_markings_list(){
        return $this->color_markings_name;
    }
    
    public function get_genes_list(){
        return array("norm"=>$this->color_gen_name_norm_array, "kirj"=>$this->color_gen_name_kirj_array);
        
    }
    
    //lists
    public function get_colour_list(){
        
        $this->db->select('vid, vari, lyhenne, pvari');
        $this->db->from('vrlv3_lista_varit');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    public function get_color_genes($id){
        
        $gene_array = array();
        $pohja = null;
        
        $this->db->select('*');
        $this->db->where('vid', $id);
        $this->db->from('vrlv3_lista_varit');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $result =  $query->result_array();

            foreach ($this->color_gen_array as $gen=>$value){
                if($result[0][$gen]){
                    $gene_array[] = $gen;
                }
            }
            
            if($result[0]['pvari'] == 'rt'){
                $pohja = 'rt';
            }
            else if($result[0]['pvari'] == 'rn'){
                $pohja = 'rn';
            }
            else if($result[0]['pvari'] == 'm'){
                $pohja = 'm';
            }
            
            
        }
        
        return array("pohja"=>$pohja, "lista"=>$gene_array);
    }
    
    public function lisaa_vari(&$msg, $vari, $lyhenne, $pohjavari, $geenit){
        $this->db->select('vari, lyhenne, pvari');
        $this->db->where('vari', $vari);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_varit');
        
        $query = $this->db->get();
        //onko varit jo lisatty
        if ($query->num_rows() > 0)
        {
            $msg = "Väriä ei voitu lisätä: ";
            foreach ($query->result_array() as $varirivi){
                if ($varirivi['vari'] == $vari){
                    $msg .= "annettu värinimi on jo käytössä. ";
                }
                if ($varirivi['lyhenne'] == $lyhenne){
                    $msg .= "annettu värinimilyhenne on jo käytössä. ";
                }
            }
            return false;
        }
        
        //tsekataanpa geenit
        $insert_data = $this->color_gen_array;
        $insert_data['vari'] = $vari;
        $insert_data['lyhenne'] = $lyhenne;
                if ($pohjavari == 'rn' || $pohjavari == 'rt' || $pohjavari == 'm'){
        $insert_data['pvari'] = $pohjavari;
        }
        
        if(isset($geenit) && sizeof($geenit)> 0){
            foreach ($geenit as $geeni){
                $insert_data[$geeni] = 1;
            }
        }
        
            $this->db->insert('vrlv3_lista_varit', $insert_data);
            $id = $this->db->insert_id();
        
        return $id;
        
        
    }
    
    
    public function muokkaa_vari(&$msg, $id, $vari, $lyhenne, $pohjavari, $geenit){
        $this->db->select('vid, vari, lyhenne, pvari');
        $this->db->where('vari', $vari);
        $this->db->or_where('lyhenne', $lyhenne);
        $this->db->from('vrlv3_lista_varit');
        
        $query = $this->db->get();
        //onko varit jo lisatty
        if ($query->num_rows() > 0)
        {
            $tulos = $query->result_array();
            
            if (!(sizeof($tulos) == 1 && $tulos[0]['vid'] == $id)) {
            
            $msg = "Väriä ei voitu muokata: ";
            foreach ($tulos as $varirivi){
                if ($varirivi['vari'] == $vari && $varirivi['vid'] != $id){
                    $msg .= "annettu värinimi on jo käytössä. ";
                }
                if ($varirivi['lyhenne'] == $lyhenne && $varirivi['vid'] != $id){
                    $msg .= "annettu värinimilyhenne on jo käytössä. ";
                }
            }
            return false;
            }
        }
        
        //tsekataanpa geenit
        $insert_data = $this->color_gen_array;
        $insert_data['vari'] = $vari;
        $insert_data['lyhenne'] = $lyhenne;
        
        if ($pohjavari == 'rn' || $pohjavari == 'rt' || $pohjavari == 'm'){
        $insert_data['pvari'] = $pohjavari;
        }
        
        if(isset($geenit)){
        foreach ($geenit as $geeni){
            $insert_data[$geeni] = 1;
        }
        }
            $this->db->where('vid', $id);
            $this->db->update('vrlv3_lista_varit', $insert_data);
        
        return $id;
        
        
    }
    
    
    public function delete_vari($id){
        
        $this->db->select('reknro');
        $this->db->where('vari', $id);
        $this->db->from('vrlv3_hevosrekisteri');
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return false;
        } else {     
            $data = array('vid' => $id);
            $this->db->delete('vrlv3_lista_varit', $data);
        }
    }

    //varien_periytyminen
    

    
    
}

