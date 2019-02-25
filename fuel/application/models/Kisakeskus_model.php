<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Kisakeskus_model extends CI_Model
{
      function __construct()
    {
	// Call the Model constructor
	parent::__construct();
	$this->load->database();
    }
    
    //Luokat luontilomakkeelle
      public function hae_luokat($porrastettu = 1, $laji = -1){
	    $this->db->select('luokka_id, teksti, porr_vaikeus, laji');
	    if ($laji != -1){
		  $this->db->where('laji', $laji);
	    }
	    $this->db->where('porrastettu', $porrastettu);
	    $this->db->order_by("laji", "asc");
	    $this->db->order_by("porr_vaikeus", "asc");
	    $this->db->order_by("luokka_id", "asc"); 
	    $query = $this->db->get('kilvat_luokat');
	    
	    $luokkalista = array();
	    $luokkalista = $query->result_array();
	    return $luokkalista;
	    
      }
      
      //Luokat luontilomakkeelle
      public function hae_luokan_laji($id){
	    $this->db->select('laji');
	    $this->db->where("luokka_id", $id);
	    $query = $this->db->get('kilvat_luokat');
	    
	    $luokkalista = array();
	    $luokkalista = $query->result_array();
	    
	    if (empty($luokkalista)){
		  return -1;
	    }
	    else {
		  return $luokkalista[0]['laji'];
	    }
	    
      }
      
      //Luokat kutsuun
      public function hae_kisaluokat($id){
	    $this->db->select('kilvat_luokat.luokka_id, kilvat_luokat.porr_vaikeus, kilvat_kisaluokat.russeille, kilvat_kisaluokat.karus_cup, kisaluokka_id, teksti');
	    $this->db->where('kisa_id', $id);	
	    $this->db->join('kilvat_luokat', 'kilvat_luokat.luokka_id = kilvat_kisaluokat.luokka_id');
	    $this->db->order_by("porr_vaikeus", "asc");
	    $this->db->order_by("luokka_id", "asc");
	    $query = $this->db->get('kilvat_kisaluokat');
	    
	    $luokkalista = array();
	    $luokkalista = $query->result_array();
	    
	    
	    foreach ($luokkalista as &$luokka){
		  $this->db->select('rimpsu');
		  $this->db->where('kisa_id', $id);
		  $this->db->where('kisaluokka_id', $luokka['kisaluokka_id']);
		  $query = $this->db->get('kilvat_kisaosallis');
		  
		  $luokka['osallistujat'] = $query->result_array();
	    }	    
	    
	    return $luokkalista;	    
      }
      
      public function onko_vain_russeille($id){
	    $this->db->select('russeille');
	    $this->db->where('luokka_id', $id);	
	    $query = $this->db->get('kilvat_kisaluokat');
	    
	    $luokkalista = array();
	    $luokkalista = $query->result_array();
	    
	    return $luokkalista[0];	    
      }
      
      public function hae_kisat($admin){
	    $this->db->Select("*");
	    if (!$admin){
		  $this->db->where('VRL_kisa_id !=', '0');
	    }
	    $this->db->order_by("pvm", "desc");
	    $query = $this->db->get('kilvat_kutsut');
	     
	    $kisat = $query->result_array();
	    
	    return $kisat;
      }
      
      public function hae_omat_kisat($user, $admin){
	    $this->db->Select("*");
      
	  if (!$admin){
		$this->db->where('user', $user);
	  }

	    $this->db->order_by("pvm", "desc");
	    $query = $this->db->get('kilvat_kutsut');
	     
	    $kisat = $query->result_array();
	    
	    return $kisat;
      }
      
      //Tiedot kutsuun
      public function hae_kutsutiedot($id, $user = -1){
	    $this->db->select('*');
	    $this->db->where('id', $id);
	    if ($user != -1){
		  $this->db->where('user', $user);
	    }
	    $query = $this->db->get('kilvat_kutsut');
	    
	    $kisa = array();
	    $kisa = $query->result_array();
	    
	    if (empty($kisa)){
		  return array();
		  
	    }
	    
	    else {
		  $kisa = $kisa[0];
		  $kisa['luokat'] = $this->hae_kisaluokat($id);
		  return $kisa;
	    }
	    
      }
      
      //Mihin hittoon tätä?
      public function hae_pvm_laji($id){
	    $this->db->select('pvm, laji');
	    $this->db->where('id', $id);
	    $query = $this->db->get('kilvat_kutsut');
	    
	    $kisa = array();
	    $kisa = $query->result_array();
	    
	    if (empty($kisa)){
		  return array();
		  
	    }
	    
	    else {
		  return $kisa[0];
	    }
	    
      }
      
     
      public function lisaa_kutsu($kutsu, $luokka_idt){
  
	    $kutsu = $this->_filter_data('kilvat_kutsut', $kutsu);
	    
	    
	    $this->db->trans_start();
	    //lisätään ponin tiedot
	    $this->db->insert('kilvat_kutsut', $kutsu);
	    
	    //haetaan kutsun id
	    $this->db->select('id');
	    $this->db->where('pvm', $kutsu['pvm']);
	    $this->db->where('laji', $kutsu['laji']);
	    $this->db->where('porrastettu', $kutsu['porrastettu']);
	    $this->db->where('vip', $kutsu['vip']);
	    $this->db->where('user', $kutsu['user']);
	    $this->db->where('talli_vrl', $kutsu['talli_vrl']);
	    
	    $query = $this->db->get('kilvat_kutsut');
	    $kutsu_id = $query->result_array();
	    $kutsu_id = $kutsu_id[0]['id'];
	    
	    $kisaluokat = array();	    
	    foreach ($luokka_idt as $luokka){
		  $kisaluokat[] = array('luokka_id' => $luokka, 'kisa_id' => $kutsu_id);
		   
	    }
	    	    
	    $this->db->insert_batch('kilvat_kisaluokat', $kisaluokat);
	    $this->db->trans_complete();
     }
     
      public function muokkaa_kutsu($id, $user, $kutsu){
	    $kutsu = $this->_filter_data('kilvat_kutsut', $kutsu);
	    $this->db->where('id', $id);
	    $this->db->where('user', $user);
	    $this->db->update('kilvat_kutsut', $kutsu);
      }
      
      public function hae_kutsun_user($id){
	    //haetaan ponin id
	    $this->db->select('user');
	    $this->db->where('id', $id);
	    $query = $this->db->get('kilvat_kutsut');
	    $user = $query->result_array();
	    $user = $user[0]['user'];
	    
	    return $user;
      }
     
     
      public function poista_kutsu($id, $user){
	    $this->db->trans_start();
	    $this->db->delete('kilvat_kutsut', array('id' => $id, 'user' => $user));
	    
	    if ($this->db->affected_rows() == 1){
		  $this->db->delete('kilvat_kisaluokat', array('kisa_id' => $id));
		  $this->db->delete('kilvat_kisaosallis', array('kisa_id' => $id));
	    }
	    $this->db->trans_complete();
	    
	    
     }
     
     

    //Osallistumiset 
      public function lisaa_osallistuja_luokkaan ($kisa_id, $luokka_id, $vh, $vrl, $rimpsu){
	    
	      $lisattava = array();
	  
		  $lisattava['kisa_id'] = $kisa_id;
		  $lisattava['kisaluokka_id'] = $luokka_id;
		  $lisattava['VH'] = $vh;
		  $lisattava['VRL'] = $vrl;
		  $lisattava['rimpsu'] = $rimpsu;		  
	      
	      
	      $this->db->insert('kilvat_kisaosallis', $lisattava);              
     }
     
     public function voi_osallistua($vh, $vrl, $kisa, $luokka){
	    
	    $this->db->trans_start();
	    $this->db->select('max_os_luokka, max_hevo_luokka, max_start_hevo, vip');
	    $this->db->where('id', $kisa);	    
	    $query = $this->db->get('kilvat_kutsut');
	    $kutsu = $query->result_array();
	    
	    $return_array = array();
	    $return_array['result'] = true;
	    $return_array['message'] = "";
	    if (empty($kutsu)){
		  $return_array['result'] = false;
		  $return_array['message'] = "Kutsua ei ole olemassa";
		  return $return_array();
	    }
	    
	    $ExpDate = new DateTime($kutsu[0]['vip']);
	    $Today = new DateTime(date("Y-m-d"));
	    $interval = $ExpDate->diff($Today);
	    $interval = $interval->format('%R%a days');  //<--- to diffenernce by days.
		

	    
	    //Kutsua ei ole, tai VIP on mennyt
	    if ($interval > 0){
		  
		  $return_array['result'] = false;
		  $return_array['message'] = "Kutsua ei ole olemassa, tai VIP on mennyt.";
		  
	    }
	    
	    else if ($this->hevonen_jo_luokassa($vh, $kisa, $luokka)){
		  $return_array['result'] = false;
		  $return_array['message'] = "Hevonen on jo luokassa kertaalleen.";
		  
	    }
	    
	    else if ($this->montako_starttia_hevosella($vh, $kisa) >= $kutsu[0]['max_start_hevo']){
		  $return_array['result'] = false;
		  $return_array['message'] = "Hevonen saa osallistua maksimissaan " . $kutsu[0]['max_start_hevo'] . " luokkaan.";
		  
	    }
	    
	    else if ($this->montako_hevosta_ratsastajalla($vrl, $kisa, $luokka) >= $kutsu[0]['max_hevo_luokka']){
		  $return_array['result'] = false;
		  $return_array['message'] = "Ratsastaja saa ilmoittaa yhteen luokkaan korkeintaan " . $kutsu[0]['max_hevo_luokka'] . " hevosta.";		  
		  
	    }
	    
	    else if ($this->montako_hevosta_luokassa($kisa, $luokka) >= $kutsu[0]['max_os_luokka']){
		  
		  $return_array['result'] = false;
		  $return_array['message'] = "Luokka on täynnä.";
	    }
	    $this->db->trans_complete();
	    return $return_array;
      
     }
     
     public function montako_starttia_hevosella($vh, $kisa){
      
	    $this->db->select('count(*)');       
	    $this->db->where('kisa_id', $kisa);
	    $this->db->where('VH', $vh); 
	    $this->db->from('kilvat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;
	
     }

     public function hevonen_jo_luokassa($vh, $kisa, $luokka){
      
	    $this->db->select('*');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('VH', $vh);
	    $this->db->where('kisa_id', $kisa);
	    $query = $this->db->get('kilvat_kisaosallis');
	
	    $result = $query->result_array();
	 
	    if (empty($result)){
		  return false;
	    
	    }
	    
	    else {
		  
		  return true;
	    }
	
     }
     
      public function montako_hevosta_ratsastajalla($vrl, $kisa, $luokka){
      
	    $this->db->select('count(*) as kpl');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('VRL', $vrl);
	    $this->db->where('kisa_id', $kisa);
	    $this->db->from('kilvat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;
	
     }
     
     
      public function montako_hevosta_luokassa($kisa, $luokka){
      
	    
	    $this->db->select('count(*) as kpl');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('kisa_id', $kisa);
	     $this->db->from('kilvat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;
	
     }
     
      protected function _filter_data($table, $data) {
		$filtered_data = array();
		$columns = $this->db->list_fields($table);

		if (is_array($data)) {
			foreach ($columns as $column) {
				if (array_key_exists($column, $data))
					$filtered_data[$column] = $data[$column];
			}
		}
		return $filtered_data;
	}
     //########################
     
     //STATS
     
     
      public function comp_amount(){   
	    
	    $this->db->select('id');       
	    $this->db->from('kilvat_kutsut');
	    $this->db->where('VRL_kisa_id !=', '0');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;	
      }
      
      public function comp_amount_per(){   
	    
	    $this->db->select('laji, COUNT(id) as kpl');       
	    $this->db->where('VRL_kisa_id !=', '0');
	    $this->db->group_by('laji');
	    $query = $this->db->get('kilvat_kutsut');
	    $return_data = array();
	    $result = $query->result_array();
	    
	    if (empty($result)){
		  
		$result = array();
	    }
	    
	    foreach ($result as $line){
		  $return_data[$line['laji']]['kisat'] = $line['kpl'];
		  
		  $this->db->select('id');       
		  $this->db->from('kilvat_kisaluokat');
		  $this->db->join('kilvat_luokat', 'kilvat_luokat.luokka_id = kilvat_kisaluokat.luokka_id');
		  $this->db->where('kilvat_luokat.laji',$line['laji']);
		  $return_data[$line['laji']]['luokat'] = $this->db->count_all_results();
	    }
	    
	    return $return_data;
      }
      
      public function class_amount(){   
	    
	    $this->db->select('id');       
	    $this->db->from('kilvat_kisaluokat');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;	
      }
      
      public function competitor_amount(){   
	    
	    $this->db->select('id');       
	    $this->db->from('kilvat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;	
      }
      
      public function uniq_competitor_amount(){   
	    
	    $this->db->select('COUNT(DISTINCT(VRL)) as n');
	    $lkm = $this->db->get('kilvat_kisaosallis');
	    $result = $lkm->result_array();
	    return $result[0]['n'];
      }
      
      public function uniq_horse_amount(){   
	    
	    $this->db->select('COUNT(DISTINCT(VH)) AS n');
	    $lkm = $this->db->get('kilvat_kisaosallis');
	    $result = $lkm->result_array();
	    return $result[0]['n'];	
      }
      
      public function fav_classes($laji){   
	    
	    $this->db->select('kilvat_luokat.teksti, count(kilvat_kisaluokat.kisaluokka_id) as kpl');
	    $this->db->join('kilvat_luokat', 'kilvat_luokat.luokka_id = kilvat_kisaluokat.luokka_id');
	    $this->db->where('kilvat_luokat.laji', $laji);
	    $this->db->group_by('kilvat_luokat.luokka_id');
	    $this->db->order_by('kpl', 'desc');
	    $this->db->limit(5);
	    $query = $this->db->get('kilvat_kisaluokat');
	
	    return $query->result_array();	
      }
      
      public function best_competitors(){   
	    
	    $this->db->select('VRL, COUNT(VRL) as kpl');
	    $this->db->order_by('kpl', 'desc');
	    $this->db->group_by('VRL');
	    $this->db->limit(10);
	    $query = $this->db->get('kilvat_kisaosallis');
	
	    return $query->result_array();	
      }
      
      public function best_horses(){   
	    
	    $this->db->select('VH, COUNT(VH) as kpl');
	    $this->db->order_by('kpl', 'desc');
	    $this->db->group_by('VH');
	    $this->db->limit(10);
	    $query = $this->db->get('kilvat_kisaosallis');
	
	    return $query->result_array();	
      }
      
      public function class_info(){
	    $lajit = array('este', 'koulu', 'valjakko', 'kentta');
	    $return_array = array();
	    foreach ($lajit as $laji){
		  $return_array[$laji]['hate'] = $this->hate_classes($laji);
		  $return_array[$laji]['fav'] = $this->fav_classes($laji);
	    }
	    return $return_array;
      }
      
      
      public function class_info_part(){
	    $lajit = array('este', 'koulu', 'valjakko', 'kentta');
	    $return_array = array();
	    foreach ($lajit as $laji){
		  $return_array[$laji]['fav'] = $this->fav_classes_part($laji);
	    }
	    return $return_array;
      }
      
      public function hate_classes($laji){   
	    
	    $this->db->select('kilvat_luokat.teksti, count(kilvat_kisaluokat.kisaluokka_id) as kpl');
	    $this->db->join('kilvat_luokat', 'kilvat_luokat.luokka_id = kilvat_kisaluokat.luokka_id');
	    $this->db->where('kilvat_luokat.laji', $laji);
	    $this->db->group_by('kilvat_luokat.luokka_id');
	    $this->db->order_by('kpl', 'asc');
	    $this->db->limit(5);
	    $query = $this->db->get('kilvat_kisaluokat');
	
	    return $query->result_array();	
      }
      
      

      
      public function fav_classes_part($laji){
	    $this->db->select('kilvat_luokat.teksti, kilvat_kisaluokat.luokka_id, COUNT(kilvat_kisaosallis.id)/COUNT(DISTINCT kilvat_kisaluokat.kisaluokka_id) AS prosentti');
	    $this->db->join('kilvat_kisaluokat', 'kilvat_kisaluokat.kisaluokka_id = kilvat_kisaosallis.kisaluokka_id');
	    $this->db->join('kilvat_luokat', 'kilvat_luokat.luokka_id = kilvat_kisaluokat.luokka_id');
	    $this->db->where('kilvat_luokat.laji', $laji);
	    $this->db->group_by('kilvat_kisaluokat.luokka_id');
	    $this->db->order_by('prosentti', 'desc');
	    $query = $this->db->get('kilvat_kisaosallis');
	   
	    
	    return $query->result_array();	
	    
      }
      
     
     
     
    
    
}