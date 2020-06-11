<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 



class Color_inheritance
{
    private $varit = array();
    private $pohjavarit = array();
    private $erikoisvarit = array();
    private $kirjavat = array();
    
    public function __construct($param)
    {
        $this->pohjavarit = $param['pohjavarit'];
        foreach ($param['pohjavarit'] as $vari=>$nimi){
            $this->varit[] = $vari;
        }
        $this->erikoisvarit = $param['erikoisvarit'];
        foreach ($param['erikoisvarit'] as $vari=>$nimi){
            $this->varit[] = $vari;
        }
        $this->kirjavat = $param['kirjavat'];
        foreach ($param['kirjavat'] as $vari=>$nimi){
            $this->varit[] = $vari;
        }
    }

    private function Periyta($isan, $eman){
	$varsalle = FALSE; // tuleeko varsalle erikoisvärimahis
	//Jos molemmat vanhemmat saman värisiä, mahis on isompi
	if ($isan == TRUE && $eman == TRUE){
		$varsalle = 2;
		}
		
	//Jo toinen vanhempi tiettyä väriä, mahis on pienempi
	else if ($isan == TRUE || $eman == TRUE){
		$varsalle = 1;
		}
	// Muutoin mahista ei ole.
	
	return $varsalle;
    }


    private function PeriytaTupla ($isankoko, $emankoko, $isapuol, $emapuol){
        //Jos molemmat vanhemmat homotsygootteja, varsa varmasti homotsygootti
        $babykoko = FALSE;
        $babypuol = FALSE;
        if ($isankoko == TRUE && $emankoko == TRUE){
            $babykoko = 3;
            }
        //Jos toinen vanhempi homotsygootti, ja toinen heterotsygootti, on mahdollisuus homoon tai heteroon. Hetero on varma.
        else if (($isankoko == TRUE && $emapuol == TRUE) || 
        ($isapuol == TRUE && $emankoko == TRUE)){
            $babypuol = 3;
            $babykoko = 2;
        }
        //Jos vain toinen vanhemmista homotsygootti, mutta toinen ei ollenkaan, varsa varmasti heterotsygootti
        else if ($isankoko == TRUE || $emankoko == TRUE){
            $babypuol = 3;
            }
        
        //Jos molemmat vanhemmat heterotsygootteja, mahdollisuus homoon tai heteroon
        else if ($isapuol == TRUE && $emapuol == TRUE){
            $babypuol = 2;
            $babykoko = 1;
            }
            
        //Jos toinen vanhemmista heterotsygootti, ja toinen ei, mahdollisuus heterostygoottiin
        else if ($isapuol == TRUE || $emapuol == TRUE){
            $babypuol = 1;
            }
        
        $baby = array ($babypuol, $babykoko);
        return $baby;
    }


    public function periytaVarit($isapohja, $emapohja, $dadlist = array(), $mumlist = array()){
        $dad = array(); // taulukko isän väreille
        $mum = array(); // taulukko emän väreille
        $baby = array(); // taulukko varsan väreille
        
        //luodaan taulukoiden sisällöt, aluksi ovat värittömiä
        $n = count($this->varit);
        for($i=0; $i < $n; $i++)
            {
              $dad[$this->varit[$i]] = FALSE;
              $mum[$this->varit[$i]] = FALSE;
              $baby[$this->varit[$i]] = FALSE;
            }
            
        //Haetaan lomakkeelta annetut isän värit
        $dad[$isapohja] = TRUE; //pohjaväri
        $tempdad = $dadlist;
        $n = count($tempdad);
        // Laitetaan valittujen värien arvoksi true
        for($i=0; $i < $n; $i++){
              $dad[$tempdad[$i]] = TRUE;
            }
         
          
        //Haetaan lomakkeelta annetut emän värit
        $ema[$emapohja] = TRUE; //pohjaväri
        $tempmum = $mumlist;
        $n = count($tempmum);
        // Laitetaan valittujen värien arvoksi true
        for($i=0; $i < $n; $i++){
            $mum[$tempmum[$i]] = TRUE;
        }
          
        
          //Sit aletaan periyttämään!
          
          // Perusvärit varsalle:
            //rautias + rautias = aina vain rautias
            if ($dad["rt"] == TRUE && $mum["rt"] == TRUE){
                $baby["rt"] = TRUE;
            }
            //musta + musta = vain musta tai rautias
            else if ($dad["m"] == TRUE && $mum["m"] == TRUE){
                $baby["rt"] = TRUE;
                $baby["m"] = TRUE;
            }
            //muutoin kaikki perusvärit mahollisia
            else {
                $baby["rt"] = TRUE;
                $baby["m"] = TRUE;
                $baby["rn"] = TRUE;
            }
            
            //Erikoisvärit varsalle:
            // 0 = ei ole/false
            // 1 = pienempi mahdollisuus
            // 2 = suurempi mahdollisuus
            // 3 = varma
            
            //voikosta, sabinosta ja splashedista erottaa homo- ja heterotsygootit:
            $voikko = $this->PeriytaTupla ($dad["gen_tvkko"], $mum["gen_tvkko"], $dad["gen_vkko"], $mum["gen_vkko"]);
            $baby["gen_vkko"] = $voikko[0];
            $baby["gen_tvkko"] = $voikko[1];
            $spl = $this->PeriytaTupla ($dad["gen_kirj_spl"], $mum["gen_kirj_spl"], $dad["merkit"], $mum["merkit"]);
            $baby["kantaaspl"] = $spl[0];
            $baby["gen_kirj_spl"] = $spl[1];
            $sabino = $this->PeriytaTupla ($dad["gen_kirj_s"], $mum["gen_kirj_s"], $dad["merkit"], $mum["merkit"]);
            $baby["kantaasabino"] = $sabino[0];
            $baby["gen_kirj_s"] = $sabino[1];
            
            if (($dad['gen_kirj_spl'] == FALSE) && ($mum['gen_kirj_spl'] == FALSE) && (($dad['merkit'] == TRUE) || ($mum['merkit'] == TRUE))){
                $baby["kantaaspl"] = TRUE;
                $baby["gen_kirj_spl"] = FALSE;
                }
                
            if ($dad['gen_kirj_s'] === FALSE && $mum['gen_kirj_s'] === FALSE && (($dad['merkit'] == TRUE) || ($mum['merkit'] == TRUE))){
                $baby["kantaasabino"] = TRUE;
                $baby["gen_kirj_s"] = FALSE;
                }
            
        
            
            //Muita värejä
            
            //Loput erikoisvärit periytyvät kiltisti
            $varmalla = array();
        $n = count($this->varit);
        for($i=0; $i < $n; $i++)
        {	//Jos väriä ei ole käsitelty, se käsitellään
            if ($this->varit[$i] != "rn" 
            && $this->varit[$i] != "m" 
            && $this->varit[$i] != "rt" 
            && $this->varit[$i] != "gen_vkko"
            && $this->varit[$i] != "gen_tvkko" 
            && $this->varit[$i] != "emtpohja"
            && $this->varit[$i] != "gen_kirj_spl" 
            && $this->varit[$i] != "gen_kirj_s" 
            && $this->varit[$i] != "gen_kirj_tvkko"
            && $this->varit[$i] != "merkit"
            && $this->varit[$i] != "kantaasabino"
            && $this->varit[$i] != "kantaaspl"){
                // haetaan varsalle värin arvo vanhempien mukaan
                $arvo = $this->Periyta($dad[$this->varit[$i]], $mum[$this->varit[$i]]);
                $baby[$this->varit[$i]] = $arvo;
            }
			
        }
    
        return $baby;
    }
    
    public function varmat($baby){
        $varmalla = array();
        $n = count($this->varit);
        for($i=0; $i < $n; $i++)
        {
			//varmat värit omaan tauluunsa
			if ($baby[$this->varit[$i]] === 3){
				$varmalla[] = $this->varit[$i];
			}
		}
        
        return $varmalla;
    }
    

    
    
    function tulostalista ($kelle){
    $p = $this->pohjavarit;
    $e = $this->erikoisvarit;
    $k = $this->kirjavat;
	$muuttuja = str_replace("[]", "pohja", $kelle);
	echo "<b>Pohjav&auml;ri</b> <small>(jokaisella hevosella on yksi n&auml;ist&auml;)</small><br /> ";
		foreach ($p as $key => $nimi) {
			echo '<input type="radio" name="'. $muuttuja . '" value="' . $key . '"  onclick="tulosta_vari(\'' .$muuttuja. '\', \''.$kelle.'\')" /> ' . $nimi . ' ';
		}
	echo "<br /><b>Erikoisv&auml;rit</b> <small>(Voit valita useita)</small><br />  ";
		foreach ($e as $key => $nimi) {

   echo '<input type="checkbox" name="'. $kelle . '" value="' . $key . '"  onclick="tulosta_vari(\'' .$muuttuja. '\', \''.$kelle.'\')" /> ' . $nimi . ' ';
     
		}

	echo "<br /><b>Valkoiset merkit ja kirjavuus:</b> <small>(Voit valita useita)</small><br /> ";
		foreach ($k as $key => $nimi) {
   if($key == "merkit"){
    echo "<br>";
   }
			echo '<input type="checkbox" name="'. $kelle . '" value="' . $key . '" onclick="tulosta_vari (\'' .$muuttuja. '\', \''.$kelle.'\')" /> ' . $nimi . ' ';
   if($key == "gen_kirj"){
    echo "<br>";
   }
		}
		
}

function tulostavarsalista ($b, $kelle){
    $p = $this->pohjavarit;
    $e = $this->erikoisvarit;
    $k = $this->kirjavat;
	$muuttuja = str_replace("[]", "pohja", $kelle);
	echo " <small>(Voit valita useita)</small><br /> ";
		foreach ($e as $key => $nimi) {
			if (($b[$key] == TRUE) && (($b[$key] == '1') || ($b[$key] == '2'))){
			echo '<input type="checkbox" name="'. $kelle . '" value="' . $key . '"  onclick="tulosta_varsavari(\'' .$muuttuja. '\', \''.$kelle.'\')" /> ' . $nimi . ' ';
			}
			else if ($b[$key] == '3') {
			echo '<input type="hidden" name="'. $kelle . '" value="' . $key . '" />'; 
			}
		}

		foreach ($k as $key => $nimi) {
		if (($b[$key] == TRUE) && (($b[$key] == '1') || ($b[$key] == '2'))){
			echo '<input type="checkbox" name="'. $kelle . '" value="' . $key . '" onclick="tulosta_varsavari (\'' .$muuttuja. '\', \''.$kelle.'\')" /> ' . $nimi . ' ';
		}
		else if ($b[$key] == '3') {
			echo '<input type="hidden" name="'. $kelle . '" value="' . $key . '" />'; 
			}
		}
		
		foreach ($p as $key => $nimi) {
		if ($b[$key] == TRUE){
			echo '<input type="hidden" name="'. $muuttuja . '" value="' . $key . '" />';
		}
		}
		
}

function tulostajavascript ($varit, $perusvari){
	// tehään lista väreistä

		$lista = "";
		$turhake = 0;
		foreach ($varit as $i => $value)
		{
			if ($turhake == 0){
				$lista = $lista . "\"";
				$turhake++;
				}
			else {
				$lista = $lista .  ', "'; 
			}
			$lista = $lista . $varit[$i];
			$lista = $lista .  '"';
		}
	//tulostetaan javascript taulukko niistä
	echo "<span id=\"". $perusvari ."kohta\">";
	echo "\n<script type=\"text/javascript\">\n";
	echo "";
	echo "var perusvari = \"". $perusvari . "\";";
	echo "var varilista = new Array (";
	echo $lista;
	echo "); \n";
	echo "var varinimi = muodosta_vari(varilista, perusvari); \n";
	echo "document.write(varinimi);\n";
	echo '</script>';
	echo "</span><br />";
	}
    
               
    
    public function tulosta_varsatulos($baby, $varmalla) {
        
    
     if ($baby["rn"] === TRUE){
		$varmalla['x'] = "rn";
		$this->tulostajavascript($varmalla, "rn");
		unset($varmalla['x']);
	}
	
	if ($baby["rt"] === TRUE){
		$varmalla['x'] = "rt";
		$this->tulostajavascript($varmalla, "rt");
		unset($varmalla['x']);
	}
	if ($baby["m"] === TRUE){
		$varmalla['x'] = "m";
		$this->tulostajavascript($varmalla, "m");
		unset($varmalla['x']);
	}
	
	
	echo "<br>";
	echo "<b>Lis&auml;ksi seuraavat v&auml;rit voivat yhdisty&auml; aiemmin listattuihin</b>:";
	$this->tulostavarsalista($baby, "varsa[]");	
	echo "<br>";
    }
    
    public function tulosta_varsalisatiedot($baby){
        if ($baby["kantaasabino"] == '1' && $baby["kantaaspl"] == '1'){
		echo "<small>Varsan vanhemmilla oli merkkej&auml;, joten jos rodussa esiintyy splashed white-kirjavuutta (esim. russit) tai sabinokirjavuutta (esim. suomenhevoset), on varsalla my&ouml;s pieni mahdollisuus synty&auml; kirjavana.</small>";
	}
	
	else if ($baby["kantaasabino"] != '1' && $baby["kantaaspl"] == '1'){
		echo "<small>Varsan vanhemmilla oli merkkej&ouml;, joten jos rodussa esiintyy splashed white-kirjavuutta (esim. russit), on varsalla my&ouml;s pieni mahdollisuus synty&auml; splashed white kirjavana.</small>";
	}
	
	else if ($baby["kantaasabino"] == '1' && $baby["kantaaspl"] != '1'){
		echo "<br><small>Varsan vanhemmilla oli merkkej&auml;, joten jos rodussa esiintyy sabinokirjavuutta (esim. suomenhevoset), on varsalla my&ouml;s pieni mahdollisuus synty&auml; sabinokirjavana.</small>";
	}
    }



}



