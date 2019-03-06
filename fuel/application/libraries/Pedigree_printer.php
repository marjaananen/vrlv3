<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pedigree_printer {
  
  var $table_id = "sukutaulu";
  var $td_class = "sukutk";


 public function createPedigree($pedigree, $max = 4) {
		
		$rowspan = (pow(2, $max)); 

		print	'<table id="'.$this->table_id.'">'."\n";

			print "\t".'<tr>'."\n";
			$this->sire($max, 1, $rowspan, $pedigree);
			print "\t".'</tr>'."\n";
			
			print "\t".'<tr>'."\n";
			$this->dam($max, 1, $rowspan, $pedigree);
			print "\t".'</tr>'."\n";
			
		print	'</table>'."\n";
	}

	private function sire($maxgeneration, $generation, $rs, $pedigree, $hevonen = '') {
		
		// Isä
		$hevonen .= 'i';
		
		
		// Hae ja tulosta isäkaakki
		$tuntematon = "";
		
		if($generation < 5){
			$tuntematon = "Tuntematon ori";
		}
		$this->print_parent($maxgeneration, $generation, $rs, $pedigree, $tuntematon, $hevonen);
		
	}

	private function dam($maxgeneration, $generation, $rs, $pedigree, $hevonen = '') {
		
		// Emä
		$hevonen .= 'e';
		
		// Hae ja tulosta emäkaakki
		$tuntematon = "";
		
		if($generation < 5){
			$tuntematon = "Tuntematon tamma";
		}
		
    $this->print_parent($maxgeneration, $generation, $rs, $pedigree, $tuntematon, $hevonen);

		
	}
  
  
  private function print_parent($maxgeneration, $generation, $rs, $pedigree, $tuntematon = "", $hevonen = '') {
		

		$rowspan = $rs / 2; 
				
		print "\t\t".'<td  class="'.$this->td_class.'" rowspan="'.$rowspan.'">'.$hevonen.'. ';
		if (isset($pedigree[$hevonen])) { $this->_print_hevonen($pedigree[$hevonen], true); } else { echo $tuntematon;}
		print "</td>\n";
		
		if( $generation < $maxgeneration)
		{
			$this->sire($maxgeneration, $generation+1, $rowspan, $pedigree, $hevonen);
			
			if( $generation+1 == $maxgeneration) {
			
				print "\t\t".'<tr>'."\n";
				print "\t";
				$this->dam($maxgeneration, $generation+1, $rowspan, $pedigree, $hevonen);
				print "\t\t".'</tr>'."\n";
				
			} else {
				$this->dam($maxgeneration, $generation+1, $rowspan, $pedigree, $hevonen);
			}
		} 
		
	}
  
  
  
  private function _print_hevonen($hevonen, $line_breaks=true){
    $break = " ";
    if ($line_breaks){
      $break = "<br>";
    }
    print $hevonen['nimi'] .$break. '(<a href="'. site_url("virtuaalihevoset/hevonen/".$hevonen['reknro']) . '">'.$hevonen['reknro'].'</a>)';
    print $break;
    
    if (isset($hevonen['rotu'])){
      print $hevonen['rotu'];
    }
    if (isset($hevonen['sakakorkeus']) && $hevonen['sakakorkeus'] > 0){
      print ", ". $hevonen['sakakorkeus'] . "cm";
    }
    
    if (isset($hevonen['vari'])){
      print ", ". $hevonen['vari'];
    }

    
  }


}


