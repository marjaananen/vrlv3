<?php
class Rajapinta extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->hevonen_url = site_url() . "virtuaalihevoset/hevonen/";
        $this->bis_url = site_url() . "kilpailutoiminta/tulosarkisto/bis/";
        $this->result_url = site_url(). "kilpailutoiminta/tulosarkisto/tulos/";
        $this->load->library("vrl_helper");
        $this->load->library("porrastetut");

    }
    
    function index ()
    {
        $vars = array();
        
        $vars['virhekoodit'] = $this->virhekoodit;
        $vars['esimerkit'] = array();
        $vars['esimerkit']['error'] = json_encode($this->_virhetaulu("Virhekoodi numerona", "Virheen sanallinen kuvaus"));
        $vars['esimerkit']['success'] = json_encode(array_merge($this->_base_array(), array("rajapinnan nimi"=>array("rajapinnan", "palauttama", "sisalto", "taulukkona"))));
        $vars['rajapinnat'] = array();
        $vars['rajapinnat']['varsat'] = array("parametrit"=>array("vh-tunnus"),
                                              "esimerkki"=>array("VH03-028-8756"),
                                              "esimerkkikoodi" => "https://github.com/marjaananen/vrlv3/blob/master/esimerkit/rajapinta/varsat.php",
                                              "kuvaus" => "Tällä komennolla voit hakea valitsemasi hevosen jälkeläiset.");
        $vars['rajapinnat']['tulos_id'] = array("parametrit"=>array("kisan id"),
                                              "esimerkki"=>array(153969),
                                              "kuvaus" => "Tällä komennolla saat valitsemasi kilpailun tulos-id:n ja tulosarkistosivun osoitteen");
        $vars['rajapinnat']['nayttelytulos_id'] = array("parametrit"=>array("näyttelyn id"),
                                              "esimerkki"=>array(3240),
                                              "kuvaus" => "Tällä komennolla saat valitsemasi kilpailun tulos-id:n ja tulosarkistosivun osoitteen");
        $vars['rajapinnat']['ominaisuudet'] = array("parametrit"=>array("vh-tunnus"),
                                              "esimerkki"=>array("VH03-028-8756"),
                                              "kuvaus" => "Tällä komennolla voit hakea valitsemasi hevosen porrastettujen ominaisuuspisteet ja tason.
                                              <b>HUOM!</b> tämä on vanhentunut, ja käsittää vain neljä alkuperäistä porrastettujen jaosta. Toimii samalla
                                              tavalla kuin vanha \"ominaisuudet\" rajapinta, mutta vanha rakenne on nyt uuden rajapintarakenteen mukaisesti
                                              \"ominaisuudet\" kentässä. Virhekoodit vastaavat vanhaa, eivätkä uutta rajapintaa. Älä ota tätä käyttöön mikäli
                                              teet sivuillesi uutta toteutusta! Käytä \"porrastetut\"-rajapintaa tämän sijaan. ");
        $vars['rajapinnat']['porrastetut'] = array("parametrit"=>array("vh-tunnus"),
                                              "esimerkki"=>array("VH03-028-8756"),
                                              "esimerkkikoodi" => "https://github.com/marjaananen/vrlv3/blob/master/esimerkit/rajapinta/porrastetut.php",
                                              "kuvaus" => "Tällä komennolla voit hakea valitsemasi hevosen porrastettujen ominaisuuspisteet ja tason kaikissa
                                              jaoksissa ja ominaisuuksissa. Koska jaoksia ja ominaisuuksia voi tulla lisää, rajapinta palauttaa myös tiedot jaoksista ja ominaisuuksista.
                                              Rajapinta palauttaa myös hevosen ikä- ja säkäkorkeustiedot sekä VRL:n sivuilla asetetun maksimitason. 
                                              ");
        $vars['rajapinnat']['tallinkasvatit'] = array("parametrit"=>array("tallitunnus", "rotunro (ei pakollinen)"),
                                              "esimerkki"=>array("KARK4835", "28"),
                                              "kuvaus" => "Haetaan tietyn tallin kasvatit, parametrina tallin tallitunnus, ja halutessaan valitun rodun id.
                                              Rodun id:n antaminen ei ole pakollista. Ilman ID:tä haetaan kaikenrotuiset kasvatit.");
        $vars['rajapinnat']['nimenkasvatit'] = array("parametrit"=>array("kasvattajanimi_id", "rotunro (ei pakollinen)"),
                                              "esimerkki"=>array("2047", "28"),
                                              "kuvaus" => "Haetaan tietyn kasvattajanimen kasvatit, parametrina kasvattajanimen id, ja halutessaan valitun rodun id.
                                              Rodun id:n antaminen ei ole pakollista. Ilman ID:tä haetaan kaikenrotuiset kasvatit.");
        
        
        
        
        
        $this->fuel->pages->render('rajapinta/index', $vars);
    }
    
    var $virhekoodit = array ("400" => "Antamasi parametri (esim. vh-tunnus tai kilpailun id) on virheellinen",
                              "404" => "Antamillasi parametreilla (esim. vh-tunnus tai kilpailun id) ei löydy tulosta",
                              "600" => "Porrastetut: Hevonen liian nuori tai siltä puuttuu ikä.",
                              "601" => "Porrastetut: Hevonen liian matala tai siltä puuttuu säkäkorkeus.",
                              "800" => "Tapahtui odottamaton virhe. Ota yhteys ylläpitoon.");
    
    private function _print($array = array()){
        if(sizeof($array) == 0){
            $array = $this->_virhetaulu(800, $this->virhekoodit[800] );
        }
        
        $this->load->view('rajapinta/json', array('json'=>json_encode($array)));
        
    }
    
    
    //valmiit perusrakenteet
    private function _virhetaulu($koodi, $teksti){
        
        return array ("error"=> 1, "error_code"=> $koodi, "error_description"=> $teksti);
    }
    
    private function _base_array(){
        return array ("error"=>0);
    }
    
    //muuttujat osoitteille
    var $hevonen_url;
	var $bis_url;
    var $result_url;

    
    function varsat ($vh){
        $data = $this->_base_array();
        if(!$this->vrl_helper->check_vh_syntax($vh)){
            $data = $this->_virhetaulu(400, "Virheellinen vh-tunnus");
        }else {
            $data['varsat'] = $this->_get_horses_foals($vh);
        }

        $this->_print($data);        
    }
    
    function tallinkasvatit ($tnro, $rotunro = null){
        $data = $this->_base_array();

        $data['tallinkasvatit'] = $this->_get_stables_foals($tnro, $rotunro);

        $this->_print($data);        
    }
    
    private function _get_stables_foals($tnro, $rotunro = null){
    
    
        $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.kasvattajanimi, h.kasvattajanimi_id, h.vari, v.lyhenne as varilyhenne, sukupuoli, syntymaaika, url, i_nro, e_nro");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');
        if(isset($rotunro)){
            $this->db->where('h.rotu', $rotunro);
        }
        $this->db->where('h.kasvattaja_talli', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $return_array = array();
            
            foreach($query->result_array() as $rivi){
                $rivi['reknro'] = $this->vrl_helper->get_vh($rivi['reknro']);
                $rivi['i_nro'] = $this->vrl_helper->get_vh($rivi['i_nro']);
                $rivi['e_nro'] = $this->vrl_helper->get_vh($rivi['e_nro']);
                $rivi['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($rivi['syntymaaika']);
                
                //haetaan vanhemmat
                if(isset($rivi['i_nro'])){
                    $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne, url");
                    $this->db->from('vrlv3_hevosrekisteri as h');
                    $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
                    $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
                    $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer'); 
                    $this->db->where('h.reknro', $this->vrl_helper->vh_to_number($rivi['i_nro']));
                    $i_query = $this->db->get();
                    
                    if ($i_query->num_rows() > 0){
                        $isa = $i_query->result_array()[0];                  
                        $isa['reknro'] = $this->vrl_helper->get_vh($isa['reknro']);
                        $rivi['i'] = $isa;
                    }
                }
                
                if(isset($rivi['e_nro'])){
                    $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne, url");
                    $this->db->from('vrlv3_hevosrekisteri as h');
                    $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
                    $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
                    $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer'); 
                    $this->db->where('h.reknro', $this->vrl_helper->vh_to_number($rivi['e_nro']));
                    $e_query = $this->db->get();
                    
                    if ($e_query->num_rows() > 0){
                        $ema = $e_query->result_array()[0];                  
                        $ema['reknro'] = $this->vrl_helper->get_vh($ema['reknro']);
                        $rivi['e'] = $ema;
                    }
                }
                

                $return_array[] = $rivi;
            }
            return $return_array; 
        }
        
        return array();
    }
    
    
    function nimenkasvatit ($id, $rotunro = null){
        $data = $this->_base_array();

        $data['nimenkasvatit'] = $this->_get_names_foals($id, $rotunro);

        $this->_print($data);        
    }
    
    private function _get_names_foals($id, $rotunro = null){
    
    
        $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.kasvattaja_talli, h.kasvattajanimi, h.kasvattajanimi_id, h.vari, v.lyhenne as varilyhenne, sukupuoli, syntymaaika, url, i_nro, e_nro");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');
        if(isset($rotunro)){
            $this->db->where('h.rotu', $rotunro);
        }
        $this->db->where('h.kasvattajanimi_id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $return_array = array();
            
            foreach($query->result_array() as $rivi){
                $rivi['reknro'] = $this->vrl_helper->get_vh($rivi['reknro']);
                $rivi['i_nro'] = $this->vrl_helper->get_vh($rivi['i_nro']);
                $rivi['e_nro'] = $this->vrl_helper->get_vh($rivi['e_nro']);
                $rivi['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($rivi['syntymaaika']);
                
                //haetaan vanhemmat
                if(isset($rivi['i_nro'])){
                    $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne, url");
                    $this->db->from('vrlv3_hevosrekisteri as h');
                    $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
                    $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
                    $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer'); 
                    $this->db->where('h.reknro', $this->vrl_helper->vh_to_number($rivi['i_nro']));
                    $i_query = $this->db->get();
                    
                    if ($i_query->num_rows() > 0){
                        $isa = $i_query->result_array()[0];                  
                        $isa['reknro'] = $this->vrl_helper->get_vh($isa['reknro']);
                        $rivi['i'] = $isa;
                    }
                }
                
                if(isset($rivi['e_nro'])){
                    $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne, url");
                    $this->db->from('vrlv3_hevosrekisteri as h');
                    $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
                    $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
                    $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer'); 
                    $this->db->where('h.reknro', $this->vrl_helper->vh_to_number($rivi['e_nro']));
                    $e_query = $this->db->get();
                    
                    if ($e_query->num_rows() > 0){
                        $ema = $e_query->result_array()[0];                  
                        $ema['reknro'] = $this->vrl_helper->get_vh($ema['reknro']);
                        $rivi['e'] = $ema;
                    }
                }
                

                $return_array[] = $rivi;
            }
            return $return_array; 
        }
        
        return array();
    }
    
    
    function tulos_id($kisa_id){
        $this->_tulos_id_haku($kisa_id, "tulos_id", false);
    }
    
    function nayttelytulos_id($nayttely_id){
        $this->_tulos_id_haku($nayttely_id, "nayttelytulos_id", true);
    }
    
    private function _tulos_id_haku($id, $name, $nayttelyt){
        if (strpos($id,".") === false AND strpos($id,",") === false AND strlen($id) < 12 AND is_numeric ( $id )){
            $data = $this->_base_array();
            $tulos_id = 0;
            $url = "";
            if($nayttelyt){
                $tulos_id = $this->_get_show_result_id($id);
                $url = $this->bis_url;

            } else {
                $tulos_id = $this->_get_comp_result_id($id);
                $url = $this->result_url;
            }

            if($tulos_id > 0){
                $data[$name]['id'] = $tulos_id;
                $data[$name]['url'] = $url . $tulos_id;
                $this->_print($data);
            }else {
                $this->_print($this->_virhetaulu(404, "Antamallasi id:llä ei löydy tuloksia"));

            }
        }
        
        else {
            $this->_print($this->_virhetaulu(400, "Virheellinen kisa-id"));

            
            
        }
    }
    
    function porrastetut($vh){
        $data = $this->_base_array();
        $return_data = array();
        
        if(!$this->vrl_helper->check_vh_syntax($vh)){
            $data = $this->_virhetaulu(400, "Virheellinen vh-tunnus");
        }else {
            $vh = $this->vrl_helper->vh_to_number($vh);
            $horse_info = $this->_get_horses_leveled_info($vh);
            $horse = $horse_info[0];
            if (sizeof($horse_info) == 0){
              $data = $this->_virhetaulu(404, "VH-tunnukselta ei löytynyt tietoja");  
            }
                
            else {
                $ominaisuudet_id_name = array();
                $jaokset_id_name = array();
                $ominaisuus_per_jaos = array();
                $maxs = array ();
                $this->_get_all_traits($ominaisuudet_id_name, $ominaisuus_per_jaos, $jaokset_id_name);
                $this->load->library("age_calc");
                $age = $this->porrastetut->calculate_age($horse);
                $levelByAge =  $this->porrastetut->level_by_age($age);
                $compare_day = date("Y-m-d");
                $ikaantyminen = $horse['ikaantyminen_d'] ?? 0;
                $calculated_age = 0;
                if($ikaantyminen > 0){
                     $calculated_age = $this->age_calc->calculateAge ($horse['syntymaaika'],  $ikaantyminen, $compare_day);
                }
                        
                
                //kaikki ominaisuudet ja jaokset listaan
                $return_data['info'] = array();
                $return_data['info']['ominaisuudet'] = $ominaisuudet_id_name;
                $return_data['info']['jaokset'] = $jaokset_id_name;
                
                //hevosen tiedot
                $return_data['hevonen'] = array();
                $return_data['hevonen']['info'] = array();
                $return_data['hevonen']['ominaisuudet'] = array();
                $return_data['hevonen']['tasot'] = array();
                $return_data['hevonen']['error'] = 0;
                
                if( $levelByAge == -1 ) {
                    $return_data['hevonen']['error'] = 1;
                    $return_data['hevonen']['error_code'] = 600;
                    $return_data['hevonen']['error_message'] ='Hevonen on liian nuori kilpailemaan tai siltä puuttuu 3-vuotispäivämäärä profiilistaan.';
                }
                else if(!isset($horse['sakakorkeus']) || $horse['sakakorkeus'] == null || $horse['sakakorkeus'] == 0 || $horse['sakakorkeus'] < 50){
                    $return_data['hevonen']['error'] = 1;
                    $return_data['hevonen']['error_code'] = 601;
                    $return_data['hevonen']['error_message']  = 'Hevoselta puuttuu säkäkorkeus profiilista!';
                } else {
                    $maxs = $this->_get_horse_max_levels($vh);
                    $return_data['hevonen']['info']['max_taso_per_ika'] = $levelByAge;
                    $return_data['hevonen']['info']['sakakorkeus'] = $horse['sakakorkeus'];
                    $return_data['hevonen']['info']['ika'] = array("syntymaaika"=>$horse['syntymaaika'],
                                                                   "3vuotta"=>$horse['3vuotta'],
                                                                   "4vuotta"=>$horse['4vuotta'],
                                                                   "5vuotta"=>$horse['5vuotta'],
                                                                   "6vuotta"=>$horse['6vuotta'],
                                                                   "7vuotta"=>$horse['7vuotta'],
                                                                   "8vuotta"=>$horse['8vuotta'],
                                                                   "ika"=> $age,
                                                                   "ikaantyminen_paivaa"=> $ikaantyminen,
                                                                   "ikaantyminen_ika"=>$calculated_age);
                    

                }
                
                
                //jaosten ominaisuudet jaokselle ja hevoselle
                foreach ($return_data['info']['jaokset'] as $id=>$jaos){
                    $return_data['info']['jaokset'][$id]['ominaisuudet'] = $ominaisuus_per_jaos[$id];
                    $points = $this->_parse_leveled_info_skills_sum($horse_info, $ominaisuus_per_jaos[$id]);

                    if( $return_data['hevonen']['error']  == 0){
                        $level_per_points = $this->porrastetut->get_level_by_points($points);
                        $level_per_age = $return_data['hevonen']['info']['max_taso_per_ika'];
                        $return_data['hevonen']['tasot'][$id] = array("jaos"=>$id,
                                                               "pisteet" => $points,
                                                               "taso" => min($level_per_age, $level_per_points),
                                                                "max_taso_per_pisteet" => $level_per_points,             
                                                                "taso_rajoitus" => $maxs[$id] ?? 10);
                    }else {
                        $return_data['hevonen']['tasot'][$id] = array("jaos"=>$id,
                                                                "pisteet" => $points,
                                                                "taso" => 0,
                                                                "max_taso_per_pisteet" => 0,             
                                                                "taso_rajoitus" => 0);
                    }
                }
                
                foreach ($return_data['info']['ominaisuudet'] as $id => $ominaisuus){
                    $return_data['hevonen']['ominaisuudet'][$id] = array("ominaisuus"=>$id,
                                                              "pisteet" => $this->_parse_leveled_info_skills_sum($horse_info, array($id)));
                }
                
                        
                
                  $data['porrastetut'] = $return_data;
                       
                        
            }            
        }
            //Heitetään data luettavaksi	
            $this->_print($data);
    }
    
    
    function ominaisuudet($vh){

        $return_data = array();
        $return_data['points'] = array();
        $return_data['error'] = 0;
        $return_data['error_code'] = 0;
        $return_data['error_message'] = "";
        if(!$this->vrl_helper->check_vh_syntax($vh)){
            $return_data = $this->_virhetaulu(400, "Virheellinen vh-tunnus");
        }else {
            $vh = $this->vrl_helper->vh_to_number($vh);
            $horse_info = $this->_get_horses_leveled_info($vh);
            
            if (sizeof($horse_info) == 0){
              $return_data = $this->_virhetaulu(404, "VH-tunnukselta ei löytynyt tietoja");  
            }
                
            else {
            
                //hyppuykapasiteetti ja rohkeus 3, 4
                //tahti ja irtonaisuus 6
                //kuuliaisuus ja luonne 5
                //nopeus ja kestävyys = 1, 2
                //tarkkuus ja ketteryys = 7
                
                $return_data['points'] = array();
                $return_data['points']['hyppykapasiteetti_rohkeus'] = $this->_parse_leveled_info_skills_sum($horse_info, array(3,4));
                $return_data['points']['tahti_irtonaisuus'] = $this->_parse_leveled_info_skills_sum($horse_info, array(6));
                $return_data['points']['kuuliaisuus_luonne'] = $this->_parse_leveled_info_skills_sum($horse_info, array(5));
                $return_data['points']['nopeus_kestavyys'] = $this->_parse_leveled_info_skills_sum($horse_info, array(1,2));
                $return_data['points']['tarkkuus_ketteryys'] = $this->_parse_leveled_info_skills_sum($horse_info, array(7));
                
                $lajit = array(1 =>"erj", 3 => "kerj", 2 => "krj", 4 => "vvj");

                

                
                //$ominaisuudet_id_name = array();
                //$jaokset_id_name = array();
                //$ominaisuus_per_jaos = array();
            
            
                //$this->_get_all_traits($ominaisuudet_id_name, $ominaisuus_per_jaos, $jaokset_id_name);
                
    
                $return_data['erj']['points'] = $return_data['points']['hyppykapasiteetti_rohkeus'] + $return_data['points']['kuuliaisuus_luonne'];
                $return_data['krj']['points'] = $return_data['points']['tahti_irtonaisuus'] + $return_data['points']['kuuliaisuus_luonne'];
                $return_data['kerj']['points'] = $return_data['points']['hyppykapasiteetti_rohkeus'] + $return_data['points']['nopeus_kestavyys'];
                $return_data['vvj']['points'] = $return_data['points']['tarkkuus_ketteryys'] + $return_data['points']['kuuliaisuus_luonne'];
                
                foreach($lajit as $id=>$laji){
                    $return_data[$laji]['error'] = 0;
                    $return_data[$laji]['level_max'] = 10;
                    $return_data[$laji]['level'] = 0;
                }
                $horse_info = $horse_info[0];
            
                $age = $this->porrastetut->calculate_age($horse_info);        
                $levelByAge =  $this->porrastetut->level_by_age($age);
        
                if( $levelByAge == -1 ) {
                    $return_data['error'] = 1;
                    $return_data['error_code'] = 600;
                    $return_data['error_message'] ='Hevonen on liian nuori kilpailemaan tai siltä puuttuu 3-vuotispäivämäärä profiilistaan.';
                }
                else if(!isset($horse_info['sakakorkeus']) || $horse_info['sakakorkeus'] == null || $horse_info['sakakorkeus'] == 0 || $horse_info['sakakorkeus'] < 50){
                    $return_data['error'] = 1;
                    $return_data['error_code'] = 601;
                    $return_data['error_message'] = 'Hevoselta puuttuu säkäkorkeus profiilista!';
                }
                else {
                    
                       
                    $height = $horse_info['sakakorkeus'];
                       
                    
                    $maxs = $this->_get_horse_max_levels($vh);
                    
                    foreach($lajit as $id=>$laji){
                        
                        if(isset($maxs[$id])){
                            $return_data[$laji]['level_max'] = $maxs[$id];
                        }
                        
            
                        $level = $this->porrastetut->get_level_by_points($return_data[$laji]['points']);
                        
                        if ($levelByAge < $level){
                            $level = $levelByAge;
                            $return_data[$laji]['error'] = 403;
                            $return_data[$laji]['error_message'] = "Hevonen on liian nuori nousemaan tasolta vaikka ominaisuuspisteet sen sallisivat.";
                        }
                        
                        
                        $return_data[$laji]['level'] = $level;
                        
                    }
                       
                        
                    
                }
                    
                
                        
            }
            
    
        }

            //Muunnetaan array jsoniksi
            $data = $this->_base_array();
            $data['ominaisuudet'] = $return_data;
            $data['error'] = $return_data['error'];
            $data['error_code'] = $return_data['error_code'];
            $data['error_message'] = $return_data['error_message'];
            //Heitetään data luettavaksi	
            $this->_print($data);
    }
    
    
    
    private function _get_horses_foals($nro)
    {
        $nro = $this->vrl_helper->vh_to_number($nro);
        $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne, sukupuoli, syntymaaika, url, i_nro, e_nro");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');                
        $this->db->group_start();//this will start grouping
        $this->db->where('i_nro', $nro);
        $this->db->or_where('e_nro', $nro);
        $this->db->group_end(); //this will end grouping
        $this->db->order_by('syntymaaika', 'desc');
        $query = $this->db->get();
                
        $hevoset = array();
        
        if ($query->num_rows() > 0)
        {
            $hevoset = $query->result_array();
            
            foreach ($hevoset as &$hevonen){
                $hevonen['reknro'] = $this->vrl_helper->get_vh($hevonen['reknro']);
                $hevonen['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($hevonen['syntymaaika']);
                $hevonen['rek_url'] = $this->hevonen_url . $hevonen['reknro'];
                $hevonen['vanhempi'] = array();
                
                $haettava = "";
                $vanhempi = false;
                //jos hettava oli emä, katsotaan onko isää
                if(isset($hevonen['e_nro']) && $hevonen['e_nro'] == $nro && isset($hevonen['i_nro'])){
                    $haettava = $hevonen['i_nro'];
                    $vanhempi = true;
                }else if(isset($hevonen['i_nro']) && $hevonen['i_nro'] == $nro  && isset($hevonen['e_nro'])){
                    $haettava = $hevonen['e_nro'];
                    $vanhempi = true;
                }
                
                if($vanhempi){
                    $this->db->select("h.reknro, h.nimi, r.rotunro, r.lyhenne as rotulyhenne, h.vari, v.lyhenne as varilyhenne,
                                      sukupuoli, syntymaaika, url");
                    $this->db->from('vrlv3_hevosrekisteri as h');
                    $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
                    $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
                    $this->db->where('h.reknro', $haettava);
                    $query2 = $this->db->get();
                    
                    
                    if ($query2->num_rows() > 0)
                    {
                        $hevonen['vanhempi'] = $query2->result_array()[0];
                        $hevonen['vanhempi']['reknro'] = $this->vrl_helper->get_vh($hevonen['vanhempi']['reknro']);
                        $hevonen['vanhempi']['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($hevonen['vanhempi']['syntymaaika']);
                        $hevonen['vanhempi']['rek_url'] = $this->hevonen_url . $hevonen['vanhempi']['reknro'];
                    }
                
                                
                }
                unset($hevonen['i_nro']);
                unset($hevonen['e_nro']);
            }
        }
        
        return $hevoset;
    }
    
    private function _get_show_result_id($kisa_id){
        $this->db->select("bis_id");
        $this->db->from("vrlv3_kisat_nayttelytulokset");
        $this->db->where("nayttely_id", $kisa_id);
        $this->db->where('hyvaksytty is NOT NULL', NULL, FALSE);
        $this->db->where("hyvaksytty != ", '0000-00-00 00:00:00');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        { 
            return $query->result_array()[0]['bis_id'];
        }
        else {
            return 0;
        }
    }
    
    private function _get_comp_result_id($kisa_id){
        $this->db->select("tulos_id");
        $this->db->from("vrlv3_kisat_tulokset");
        $this->db->where("kisa_id", $kisa_id);
        $this->db->where('hyvaksytty is NOT NULL', NULL, FALSE);
        $this->db->where("hyvaksytty != ", '0000-00-00 00:00:00');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]['tulos_id'];
        }
        else {
            return 0;
        }
    }
    
    private function _get_all_traits(&$ominaisuudet_id_name, &$ominaisuus_per_jaos, &$jaokset_id_name){
        $this->db->select('j.id as jaos_id, j.lyhenne as jaos_lyhenne, j.nimi as jaos_nimi, o.id as ominaisuus_id, o.ominaisuus as ominaisuus_nimi');
        $this->db->from('vrlv3_kisat_jaokset_ominaisuudet');
        $this->db->join('vrlv3_lista_ominaisuudet as o','o.id = vrlv3_kisat_jaokset_ominaisuudet.ominaisuus');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = vrlv3_kisat_jaokset_ominaisuudet.jaos');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $rivi){
                $ominaisuudet_id_name[$rivi['ominaisuus_id']] = $rivi['ominaisuus_nimi'];
                $jaokset_id_name[$rivi['jaos_id']] = array("jaos_nimi"=>$rivi['jaos_nimi'], "jaos_lyhenne"=>$rivi['jaos_lyhenne']);
                if(isset($ominaisuus_per_jaos[$rivi['jaos_id']]) && sizeof($ominaisuus_per_jaos[$rivi['jaos_id']])>0){

                    if(array_search($rivi['ominaisuus_id'], $ominaisuus_per_jaos[$rivi['jaos_id']]) === false){
                        $ominaisuus_per_jaos[$rivi['jaos_id']][] = $rivi['ominaisuus_id'];
                    }
                }else {
                    $ominaisuus_per_jaos[$rivi['jaos_id']] = array($rivi['ominaisuus_id']);
                }
                                
                                                                
            }

            
        }
    }
    
    private function _get_horses_leveled_info($vh){     
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join('vrlv3_hevosrekisteri_ikaantyminen as i', 'h.reknro = i.reknro', 'LEFT');
        $this->db->join('vrlv3_hevosrekisteri_ominaisuudet as o', 'h.reknro = o.reknro', 'LEFT');

        $this->db->where('h.reknro', $vh);
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }else {
            return array();
        }
    }
    
    private function _get_horse_max_levels($vh){
        $max_levels = array();
        $info = $this->_get_horse_competition_info($vh);
        foreach($info as $rivi){
            $max_levels[$rivi['jaos']] = $rivi['taso_max'];
        }
        
        return $max_levels;
        
    }
    
    private function _get_horse_competition_info($vh){
        $this->db->from('vrlv3_hevosrekisteri_kisatiedot');
        $this->db->where('reknro', $vh);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }else {
            return array();
        }
        
    }
    
    
    
    private function _parse_leveled_info_skills_sum($list, $needed_skills){
        $sum = 0;
       
        foreach ($list as $row){
            if(in_array($row['ominaisuus'], $needed_skills)){
                $sum = $sum + $row['arvo'];
            }
        }
        
        return $sum;
    }

    

	
   
}
?>






