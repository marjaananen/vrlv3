<?php
class Kilpailutoiminta extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
	
        $this->load->model('Jaos_model');
        $this->load->library('Jaos');
        $this->load->library('Porrastetut');


    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
	
	function index ()
    {
        $vars = array();
        $vars['message'] = $this->session->flashdata('message');
        $this->fuel->pages->render('kilpailutoiminta/index', $vars);
    }
	
	function kilpailujaokset ()
    {
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full();     
        $this->fuel->pages->render('jaokset/jaoslista', $vars);
    }
    
    function kilpailusaannot(){
        $vars = array();
        $vars['jaokset'] = $this->Jaos_model->get_jaokset_full(); 
        $vars['jaoskohtaiset'] = $this->load->view('kilpailutoiminta/jaoskohtaiset_rajaukset', $vars, TRUE);
        $this->fuel->pages->render('kilpailutoiminta/kilpailusaannot', $vars);
    }
    
    function porrastetut($sivu = null){
        $vars = array();
        $vars['levels'] = $this->porrastetut->get_levels();
        $vars['traits'] = $this->porrastetut->get_traits();
        $vars['aste'] = $this->porrastetut->get_asteet();
        $vars['jaokset'] = $this->porrastetut->get_all_porrastettu_info();
        
        if($sivu == "luokat"){
           $this->fuel->pages->render('kilpailutoiminta/porrastetut_luokat', $vars); 
        }else if($sivu == "kilpailulistat"){
            if(!($this->ion_auth->logged_in()))
            {
                $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => 'Kirjaudu sisään katsoaksesi kilpailulistaasi!'));
            } else {
                $this->_porrastetut_kisalistat('kilpailutoiminta/porrastetut/kilpailulistat');
            }
        }
        else {
            $this->fuel->pages->render('kilpailutoiminta/porrastetut_saannot', $vars);
        }
    }
    
    
    private function _porrastetut_kisalistat($url){
        $vars['jaokset'] = $this->porrastetut->get_porrastetut_jaokset();
        $full_porrastettu_info = $this->porrastetut->get_all_porrastettu_info();
        
        $this->load->model("Tallit_model");
        $this->load->model("Hevonen_model");
        $this->load->library("Vrl_helper");
        
        $tunnus = $this->ion_auth->user()->row()->tunnus;
        $nick = $this->ion_auth->user()->row()->nimimerkki;
        $sort = array();
        $sort = $this->_read_kisalista_input();

        $vars['tallit']  = $this->Tallit_model->get_users_stables($this->ion_auth->user()->row()->tunnus);
        $vars['rodut'] = $this->Hevonen_model->get_owners_breeds($this->ion_auth->user()->row()->tunnus);
        $vars['form'] = $this->_kisalista_form($sort, $vars['rodut'],$vars['tallit'],$url);

        $empty_trait_list = $this->porrastetut->get_empty_trait_array();
        
        $vars['printArrayWaitlist'] = array();
        $vars['printArrayReadylist'] = array();
        $vars['printArray'] = array();
    
        
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            
            $vars['hevoset'] = array();

            $rotu = null;
            $talli = null;
            $minheight = null;
            if($sort['rotu'] > 0){
                $rotu = $sort['rotu'];
            }if($sort['talli'] != "-1"){
                $talli = $sort['talli'];
            }if($sort['minheight'] > 10){
                $minheight = $sort['minheight'];
            }
            
            $vars['hevoset'] = $this->Hevonen_model->get_owners_porrastettu_horses($this->ion_auth->user()->row()->tunnus, $rotu, $talli,$minheight);
            
            
            

            
            foreach ($vars['hevoset'] as $poni){
                if($poni['porr_kilpailee'] === 0){
                    continue;
                }
                //Haetaan ikä
                $levelByAge = $this->porrastetut->level_by_age($poni);
                $full_traits = $this->porrastetut->get_horses_full_level_list($poni['reknro'], $empty_trait_list, $vars['jaokset']);
                $full_sport_info = $this->Hevonen_model->get_horse_sport_info_by_jaos($poni['reknro']);
                //On ikää kisata, ja säkäkorkeus merkitty
                if( $levelByAge > 2 && $poni['sakakorkeus'] > 10) {
                    
                
                    foreach ($vars['jaokset'] as $jaos){
                        $level = 0;
            
                        if ($sort['painotus'] == 1 && (!isset($poni['painotus']) || $jaos['laji'] != $poni['painotus'])){
                            //echo "Pois painotuksen takia: " . $poni['nimi'] . ": " . $poni['painotus'] ."vs. ". $jaos['laji']."<br>";
                            continue;
                        }
                        
                        //ei tarvitse enää sortata rotujen ja tallien mukaan kun ne ei pääse tänne asti
                                                      
                        $level = $full_traits[$jaos['lyhenne']]['level'];
                        
                        $vh = $this->vrl_helper->get_vh($poni['reknro']);
                        
                        $rajoitus = $full_sport_info[$jaos['id']]['taso_max'] ?? 10;
                
                        //Jos hevonen ei ole tarpeeksi vanha nousemaan tasolta, se ei nouse, vaikka pisteet riittÃ¤isi
                        if ($levelByAge < $level && $level <= $rajoitus){
                            $vars['printArrayWaitlist'][$jaos['lyhenne']][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . " (Odottaa ikääntymistä päästäkseen seuraavalle tasolle vt." . $level .")<br />";
                        }
                                        
                        else if ($level <= $rajoitus){
                            $vars['printArray'][$jaos['lyhenne']][$level][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . "<br />";
                        }
                        
                        else if ($rajoitus > -1){
                            $vars['printArrayReadylist'][$jaos['lyhenne']][] = $nick . " (VRL-" . $tunnus . ") - " . $poni['nimi'] . " " . $vh . " (vt." . $level .")<br />";
                        }

                        
                    }	
                }
                
            }
        }
        
         $this->fuel->pages->render('kilpailutoiminta/porrastetut_kisalistat', $vars);


        
    }
    
    private function _kisalista_form($result, $rodut, $tallit, $url){
        $this->load->library('form_builder', array('submit_value' => 'Suodata'));
      
        $rodut_options = array(-1 =>"Kaikki rodut");
    
        foreach ($rodut as $rotu){
            $rodut_options[$rotu['rotunro']] = $rotu['lyhenne'];
        }
        
        $tallit_options = array(-1 =>"Kaikki tallit");
    
        foreach ($tallit as $talli){
            $tallit_options[$talli['tnro']] = $talli['nimi'];
        }
        
        
        $fields = array();
        $fields['rotu'] = array('type' => 'select', 'options' => $rodut_options, 'value' => $result['rotu'] ?? -1, 'class'=>'form-control');
        $fields['painotus'] = array('type' => 'enum', 'mode' => 'radios',
                                    'options' => array("0"=>"Ei jaottelua painotuksen mukaan", "1"=> "Jaottele painotuksen mukaan"),
                                    'value' => $result['painotus'] ?? 0);
        $fields['talli'] = array('type' => 'select', 'options' => $tallit_options, 'value' => $result['talli'] ?? -1, 'class'=>'form-control');
        $fields['minheight'] = array('label' => 'Minimisäkäkorkeus', 'type' => 'number', 'value' => $result['minheight'] ?? 0, 'class'=>'form-control', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => FALSE, 'decimal' => FALSE);    

  
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($url));
		        
        return $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    
    private function _read_kisalista_input(){
        $sort_values = array();
        $sort_values['rotu'] = -1;
        $sort_values['painotus'] = 0;
        $sort_values['talli'] = -1;
        $sort_values['minheight'] = 10;
        
        if($this->input->post('rotu')){
            $sort_values['rotu'] =  $this->input->post('rotu');
        }
        if($this->input->post('painotus')){
            $sort_values['painotus'] =  $this->input->post('painotus');
        }
        if($this->input->post('talli')){
            $sort_values['talli'] =  $this->input->post('talli');
        }
        if($this->input->post('minheight')){
            $sort_values['minheight'] =  $this->input->post('minheight');
        }
        
        return $sort_values;

    }
    
    
    
    ////vanha kisajärjestelmö
    
    

	
	function getSport ( $id ) {
	
		$getDivisionNum = mysql_query("
								SELECT jaos 
								FROM kisat_kisakalenteri 
								WHERE kisa_id = '".$id."'
								");
					
		$division = mysql_fetch_array($getDivisionNum);
			
		return $division['jaos'];
		
	}
	
	function getDivisionName ( $id ) {
	
		$getDivisionName = mysql_query("
								SELECT lyhenne 
								FROM lista_jaokset 
								WHERE jaos_id = '".$id."'
								");
					
		$division = mysql_fetch_array($getDivisionName);
			
		return strtoupper($division['lyhenne']);
		
	}
	
	/**
		Tutkii, onko henkilöllä aiempia kisoja kys. jaokselta. Palauttaa kisojen määrän.
	*/
	function countPersonRaces($vrl, $division) {
			$getCount = mysql_query("
					SELECT count(kisa_id) AS maara 
					FROM kisat_kisakalenteri 
					WHERE 
						tunnus = '".$vrl."' AND 
						jaos = '".$division."' AND 
						vanha = 0 AND
						hyvaksytty IS NOT NULL AND
						tulokset IS NOT NULL
					");
				
			$count = mysql_fetch_array($getCount);
			
			return $count[0];
		
	}
	
	/**
		Tutkii, onko henkilöllä aiempia kisoja kys. jaokselta. Palauttaa kisojen määrän.
	*/
	function countResultApplications($division, $porrastettu = 0) {
	
		$howMany = mysql_query("
								SELECT count(tulos_id) FROM kisat_tulokset
								WHERE 
										(kisat_tulokset.hyvaksytty IS NULL OR 
										kisat_tulokset.hyvaksytty = '0000-00-00 00:00:00')
									AND
									kisa_id IN
										(
											SELECT kisa_id 
											FROM kisat_kisakalenteri 
											WHERE 
												jaos = ".$division."  AND 
												porrastettu = ".$porrastettu." AND 
												hyvaksytty IS NOT NULL AND (tulokset = 0 OR tulokset IS NULL)
										) 
								") or die("Problem at tulosjonohaku!".mysql_error());
		
		$count = mysql_fetch_array($howMany);	
		return $count[0];
		
	}
	
	/**
		Lisää henkilölle etuuspisteet
		$vrl		VRL-tunnus
		$points		Lisättävät pisteet
		$division	Jaos
	*/
	function updateRacePoints($vrl, $points, $division) {
	
		$getPoints = mysql_query("SELECT tunnus FROM tunnukset_etuuspisteet WHERE tunnus = ".$vrl." LIMIT 0,1");
		
		if( mysql_num_rows($getPoints) > 0 ) {
		
			$newPoints = mysql_query("
					UPDATE tunnukset_etuuspisteet 
					SET ".$division." = ".$division."+".$points."
					WHERE tunnus = ".$vrl.";");	
					
		} else {
			$newPoints = mysql_query("
											INSERT INTO tunnukset_etuuspisteet (".$division.", tunnus) 
											VALUES ('".$points."', ".$vrl.")"
											);
		}

		if($newPoints) {
			print 	'<p class="ok">'.$points.' '.$division.'-etuuspistettä annettiin tulosten lähettäjälle VRL-'.$vrl.'.</p>';
		} else {
			print '<p class="error">Etuuspisteitä ei voitu päivittää!.</p>';
		}	

		return TRUE;		
	}
	
	/**
		Palauttaa etuuspisteet php-arrayna kultakin jaokselta sekä yhteensä.
		$points['JAOS'] = jaoksen etuuspisteet | ERJ, KERJ, KRJ, ARJ, VVJ, WRJ, NJ
		$points['sum']	= etuuspisteet yhteensä
	*/
	function selectLotteryStress($type) {
		if( $type > 0 AND $type < 5 ) {
			switch($type) {
				case 1:
					$stress = 1;
					break;
				case 2:
					$stress = 1.5;
					break;
				case 3:
					$stress = 1.5;
					break;
				case 4:
					$stress = 2;
					break;
			}
		} else {
			$stress = 1;
		}
		
		return $stress;		
	}
	
	/**
		Palauttaa etuuspisteet php-arrayna kultakin jaokselta sekä yhteensä.
		$points['JAOS'] = jaoksen etuuspisteet | ERJ, KERJ, KRJ, ARJ, VVJ, WRJ, NJ
		$points['sum']	= etuuspisteet yhteensä
	*/
	function countRankingPoints($order, $stress) {
		
		if($order <= 3) {
			switch ($order) {
				case 1:
					$points = 6 * $stress;
					break;
				case 2:
					$points = 4 * $stress;
					break;
				case 3:
					$points = 2 * $stress;
					break;	
			}
		} elseif ($order >= 3 AND $order <= 10) {
			$points = 1 * $stress;
		} else {
			$points = 0;
		}
		
		return $points;		
	}
	
	/**
		Lisää rankinpisteet tietokantaan.
		$vrl		VRL-tunnus
		$points		Lisättävät pisteet
		$division	Jaos
	*/
	function addRankingPoints($vrl, $points, $division) {
		
		$year = date("y");
		$addPoints = mysql_query("
									INSERT INTO ranking_".$year." (tunnus, ".$division.") 
									VALUES (".$vrl.", +".$points.") 
									ON DUPLICATE KEY UPDATE ".$division." = ".$division."+".$points
								);
		
		if($addPoints) {
			// print 'Lisättiin ranking-pisteitä kantaan: '.$points.', tunnukselle: '.$vrl;
			return TRUE;
		} else {
			// print 'Ei lisätty ranking-pisteitä kantaan: '.$points.', tunnukselle: '.$vrl;
			return FALSE;
		}
	}
	
############### PORRASTETUT ALKAVAT TÄSTÄ
###############


/* Tarkistaa tietokannasta, mitä "porrastettu"-kenttään on laitettu. 1 = porrastettu, 2 = ei porrastettu */
function Leveled ( $id  ) {

	// Hae kilpailu tietokannasta
	$getLeveled = mysql_query("
				SELECT porrastettu
				FROM kisat_kisakalenteri
				WHERE kisa_id = '$id'
				") or die ( mysql_error() );
	
	$queryResult = mysql_fetch_assoc($getLeveled);
	$result = $queryResult['porrastettu']; 
	
	return $result;
}

/*  */
function getPropertiesSport ( $sport  ) {

	// 1. Hae tietokannasta lajin $sport tiedot
	$getSport = mysql_query("
				SELECT lista_ominaisuudet.alias
				FROM lista_ominaisuudet
				LEFT JOIN lista_vaikutukset
					ON lista_ominaisuudet.id = lista_vaikutukset.ominaisuus
				WHERE lista_vaikutukset.laji = $sport
				") or die ( mysql_error() ); // nrolla 3: kuuliaisuus_luonne, tahti_irtonaisuus

	$ominaisuudet = array();
	
	// 2. Työnnä ominaisuuksien aliakset taulukkoon, jotta voidaan käyttää 3. vaiheessa hyväksi
	while ( $ominaisuus = mysql_fetch_array($getSport, MYSQL_ASSOC) ) {
		array_push($ominaisuudet, $ominaisuus['alias']);
	}
	
	return $ominaisuudet;
}


/* Tarkasta hevosen ominaisuuspisteet lajissa */
/* checkPropertyPoints( '000350006', 3 )  */
function checkPropertyPoints( $vh, $sport ) {
	// $sport = nro, esim. "1"

	// 1. Hae tietokannasta lajin $sport tiedot
	// Haetaan lajin ID
	$getSport = mysql_query("
				SELECT lista_ominaisuudet.alias
				FROM lista_ominaisuudet
				LEFT JOIN lista_vaikutukset
					ON lista_ominaisuudet.id = lista_vaikutukset.ominaisuus
				WHERE lista_vaikutukset.laji = $sport
				") or die ( mysql_error() ); // nrolla 3: kuuliaisuus_luonne, tahti_irtonaisuus

	$ominaisuudet = array();
	
	// 2. Työnnä ominaisuuksien aliakset taulukkoon, jotta voidaan käyttää 3. vaiheessa hyväksi
	while ( $ominaisuus = mysql_fetch_array($getSport, MYSQL_ASSOC) ) {
		array_push($ominaisuudet, $ominaisuus['alias']);
	}
	
	// Siistitään VH
	$vh = clearVh( $vh );
			
	// 3. Tarkista, paljonko hevosella on ominaisuuksissa pisteitä
	$getPoints = mysql_query("
		SELECT ".implode(", ", $ominaisuudet)."
		FROM hevosrekisteri_ominaisuudet										
		WHERE reknro = '$vh'
		") or die ( "" );  
		
	$points = mysql_fetch_assoc($getPoints);

	$properties[0] = $points[$ominaisuudet[0]];
	$properties[1] = $points[$ominaisuudet[1]];
	
	if( empty ($properties[0]) ) { $properties[0] = 0; }
	if( empty ($properties[1]) ) { $properties[1] = 0; }
	
	// 4. Palauta hevosen ominaisuuspisteet taulukossa
	return $properties;
}

function checkHeight( $vh ) {
	$vh = clearVh( $vh );	
	
	$getHeight = mysql_query("
						SELECT sakakorkeus
						FROM hevosrekisteri_perustiedot
						WHERE reknro = $vh
						LIMIT 0,1
						");
	$gotHeight = mysql_fetch_assoc($getHeight);

	if ( empty ($gotHeight['sakakorkeus']) ) {
		$height = 0;
	} else {
		$height = $gotHeight['sakakorkeus'];
	}
	
	return $height;
}

function checkHorseInfo( $vh ) {
	$vh = clearVh( $vh );	
	
	$getInfo = mysql_query("
						SELECT sakakorkeus, rotu
						FROM hevosrekisteri_perustiedot
						WHERE reknro = $vh
						LIMIT 0,1
						");
	$information = mysql_fetch_assoc($getInfo);

	if ( empty ($information['sakakorkeus']) ) {
		$height = 0;
	} else {
		$height = $information['sakakorkeus'];
	}
	if ( empty ($information['rotu']) ) {
		$breed = 0;
	} else {
		$breed = $information['rotu'];
	}
	
	$information['breed'] = $breed;
	$information['height'] = $height;
	
	return $information;
}


	
	
/* Tarkastaa, mille tasolle hevonen kuuluu lajissa $sport */
function checkLevel ( $vh, $sport ) {

	// 1. Tarkista ikä
	$age = checkAge( $vh );
	// print '-'.$age.'-';
	
	// 2. Tarkista ominaisuuspisteet lajista
	$propertypoints = checkPropertyPoints( $vh, $sport );
	
	// 3. Laske ominaisuuspisteet yhteen
	$properties = $propertypoints[0] + $propertypoints[1];
	
	$levelByAge = levelByAge($age);
	$levelByProperties = levelByProperties($properties);
	
	//Oletus, että taso on se, mihin ominaisuuspisteillä päästään.
	
	$level = $levelByProperties;
	
	//Jos hevonen ei ole tarpeeksi vanha nousemaan tasolta, se ei nouse, vaikka pisteet riittäisi
	if ($levelByAge < $levelByProperties){
		$level = $levelByAge;
	}
	
	//Jos hevonen ei ole kisaikäinen
	if ($level == -1){
		$level = 'Hevonen on liian nuori kilpailemaan ('.$age.' vuotta)';
	}
	
	$information['points'] = $properties;
	$information['level'] = $level;
	
	return $information;
	
}



function levelByProperties ( $properties ){	
	if ( properties >= 0 AND $properties < 201) {
		$level = 0; 
	} elseif ( $properties >= 201 AND $properties < 601 ) {
		$level = 1; 
	} elseif ($properties >= 601 AND $properties < 1001) {
		$level = 2; 
	} elseif ( $properties >= 1001 AND $properties < 1401) {
		$level = 3; 
	} elseif ($properties >= 1401 AND $properties < 1801 ) {
		$level = 4;
	} elseif ($properties >= 1801 AND $properties < 2401) {
		$level = 5; 
	} elseif ($properties >= 2401 AND $properties < 3001 ) {
		$level = 6; 
	} elseif ($properties >= 3001 AND $properties < 3801 ) {
		$level = 7; 
	} elseif ($properties >= 3801 AND $properties < 4601) {
		$level = 8;
	} elseif ( $properties >= 4601 AND $properties < 5601 ) {
		$level = 9; 
	} elseif ($properties >= 6501) {
		$level = 10; 
	} else {
		$level = 11;	
	}
	
	return $level;
	
}

function allClasses ( $sport ) {

	$getClasses = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE laji = '$sport'
						ORDER BY taso ASC
						");
						
						
	$classes = array();
	$counter = 0;
	
	while($dbclass = mysql_fetch_assoc($getClasses)) {
		$classes[$counter]['id'] = $dbclass['id'];
		$classes[$counter]['nimi'] = $dbclass['nimi'];
		$classes[$counter]['taso'] = $dbclass['taso'];
		$counter++;
		
	}
						
	return $classes;
}

function getClassInfo ( $class ) {

	$getClass = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE id = '$class' OR nimi = '$class'
						");
						
						
	$class = array();
	$counter = 0;
	
	while($dbclass = mysql_fetch_assoc($getClass)) {
		$class[$counter]['id'] = $dbclass['id'];
		$class[$counter]['nimi'] = $dbclass['nimi'];
		$class[$counter]['taso'] = $dbclass['taso'];
		$class[$counter]['aste'] = $dbclass['aste'];
		$class[$counter]['minheight'] = $dbclass['minheight'];
		$counter++;
		
	}

	return $class;
}

function classesToPart ( $level, $sport, $height ) {

	if( $level == 1 ) {
		$tasot = 'taso = 0 OR taso = 1 OR taso = 2';
	} elseif ( $level == 2 ) {
		$tasot = 'taso = 1 OR taso = 2 OR taso = 3';
	} else {
		$tasot = 'taso = '.$level.' OR taso = '.($level+1);
	}

	$getClasses = mysql_query("
						SELECT *
						FROM lista_luokat
						WHERE 
							laji = $sport AND
							($tasot) AND
							(
								minheight IS NULL OR 
								minheight <= $height
							) 
						");
	$classes = array();
	
	while ( $class = mysql_fetch_array($getClasses, MYSQL_ASSOC) ) {
		$handled = $class['nimi'].', taso '.$class['taso'];
		
		if ( $class['minheight'] != NULL ) {
			$handled .= ' (sk. '.$class['minheight'].'cm )';
		}
		
		array_push($classes, $handled);
	}
	return $classes;
}

function generateResults ( $participants, $class, $sport  ) {
	
	$participants = array_diff( $participants, array('') );
	
	// print_r( $participants ); print '<hr />';
	
	for ($i = 0; $i < count($participants); $i++) {
		$horse = $participants[$i];
		
		// 1. Tarkistetaan, onko rivillä VH-tunnusta
		if( strpos( strtoupper($participants[$i]), "VH") !== FALSE ) {
			// 1.0 Otetaan VH-tunnus talteen ja jatketaan
			$vh = substr($participants[$i], strpos($participants[$i], "VH"),13);
			$vh = str_replace('-', '', $vh);
			$vh = substr( $vh, (-9) );
			
			// 1.1. Tarkista hevosen ikä ja millä tasolla hevonen on 
			$age = checkAge( $vh ); 
			$horselevel = checkLevel ( $vh, $sport );
			
			// 1.2. Tarkasta hevosen säkäkorkeus ja rotu
			// $height = checkHeight( $vh );
			$information = checkHorseInfo( $vh );
			$height = $information['height'];
			$breed = $information['breed'];
			
			// HAE MINIMI-IKÄ JA SÄKÄ, MILLÄ HEVONEN VOI OSALLISTUA
			// $class = luokan ID
			$classinfo = getClassInfo ( $class );
			
			if ( $age >= 3 ) {
				// Hevonen saa kilpailla vaan omalla tasollaan ja yhtä ylemmällä tasolla. 			
				// Jos hevosen taso on 2 ja luokan taso 0
				// Jos hevosen taso on sama kuin luokan taso
				// Jos hevosen taso on yhtä isompi kuin luokan taso | hevonen lv 2 voi osallistua lk lv3
				if ( 
					( $horselevel['level'] == 1 && $classinfo[0]['taso'] == 0 ) OR
					( $horselevel['level'] == 2 && $classinfo[0]['taso'] == 1 ) OR
					$horselevel['level'] == $classinfo[0]['taso'] OR 
					($horselevel['level']+1) == $classinfo[0]['taso']
					) {
					
					/*
						horselevel['level']== 2 && clasinfo = 0
					*/
					
					// print $classinfo[0]['taso'].'vs'.$horselevel['level'];
				
					if ( $height >= $classinfo[0]['minheight'] ) {
					
						if ( $classinfo[0]['nimi'] == 'CIC2, avoin - ei sh (kansallinen)' AND $breed == '018' ) {
							$failed[] = array('horse' => $horse, 'reason' => 'Suomenhevonen ei voi osallistua tähän luokkaan');
							
						} else {
						
							// print $classinfo[0]['minheight'].'vs'.$height;
						
							// 1.3. Hae hevosen ominaisuuspisteet
							$propertypoints = checkPropertyPoints( $vh, $sport ); // Hae ominaisuudet
							$horsePropertyPoints = $propertypoints[0] + $propertypoints[1]; // Laske ominaisuuspisteet yhteen
							
							// 1.4. Laske hevoselle pistemäärä ja lisää se taulukkoon $accepted
							
							
							$pointsInClass = ( $horsePropertyPoints / 3 )+(rand(0,100)/100) * ( (rand(0,100)/100) + (rand(0,100)/100) + (rand(0,100)/100) + 1.00);
							$accepted[] = array('vh' => $vh, 'horse' => $participants[$i], 'points' => $pointsInClass);
						}
						
					} else {
						$failed[] = array('horse' => $horse, 'reason' => 'Hevosella ei ole riittävää säkäkorkeutta');
					}
					
				} else {
					$failed[] = array('horse' => $horse, 'reason' => 'Hevosen taso ei ole riittävä tai se on liian korkea: lk. '.$classinfo[0]['taso'].' vs. hevonen '.$horselevel['level']);
				}
			} else {
				$failed[] = array('horse' => $horse, 'reason' => 'Hevonen on liian nuori kilpailemaan');
			}
			
			
		} else {
			if ( !empty($horse) AND $horse != '' ) {
				$failed[] = array('horse' => $horse, 'reason' => 'Hevosella ei ole VH-tunnusta');
			} else {
				print '';
			}
		}
	}
	
	// 2.0 Järjestä kaikki osallistujat taulukossa $accepted
	foreach ($accepted as $key => $row) {
		$points[$key] = $row['points'];
		$vhIdentification[$key]  = $row['vh'];
	}
	
	array_multisort($points, SORT_DESC, $vhIdentification, SORT_DESC, $accepted);
	
	// print_r($accepted);
	
	// 2.5 Järjestä failed-taulussa kaikki osallistujat
	foreach ($failed as $key => $row) {
		$horseIdentification[$key]  = $row['horse'];
		$reason[$key] = $row['reason'];
	}
	
	array_multisort($horseIdentification, SORT_DESC, $reason, SORT_DESC, $failed);
	
	print '<hr />';
	
	$results = array($accepted, $failed);
	
	// 3. Palauta $results
	return $results;
}
	
/* Lisää hevoselle ominaisuuspisteet */
function addPropertyPoints ( $vh, $classinfo ) {
	//	$vh = hevosen VH-tunnus | $classinfo = taulukko luokan tiedoille
	//		$classinfo['participants']	Osallistujien yhteismäärä
	//		$classinfo['rank']			Hevosen sijoitus
	//		$classinfo['difficulty']	Luokan vaikeustaso 0-10
	//		$classinfo['property1']		Kerrytettävä ominaisuus 1
	//		$classinfo['property2']		Kerrytettävä ominaisuus 2
	
	$vh = clearVh( $vh );	
	
	// 1. Etsi hevonen tietokannasta, tarkista että on olemassa
	$getHorse = mysql_query("SELECT * FROM hevosrekisteri_perustiedot WHERE reknro = ".$vh);
	
	if ( mysql_num_rows ( $getHorse ) < 0 ) {
		return FALSE;
	}

	// 2. Laske  annettavat ominaisuuspisteet
	
	if ( $classinfo['difficulty'] >= 0 AND $classinfo['difficulty'] <= 3 ) {
		// Tasot 0-3
		$max = 0.5; // max. 15p
	
	} elseif ( $classinfo['difficulty'] >= 4 AND $classinfo['difficulty'] <= 6 ) {
		// Tasot 4-6
		$max = 1; // max. 20p
	
	} elseif ( $classinfo['difficulty'] >= 7 AND $classinfo['difficulty'] < 9 ) {
		// Tasot 7-9
		$max = 1.5; // max. 25p
	
	} else {
		// Tasot 10-
		$max = 2; // max. 30p
	
	}
	
	$points = 100 / $classinfo['participants'] * ( ($classinfo['participants'] -  $classinfo['rank'] + 0.4 ) / 10);
	$points = ($points * ( 1 + $max / $classinfo['rank'] ) );
	$points =  $points - ( $points / 10 );
	$points =  round($points, 2);
	
	//Taikakerroin on kerroin jolla säädellään porrastettujen pistesaantitasoa kulloisenkin virtuaalimaailmantilanteen mukaan.
	$taikakerroin = 8.7;
							
	$points =  $taikakerroin * $points;
	
	// 3. Jaa ominaisuuspisteet ominaisuuksien kesken
	$percent = rand (15, 75 ); // 34
	$property1 = ($percent/100) * $points; // (0,34) * $points
	$property2 = (1 - $percent/100) * $points; // (1-0,34) * $points
	$properties = round($property1,2).'p. / '.round($property2,2).'p. ';
	
	// 4. Lisää ominaisuuspisteet tietokantaan
	$addPoints = mysql_query("
							INSERT INTO hevosrekisteri_ominaisuudet 
							(reknro, ".$classinfo['property1'].", ".$classinfo['property2'].") VALUES ($vh, '".$property1."', '".$property2."') 
							ON DUPLICATE KEY UPDATE 
								".$classinfo['property1']." = ".$classinfo['property1']."+'".$property1."', 
								".$classinfo['property2']." = ".$classinfo['property2']."+'".$property2."';
							");
							
	// print ' --- '.$property1."+".$property2." = ".$points." <br />";
	
	// 5. Return false/true	
	if ( !$addPoints ) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function checkAllLevels ( $vh ) {

	$sports = array (1,2);
	$levels = array();
	
	$height = checkHeight( $vh );
			
	if ( $height > 0 ) {

		foreach ($sports as &$sportnumber) {
		
			$checklevel =  checkLevel( $vh, $sportnumber );
			
			if( $checklevel['level'] != (-1) ) {
				$levels = 'laji'.$sportnumber.': taso '.$checklevel['level'];
				array_push ($result, $levels);
				
			} else {
			
			}
		}
		
		if( $checklevel['level'] == (-1) ) {
			print '<span style="font-size: 90%; color: ##E8E8E8;">Hevonen on liian nuori kilpailemaan tai siltä puuttuu 3-vuotispäivämäärä profiilistaan.</span>';
		}
		
	} else {
		print '<span style="font-size: 90%; color: ##E8E8E8;">Hevoselta puuttuu säkäkorkeus profiilista eikä se voi kilpailla ennen sen lisäämistä.</span>';
	}
	
	
	return $result;

}
   
}
?>






