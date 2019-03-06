<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vrl_helper {


  public function check_vh_syntax($vh){
    $found_vh = array();
    $vh_amount = preg_match_all('/\VH[0-9]{2}\-[0-9]{3}\-[0-9]{4}/', $vh , $found_vh);
    
    if ($vh_amount == 1 && strlen($vh)==13){
      return true;
    }
    
    else {
      return false;      
    }
  }
  
  public function check_vrl_syntax($vrl){
    $found_vrl = array();
    $vrl_amount = preg_match_all('/\VRL\-[0-9]{5}/', $vrl , $found_vrl);
    $vrl_amount2 = preg_match_all('/[0-9]{5}/', $vrl , $found_vrl);

    //Sekä pelkkä numero että VRL-tunnus kelpaavat
    if (($vrl_amount == 1 && strlen($vrl) == 9) || ($vrl_amount2 == 1  && strlen($vrl) == 5)){
      return true;
    }
    
    else {
      return false;
    }
  }
  
  
  public function vh_to_number($vh){
    
    if ($this->check_vh_syntax($vh)){
      return preg_replace("/[^0-9]/", "", $vh);					
    }
    
    else return "";
    
  }
  
    
  public function vrl_to_number($vrl) {
    
    if ($this->check_vrl_syntax($vrl)){
      return preg_replace("/[^0-9]/", "", $vrl);					
    }
    
    else return "";
    
  }

  public function get_vh($vh) {
    if (strlen($vh)==9){
      return "VH".substr($vh, 0, 2)."-".substr($vh, 2, 3)."-".substr($vh, 5, 4);
    }
    
    else {
      return "";
    }
  }
  
  public function get_vrl($nro) {
    if ($this->check_vrl_syntax($nro)){
      
      if(strlen($nro)==5){
        return "VRL-".$nro;
      }
      
      else {
        return $nro;
      }
    }
    
    else {
      return "";
    }
  }
  
  public function sql_date_to_normal($date){
    $oDate = new DateTime($date);
    return $oDate->format("d.m.Y");
  }
  


}


