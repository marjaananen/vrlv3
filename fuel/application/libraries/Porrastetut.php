<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Porrastetut
{

    private $CI;
    
    private $levels = array(
                            0=>array("point_min"=>0, "point_max"=>200, "points_to_level_up"=>200, "min_age"=>3),
                            1=>array("point_min"=>201, "point_max"=>600, "points_to_level_up"=>400, "min_age"=>4),
                            2=>array("point_min"=>601, "point_max"=>1000, "points_to_level_up"=>400, "min_age"=>4),
                            3=>array("point_min"=>1001, "point_max"=>1400, "points_to_level_up"=>400, "min_age"=>5),
                            4=>array("point_min"=>1401, "point_max"=>1800, "points_to_level_up"=>400, "min_age"=>5),
                            5=>array("point_min"=>1801, "point_max"=>2400, "points_to_level_up"=>600, "min_age"=>6),
                            6=>array("point_min"=>2401, "point_max"=>3000, "points_to_level_up"=>600, "min_age"=>6),
                            7=>array("point_min"=>3001, "point_max"=>3800, "points_to_level_up"=>800, "min_age"=>7),
                            8=>array("point_min"=>3801, "point_max"=>4600, "points_to_level_up"=>800, "min_age"=>7),
                            9=>array("point_min"=>4601, "point_max"=>5600, "points_to_level_up"=>1000, "min_age"=>8),
                            10=>array("point_min"=>5601, "point_max"=>6600, "points_to_level_up"=>1000, "min_age"=>8)
                                                   
                            );
    private $aste = array(1=> "Seurataso", 2=>"Aluetaso", 3=> "Kansallinen taso");
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model("Trait_model");
        $this->CI->load->model("Jaos_model");
        $this->CI->load->model("Hevonen_model");
    }
    
    public function get_levels(){
        return $this->levels;
    }
    
    public function get_level_by_points($points){
        $level = 0;
        
        foreach ($this->levels as $nro=>$info){
            if($points > $info['point_max']){
                $level = $nro + 1;
            }else {
                break;
            }
        }
        
        return $level;
        
    }

    
    public function get_porrastetut_jaokset(){
        return $this->CI->Jaos_model->get_jaos_porr_list();
    }
    
    public function get_all_porrastettu_info(){
        $full_info = array();
        $jaokset =  $this->CI->Jaos_model->get_jaos_porr_list();
        foreach ($jaokset as $jaos){
            $full_info[$jaos['id']]['jaos'] = $jaos;
            $full_info[$jaos['id']]['traits'] = $this->get_traits($jaos['id']);
            $full_info[$jaos['id']]['classes'] = $this->get_classes_by_jaos($jaos['id']);
        }
        
        return $full_info;
            
    }
    
    public function get_classes_by_jaos($id){ 
        return $this->CI->Jaos_model->get_class_list($id, true, true);
    }
    
    public function get_asteet(){
        return $this->aste;
    }
    
    //Ominaisuudet
    public function get_traits($jaos = null){
        return $this->CI->Trait_model->get_trait_list($jaos);
    }
    
    public function get_trait_names_array(){
        $traits = $this->get_traits();
        $full_trait_list = array();
        
        foreach ($traits as $trait){
            $full_trait_list[$trait['id']] = $trait['ominaisuus'];
        }
        
        return $full_trait_list;
    }
    
    public function get_empty_trait_array(){
        $traits = $this->get_traits();
        $full_trait_list = array();
        
        foreach ($traits as $trait){
            $full_trait_list[$trait['id']] = 0.00;
        }
        
        return $full_trait_list;
    }
    
    
    
    public function get_horses_full_traitlist($reknro){
        $full_trait_list = array();
        $full_trait_list = $this->get_empty_trait_array();
        
        if(isset($reknro)){
            $horse_traits = $this->CI->Hevonen_model->get_horse_traits($reknro);
            foreach ($horse_traits as $horse){
                $full_trait_list[$horse['id']] = $horse['arvo'];
            }
        }
        return $full_trait_list;
    
    }
    
    
    public function get_horses_point_sum_for_sport($horse_list, $jaos, $jaos_traits = null){
        if(!isset($jaos_traits)){
            $jaos_traits = $this->get_traits($jaos);
        }
        
        $sum = 0.00;           
        foreach ($jaos_traits as $trait){
            $sum += $horse_list[$trait['id']];
        }
        
        return $sum;
    }
    
    
    
    public function get_horses_full_level_list($reknro){
        $full_info = array();
        $jaokset =  $this->CI->Jaos_model->get_jaos_porr_list();
        $horse_list = $this->get_horses_full_traitlist($reknro);
        
        foreach ($jaokset as $jaos){
            $full_info[$jaos['lyhenne']]['points'] = $this->get_horses_point_sum_for_sport($horse_list, $jaos['id']);           
            $full_info[$jaos['lyhenne']]['level'] = $this->get_level_by_points($full_info[$jaos['lyhenne']]['points']);

        }
        
        return $full_info;

    }
        
        
        

        //periytyminen
        
    public function get_foals_traitlist($i, $e){
        $empty = $this->get_empty_trait_array();
        $i_list = $this->get_horses_full_traitlist($i);
        $e_list = $this->get_horses_full_traitlist($e);
        $foal = array();
        foreach ($empty as $id=>$trait){
            $foal[$id] = (($i_list[$id] + $e_list[$id])/2)*0.25;
        }
        return $foal;
    }
    
    
}