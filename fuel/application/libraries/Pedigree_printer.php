<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pedigree_printer {
  
  var $table_id = "sukutaulu";
  var $td_class = "sukutk";
  var $colours = array('#FFDFD3', '#E2F0CB', '#C7CEEA', '#ECE4D0', ' #BCE0F0', '#DAE795', '#FBFBCC', '#F8E3EB', '#A8C1C6', '#D2C3C3', '#E7E3E0', '#F8C8A5', '#F6FFE5', '#BFEADC', '#E4FFFF');
  var $multiples = array();

 public function createPedigree($pedigree, $max = 4) {
  //var_dump($pedigree);
  $this->_handle_multiples($pedigree, $max);
		
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
 
 private function _handle_multiples($pedigree, $max){
  $horses = array();
  $multiples = array();
  foreach($pedigree as $id=>$horse){
   if(strlen($id) <= $max){
    if(isset($horse['reknro'])){
     if(in_array($horse['reknro'], $horses) && !isset($multiples[$horse['reknro']])){
      $multiples[$horse['reknro']] = $this->colours[sizeof($multiples)];
     }else {
       $horses[] = $horse['reknro'];
 
     }
    }
   }
  }
  
  $this->multiples = $multiples;
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
 
 private function _set_td_colour($hevonen){
    if (isset($hevonen['reknro']) && isset($this->multiples[$hevonen['reknro']])){
    return  ' bgcolor="'. $this->multiples[$hevonen['reknro']] .'" ';
				
  	}else {
    return "";
   }
 }
  
  
  private function print_parent($maxgeneration, $generation, $rs, $pedigree, $tuntematon = "", $hevonen = '') {
		

		$rowspan = $rs / 2;
  $colourcode = "";
  	if (isset($pedigree[$hevonen])){
    $colourcode = $this->_set_td_colour($pedigree[$hevonen]);
				
  	}
		print "\t\t".'<td  class="'.$this->td_class.'" rowspan="'.$rowspan.'"'.$colourcode.'>'.$hevonen.'. ';
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
  
  
  
  
  
  public function inbreedingPercentage ($id, &$lasketut = array(), $yhteiset_nimet = array()){
		if (array_key_exists($id, $lasketut)){
			return $lasketut[$id];
		}
		
		$CI =& get_instance();
		$CI->load->model('Arkisto_model');

		//Annettiinko yhteisiä nimiä valmiiksi?
		if ($id > -1 || empty($yhteiset_nimet)){
			$yhteiset_nimet = $CI->Arkisto_model->hae_yhteiset_nimet($id);
	
		}
		
		//löytyikö vieläkään?
		if (empty($yhteiset_nimet)){			
			$lasketut[$id] = 0;
			$CI->Arkisto_model->muokkaa_perustietotaulu($id, array("ssprosentti"=>0), true);
			return 0; //Jos ei yhteisiä nimiä, prosentti on 0.
		}
		
		$kaikki = array();
		$isanpuoli = array();
		$emanpuoli = array();
		
		foreach ($yhteiset_nimet as $nimi){
			$kaikki[$nimi['vanhempi']][] = $nimi['tunnus'];
			
			/*if (isset($nimi['ssprosentti']) || !is_null($nimi['ssprosentti'])){
				$lasketut[$nimi['vanhempi']][] = $nimi['ssprosentti'];
			}*/
			
			if (substr($nimi['tunnus'], 0, 1) == 'e'){
				$emanpuoli[$nimi['vanhempi']][] = $nimi['tunnus'];
			}
			
			else if (substr($nimi['tunnus'], 0, 1) == 'i'){
				$isanpuoli[$nimi['vanhempi']][] = $nimi['tunnus'];
			
			}
				
		}
		
		$lapikaydyt_tunnukset = array();
		$prosentti = 0;
		
		foreach ($kaikki as $sukulaisid=>$tunnuslista){
			$isanp_lapikaymattomat = array();
			$emanp_lapikaymattomat = array();
			
	
			
			foreach ($tunnuslista as $tunnus){
				//Onko varsa jo tarkistettu? Tiputetaan vika merkki pois niin katsotaan.
				$varsantunnus = substr($tunnus, 0, (strlen($tunnus)-1));
				
				//Jo ko. otuksen varsaa ei olla käyty läpi...
				if (!isset($lapikaydyt_tunnukset[$varsantunnus])){
					if (substr($tunnus, 0, 1) == 'e'){
						$emanp_lapikaymattomat[] = $tunnus;
					}
					else if (substr($tunnus, 0, 1) == 'i'){
						$isanp_lapikaymattomat[] = $tunnus;
					}
					
				}
				//Varsa oli käyty läpi, ni sitten on otus itsekin
				else {
					
					$lapikaydyt_tunnukset[] = $tunnus;
				}						
				
			}
			
			//Lasketaan sukulaisen oma prosentti, ja sukulaisen osuus laskettavasta prosentista
				$sukulaisen_osuus = $this->percentagecount ($emanp_lapikaymattomat, $isanp_lapikaymattomat, $emanpuoli, $isanpuoli, $sukulaisid);

				
				//Merkitään oma prosentti lasketuksi kys. sukulaiselle
				$lasketut[$sukulaisid] = $this->inbreedingPercentage($sukulaisid, $lasketut);
				$sukulaisen_oma = $lasketut[$sukulaisid] + 1;
				$sukulaisen_kokonaispros_osuus = $sukulaisen_osuus * $sukulaisen_oma;
				


			
			//Kaikki nyt läpikäydyt tunnukset merkitään läpikäydyiksi
				$lapikaydyt_tunnukset = array_merge($lapikaydyt_tunnukset, $isanp_lapikaymattomat, $emanp_lapikaymattomat);
			
			//Lisätään tämän sukulaisen osuus kokonaisprosenttiin	
			$prosentti = $prosentti + $sukulaisen_kokonaispros_osuus;	
			
		}
		
		if ($id > -1){
			$CI->Arkisto_model->muokkaa_perustietotaulu($id, array("ssprosentti"=>$prosentti), true);
		}
		return  $prosentti;
			
		
	}
	
	
	private function percentagecount ($isanpl, $emanpl, $isanp, $emanp, $id){
		
		$summa = 0;
		
		if (sizeof($isanpl) == 0){
			$isanpl = $isanp[$id];
			
		}
		
		if (sizeof($emanpl) == 0){
			$emanpl = $emanp[$id];
			
		}

		foreach ($isanpl as $itunnus){
			foreach ($emanpl as $etunnus){

				$potenssi = (strlen($itunnus) - 1) + (strlen($etunnus) - 1) + 1;
				$summa = $summa + pow (0.5, $potenssi);				
			}
		}
		
		return $summa;
		
	}


}


