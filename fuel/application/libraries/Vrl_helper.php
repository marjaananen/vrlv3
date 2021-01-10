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
    
     return preg_replace("/[^0-9]/", "", $vh);					

    
  }
  
    
  public function vrl_to_number($vrl) {
    
    if ($this->check_vrl_syntax($vrl)){
      return preg_replace("/[^0-9]/", "", $vrl);					
    }
    
    else return -1;
    
  }

  public function get_vh($vh) {
    if (strlen($vh)==9){
      return "VH".substr($vh, 0, 2)."-".substr($vh, 2, 3)."-".substr($vh, 5, 4);
    }
    
    else {
      return "";
    }
  }
  
  
  public function sanitize_registration_date($date) {
    
    
    if ($date == null || strlen($date) < 1){
      return "Ei tiedossa";
    }
    
    else {
          $datetocompare = date("Y.m.d", strtotime('2010-12-30'));
          $datetocheck = date("Y.m.d", strtotime($date));
                              
          if($datetocheck < $datetocompare){
             return "Vuonna 2010 tai aiemmin.";  
          }
          
          else {
            return date("d.m.Y", strtotime($date));
          }

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
  
  public function generate_vh($rotu, $nro){
    return "VH". date("y") ."-".sprintf("%03d", $rotu)."-".sprintf("%04d", $nro);
  }
  
  public function sql_date_to_normal($date){
    $oDate = new DateTime($date);
    return $oDate->format("d.m.Y");
  }
  
  public function normal_date_to_sql($date){
    $oDate = new DateTime($date);
    return $oDate->format("Y-m-d");
  }
  
  public function validateDate($date, $format = 'd.m.Y')
  {
    try {
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;

    }
    catch (Exception $e){
      return false;
    }
    
  }
  
  public function get_option_script($fieldname, $options = array()){
    $script = '<script type="text/javascript">
         function optionsAdd'.$fieldname.'(text){
         document.getElementById(\''.$fieldname.'\').value = text;
         }
         </script>';
      $lista = "";
      
      if (sizeof($options) > 0){
          foreach ($options as $option_id=>$option){
         $lista .= '[<a href="#" onClick="optionsAdd'.$fieldname.'(\''.$option_id.'\'); return false">'.$option.'</a>] ';
         }
      } else {
        $lista = "-";
      }
      
      return array("script"=>$script, 'list'=>$lista);

  }
  
  

  
  


}


