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
                             "gen_savy"=>0);
    
    private $color_gen_name_norm_array = array("gen_vkko"=>"Voikko",
                             "gen_tvkko"=>"Tuplavoikko",
                             "gen_hkko"=>"Hallakko",
                             "gen_hp"=>"Hopea",
                             "gen_cha"=>"Shampanja",
                             "gen_p"=>"Pearl",
                             "gen_km"=>"Kimo",
                             "gen_pais"=>"Päistärikkö");
    private $color_gen_name_kirj_array = array(
                             "gen_kirj"=>"Kirjavuus",
                             "gen_kirj_t"=>"Kirjavuus (Tobiano)",
                             "gen_kirj_s"=>"Kirjavuus (Sabino)",
                             "gen_kirj_spl"=>"Kirjavuus (Splashed white)",
                             "gen_kirj_fo"=>"Kirjavuus (Frame Overo)",
                             "gen_kirj_tkirj"=>"Kirjavuus (Tiikerinkirjava)");
    
    
    private $color_base = array("Ei tiedossa", "rn", "rt", "m");
    
    
    //info
    public function get_colour_info($id){
        
        $this->db->select('*');
        $this->db->from('vrlv3_lista_varit');
        $this->db->where("vid", $id);
        $query = $this->db->get();
        
        return $query->row_array();
                
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
    
}

