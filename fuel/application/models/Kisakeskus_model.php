<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Kisakeskus_model extends CI_Model
{
    private $CI;
      function __construct()
    {
	// Call the Model constructor
	parent::__construct();
	$this->load->database();
    $this->CI =& get_instance();

    }
    
    
    public function insertNewCompetition ($kutsu, $luokka_idt, $direct, &$msg){
          
	    $kutsu['kp'] = date("Y-m-d",strtotime($kutsu['kp']));
        $kutsu['vip'] = date("Y-m-d",strtotime($kutsu['vip']));
        if(isset($kutsu['info'])){
            $kutsu['info'] = htmlspecialchars($kutsu['info']);
        }
        $kutsu['ilmoitettu'] = date("Y-m-d H:i:s");
        
        if($kutsu['porrastettu'] || $direct){
            $kutsu['hyvaksytty'] = date("Y-m-d H:i:s");
            $kutsu['kasitelty'] = date("Y-m-d H:i:s");
            $kutsu['hyvaksyi'] = "00000";
        }
	    
	    
	    $this->db->trans_start();
	    $this->db->insert('vrlv3_kisat_kisakalenteri', $kutsu);
        $kutsu_id = $this->db->insert_id();
	    
        if($kutsu['porrastettu']){
            $kisaluokat = array();	    
            foreach ($luokka_idt as $luokka){
              $kisaluokat[] = array('luokka_id' => $luokka, 'kisa_id' => $kutsu_id);
               
            }
	    	    
            $this->db->insert_batch('vrlv3_kisat_kisaluokat', $kisaluokat);

        }
	    $this->db->trans_complete();
        
        
        
        if ($this->db->trans_status() === FALSE)
        {
            $msg = "Virhe kutsun lisäämisessä. Yritä hetken kuluttua uudelleen, tai ole yhteydessä ylläpitoon.";
                        var_dump($kutsu);

                return false;
        }
        else {
            return true;
        }
    }
    
    
     public function insertNewShow ($kutsu, &$msg){
          
	    $kutsu['kp'] = date("Y-m-d",strtotime($kutsu['kp']));
        $kutsu['vip'] = date("Y-m-d",strtotime($kutsu['vip']));
        if(isset($kutsu['info'])){
            $kutsu['info'] = htmlspecialchars($kutsu['info']);
        }
        $kutsu['ilmoitettu'] = date("Y-m-d H:i:s");
        
        unset($kutsu['porrastettu']);
	    
	    $this->db->insert('vrlv3_kisat_nayttelykalenteri', $kutsu);
        $kutsu_id = $this->db->insert_id();
	    
       return true;
    }
    
    

    //hae kisakalenterissa olevat tiedot
    public function get_calendar($porrastettu = null, $arvontatapa = null, $jaos = null){ 
        $this->db->select('vip, kp, j.id as jaos, j.lyhenne as jaoslyhenne, k.url, k.info, jarj_talli, t.nimi as tallinimi, kisa_id, porrastettu, k.hyvaksytty');
        $this->db->from('vrlv3_kisat_kisakalenteri as k');
        $this->db->join('vrlv3_tallirekisteri as t', 't.tnro = k.jarj_talli');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        if(isset($porrastettu)){
            $this->db->where('porrastettu', $porrastettu);
        }
        if(isset($arvontatapa)){
            $this->db->where('arvontatapa', $arvontatapa);
        }

        $this->db->where('tulokset', 0);
        $this->db->where('k.hyvaksytty is NOT NULL', NULL, FALSE);

        $this->db->order_by('kp', 'desc');
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    
    public function get_calendar_show(){ 
        $this->db->select('vip, kp, j.id as jaos, j.lyhenne as jaoslyhenne, k.url, k.info, jarj_talli, t.nimi as tallinimi, kisa_id, k.hyvaksytty');
        $this->db->from('vrlv3_kisat_nayttelykalenteri as k');
        $this->db->join('vrlv3_tallirekisteri as t', 't.tnro = k.jarj_talli');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');

        $this->db->where('tulokset', 0);
        $this->db->where('k.hyvaksytty is NOT NULL', NULL, FALSE);

        $this->db->order_by('kp', 'desc');
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    
    
     public function get_results($porrastettu = null, $arvontatapa = null, $jaos = null){ 
        $this->db->select('kp, j.id as jaos, j.lyhenne as jaoslyhenne, k.url, tulos_id, jarj_talli, t.nimi as tallinimi, k.kisa_id, porrastettu');
        $this->db->from('vrlv3_kisat_kisakalenteri as k');
        $this->db->join('vrlv3_tallirekisteri as t', 't.tnro = k.jarj_talli');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        $this->db->join('vrlv3_kisat_tulokset as u', 'k.kisa_id = u.kisa_id');

        if(isset($porrastettu)){
            $this->db->where('porrastettu', $porrastettu);
        }
        if(isset($arvontatapa)){
            $this->db->where('arvontatapa', $arvontatapa);
        }

        $this->db->where('k.tulokset', 1);
        $this->db->where('t.hyvaksytty !=','0000-00-00 00:00:00');

        $this->db->order_by('kp', 'desc');
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    
    
    //hae_yksittäinen_tulos
    public function get_result($result_id = null, $kisa_id = null, $hyvaksytty = true){
        $this->db->select('t.*, k.kp, k.vip, t.ilmoitettu, k.tunnus as tunnus, k.url, t.tunnus as tulosten_lah, k.jarj_talli, k.info, k.tunnus, k.jaos, k.arvontatapa');
        $this->db->from('vrlv3_kisat_tulokset as t');
        $this->db->join('vrlv3_kisat_kisakalenteri as k', 'k.kisa_id = t.kisa_id');

        if(isset($result_id)){
            $this->db->where('t.tulos_id', $result_id);
        }
        if(isset($kisa_id)){
            $this->db->where('t.kisa_id', $kisa_id);
        }
        $this->db->where('k.tulokset', 1);

        if($hyvaksytty){
            $this->db->where('t.hyvaksytty is NOT NULL', NULL, FALSE);
            $this->db->where('t.hyvaksytty !=','0000-00-00 00:00:00');
        } else {
            $this->db->where('t.hyvaksytty','0000-00-00 00:00:00');
                
                
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {        
            return $query->row_array();
        }else {
            return array();
        }
        
    }
    
     //hae_yksittäinen_tulos
    public function get_latest_result_classes($jaos, $user){
        $this->db->select('t.luokat');
        $this->db->from('vrlv3_kisat_tulokset as t');
        $this->db->join('vrlv3_kisat_kisakalenteri as k', 'k.kisa_id = t.kisa_id');
        $this->db->where('k.jaos', $jaos);
        $this->db->where('t.tunnus', $user);
        $this->db->where('t.tulokset', 1);
        $this->db->order_by('t.ilmoitettu', 'desc');
        $this->db->limit(1);
        
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {        
            return $query->row_array()['luokat'];
        }else {
            return null;
        }
        
    }
    
    public function search_competitions($jaos, $parameters){
        $where = array();
        $this->db->select('vip, kp, j.id as jaos, j.lyhenne as jaoslyhenne, k.tunnus, k.url, k.info, jarj_talli, kisa_id, porrastettu, k.hyvaksytty, k.hyvaksyi');
        $this->db->from('vrlv3_kisat_kisakalenteri as k');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        $this->db->where('jaos', $jaos);
        if(isset($parameters['id'])){
            $this->db->where('kisa_id', $parameters['id']);

        }else {
            unset($parameters['id_type']);
            
            foreach ($parameters as $key=>$parameter){
                $where['k.'.$key] = $parameter;
            }
            if(isset($where['k.tunnus'])){
                $where['k.tunnus'] = $this->CI->vrl_helper->vrl_to_number($where['k.tunnus']);
            }
            if(isset($where['k.hyvaksyi'])){
                $where['k.hyvaksyi'] = $this->CI->vrl_helper->vrl_to_number($where['k.hyvaksyi']);
            }
            if(isset($where['k.kp'])){
                $where['k.kp'] = date('Y-m-d',strtotime($where['k.kp']));
            }
            if(isset($where['k.vip'])){
                $where['k.vip'] = date('Y-m-d',strtotime($where['k.vip']));
            }
            if(isset($where['k.hyvaksytty'])){
                $where['k.hyvaksytty'] = date('Y-m-d',strtotime($where['k.hyvaksytty']));
            }

                $this->db->where($where);
                
        }         
        
        $this->db->where('k.hyvaksytty is NOT NULL', NULL, FALSE);
        $this->db->order_by('hyvaksytty', 'desc');
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        } else {
            return array();
        }     
        
    }
    
    
    
     public function search_results($jaos, $parameters){
        $this->db->select('kp, j.id as jaos, j.lyhenne as jaoslyhenne, u.hyvaksytty, u.tunnus, k.url, tulos_id, jarj_talli, k.kisa_id, porrastettu, u.hyvaksyi');
        $this->db->from('vrlv3_kisat_kisakalenteri as k');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        $this->db->join('vrlv3_kisat_tulokset as u', 'k.kisa_id = u.kisa_id');

        if(isset($parameters['id_type']) && $parameters['id_type'] == "kisa_id" && isset($parameters['id'])){
            $this->db->where('k.kisa_id', $parameters['id']);

        }  else if(isset($parameters['id_type']) && $parameters['id_type'] == "tulos_id" && isset($parameters['id'])){
            $this->db->where('tulos_id', $parameters['id']);

        }else {
            unset($parameters['id_type']);
            unset($parameters['id']);

            
            foreach ($parameters as $key=>$parameter){
                $where['k.'.$key] = $parameter;
            }
            if(isset($where['k.tunnus'])){
                $where['k.tunnus'] = $this->CI->vrl_helper->vrl_to_number($where['k.tunnus']);
            }
            if(isset($where['k.hyvaksyi'])){
                $where['u.hyvaksyi'] = $this->CI->vrl_helper->vrl_to_number($where['k.hyvaksyi']);
                unset($where['k.hyvaksyi']);
            }
            if(isset($where['k.kp'])){
                $where['k.kp'] = date('Y-m-d',strtotime($where['k.kp']));
            }
            if(isset($where['k.vip'])){
                $where['k.vip'] = date('Y-m-d',strtotime($where['k.vip']));
            }
            if(isset($where['k.hyvaksytty'])){
                $where['u.hyvaksytty'] = date('Y-m-d',strtotime($where['k.hyvaksytty']));
                unset($where['k.hyvaksytty']);
            }
                $this->db->where($where);        }  

        $this->db->where('k.tulokset', 1);
        $this->db->where('u.hyvaksytty !=','0000-00-00 00:00:00');

        $this->db->order_by('u.hyvaksytty', 'desc');
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    

    
    //tarkistetaan onko samalla päivällä jo tallilla + tunnuksella kisa
    public function check_date_for_competition ($tunnus, $jarj_talli, $pvm, $jaos){
        $this->db->select('*');
        $this->db->from('vrlv3_kisat_kisakalenteri');
        $this->db->where('tunnus', $tunnus);
        $this->db->where('jarj_talli', $jarj_talli);
        $this->db->where('kp', $pvm);
        $this->db->where('jaos', $jaos);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {        
            return false;
        }else {
            return true;
        }
        
        
    }
    
    
          
    //Tiedot kutsuun
    public function hae_kutsutiedot($id, $user = null, $tulokselliset = null){
      $this->db->select('*');
      $this->db->where('kisa_id', $id);
      if (isset($user)){
        $this->db->where('tunnus', $user);
      }
      if(isset($tulokselliset)){
        $this->db->where('tulokset', $tulokselliset);

      }
      $query = $this->db->get('vrlv3_kisat_kisakalenteri');
      

      
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
    
    //Luokat kutsuun
    public function hae_kisaluokat($id){
      $this->db->select('l.id, kl.id as kisaluokka_id, l.taso, l.aste, l.minheight, l.min_age, l.nimi');
      $this->db->where('kl.kisa_id', $id);	
      $this->db->join('vrlv3_kisat_luokat as l', 'kl.luokka_id = l.id');
      $this->db->order_by("l.jarjnro", "asc");
      $this->db->order_by("l.taso", "asc");
      $this->db->order_by("l.aste", "asc");
      $query = $this->db->get('vrlv3_kisat_kisaluokat as kl');
      
      
      $luokkalista = array();
      $luokkalista = $query->result_array();
      
      
      foreach ($luokkalista as &$luokka){
        $this->db->select('rimpsu');
        $this->db->where('kisa_id', $id);
        $this->db->where('kisaluokka_id', $luokka['kisaluokka_id']);
        $query = $this->db->get('vrlv3_kisat_kisaosallis');

        $luokka['osallistujat'] = $query->result_array();
      }	    
      
      return $luokkalista;	    
    }
    
    
    public function get_users_competitions($user, $jonossa, $avoin, $tjonossa, $tulokset, $porrastettu = null){ 
        $this->db->select('vip, kp, j.lyhenne as jaoslyhenne, k.url, k.info, jarj_talli, k.ilmoitettu as kisailmoitettu, t.ilmoitettu as tulosilmoitettu, k.kisa_id, t.tulos_id, porrastettu, k.hyvaksytty');
        $this->db->from('vrlv3_kisat_kisakalenteri as k');     
        $this->db->join('vrlv3_kisat_tulokset as t', 'k.kisa_id = t.kisa_id', 'left');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        $this->db->where('k.tunnus', $user);
        $this->db->where('k.vanha', 0);

        if(isset($porrastettu)){
            $this->db->where('porrastettu', $porrastettu);
        }
        
        if($jonossa){
            $this->db->where('k.tulokset', 0);
            $this->db->group_start();
            $this->db->where('k.hyvaksytty IS NULL OR k.hyvaksytty = \'0000-00-00 00:00:00\'');
            $this->db->group_end();
            $this->db->order_by('k.ilmoitettu', 'desc');

        }
        
        else if($tjonossa){
            $this->db->where('k.tulokset', 1);
            $this->db->group_start();
            $this->db->where('t.hyvaksytty IS NULL OR t.hyvaksytty = \'0000-00-00 00:00:00\'');
            $this->db->group_end();
            $this->db->order_by('t.ilmoitettu', 'desc');

        } else if($avoin){
            $this->db->where('k.tulokset', 0);
            $this->db->where('k.hyvaksytty is NOT NULL', NULL, FALSE);
            $this->db->order_by('k.kp', 'asc');

        } else if ($tulokset){
            $this->db->where('k.tulokset', 1);
            $this->db->where('t.hyvaksytty IS NOT NULL', NULL, FALSE);
            $this->db->order_by('k.kp', 'desc');

        }
        
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    
    
    
    public function get_users_shows($user, $jonossa, $avoin, $tjonossa, $tulokset){ 
        $this->db->select('vip, kp, j.lyhenne as jaoslyhenne, k.url, k.info, jarj_talli, k.ilmoitettu as kisailmoitettu, t.ilmoitettu as tulosilmoitettu,
                          k.kisa_id, t.tulos_id, k.hyvaksytty');
        $this->db->from('vrlv3_kisat_nayttelykalenteri as k');     
        $this->db->join('vrlv3_kisat_tulokset as t', 'k.kisa_id = t.kisa_id', 'left');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = k.jaos');
        $this->db->where('k.tunnus', $user);
        $this->db->where('k.vanha', 0);
        
        if($jonossa){
            $this->db->where('k.tulokset', 0);
            $this->db->group_start();
            $this->db->where('k.hyvaksytty IS NULL OR k.hyvaksytty = \'0000-00-00 00:00:00\'');
            $this->db->group_end();
            $this->db->order_by('k.ilmoitettu', 'desc');

        }
        
        else if($tjonossa){
            $this->db->where('k.tulokset', 1);
            $this->db->group_start();
            $this->db->where('t.hyvaksytty IS NULL OR t.hyvaksytty = \'0000-00-00 00:00:00\'');
            $this->db->group_end();
            $this->db->order_by('t.ilmoitettu', 'desc');

        } else if($avoin){
            $this->db->where('k.tulokset', 0);
            $this->db->where('k.hyvaksytty is NOT NULL', NULL, FALSE);
            $this->db->order_by('k.kp', 'asc');

        } else if ($tulokset){
            $this->db->where('k.tulokset', 1);
            $this->db->where('t.hyvaksytty IS NOT NULL', NULL, FALSE);
            $this->db->order_by('k.kp', 'desc');

        }
        
        $this->db->limit('1000');
                
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }else {
            return array();
        }
        
    }
    
    
    
    
    /*
        //////////////////////WANHAT
    
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
     
     */

    //Osallistumiset h
      private function _lisaa_osallistuja_luokkaan ($kisa_id, $luokka_id, $vh, $vrl, $rimpsu){
	    
	      $lisattava = array();
	  
		  $lisattava['kisa_id'] = $kisa_id;
		  $lisattava['kisaluokka_id'] = $luokka_id;
		  $lisattava['VH'] = $vh;
		  $lisattava['VRL'] = $vrl;
		  $lisattava['rimpsu'] = $rimpsu;		  
	      
	      
	      $this->db->insert('vrlv3_kisat_kisaosallis', $lisattava);              
     }
     
     public function osallistuminen($vh, $vrl, $kisa, $luokka, $rimpsu){
	    
	    $this->db->trans_start();
	    $this->db->select('s_hevosia_per_luokka as max_os_luokka, s_luokkia_per_hevonen as max_start_hevo, vip');
	    $this->db->where('kisa_id', $kisa);	    
	    $query = $this->db->get('vrlv3_kisat_kisakalenteri');
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
		

        $this->CI->load->library('Kisajarjestelma');
        $this->CI->load->model('Tunnukset_model');
        $this->CI->load->model('Hevonen_model');
        $kutsu[0]['max_hevo_luokka'] = $this->CI->kisajarjestelma->max_hevosia_per_luokka_per_ratsastaja($kutsu[0]['max_os_luokka']);    
        
	    //Kutsua ei ole, tai VIP on mennyt
	    if ($interval > 0){
		  
		  $return_array['result'] = false;
		  $return_array['message'] = "Kutsua ei ole olemassa, tai VIP on mennyt.";
		  
	    }
        else if(!$this->Tunnukset_model->onko_tunnus($vrl)){
          $return_array['result'] = false;
		  $return_array['message'] = "VRL-tunnusta ei ole olemassa";
        }
        else if(!$this->Hevonen_model->onko_tunnus($vh)){
          $return_array['result'] = false;
		  $return_array['message'] = "VH-tunnusta ei ole olemassa";
        }
	    
	    else if ($this->_hevonen_jo_luokassa($vh, $kisa, $luokka)){
		  $return_array['result'] = false;
		  $return_array['message'] = "Hevonen on jo luokassa kertaalleen.";
		  
	    }
	    
	    else if ($this->_montako_starttia_hevosella($vh, $kisa) >= $kutsu[0]['max_start_hevo']){
		  $return_array['result'] = false;
		  $return_array['message'] = "Hevonen saa osallistua maksimissaan " . $kutsu[0]['max_start_hevo'] . " luokkaan.";
		  
	    }
	    
	    else if ($this->_montako_hevosta_ratsastajalla($vrl, $kisa, $luokka) >= $kutsu[0]['max_hevo_luokka']){
		  $return_array['result'] = false;
		  $return_array['message'] = "Ratsastaja saa ilmoittaa yhteen luokkaan korkeintaan " . $kutsu[0]['max_hevo_luokka'] . " hevosta.";		  
		  
	    }
	    
	    else if ($this->_montako_hevosta_luokassa($kisa, $luokka) >= $kutsu[0]['max_os_luokka']){
		  
		  $return_array['result'] = false;
		  $return_array['message'] = "Luokka on täynnä.";
	    }
        
        else if($return_array['result'] === true){
            $this->_lisaa_osallistuja_luokkaan ($kisa, $luokka, $vh, $vrl, $rimpsu);
        }
	    $this->db->trans_complete();
	    return $return_array;
      
     }
     
     private function _montako_starttia_hevosella($vh, $kisa){
      
	    $this->db->select('count(*)');       
	    $this->db->where('kisa_id', $kisa);
	    $this->db->where('VH', $vh); 
	    $this->db->from('vrlv3_kisat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;
	
     }

     private function _hevonen_jo_luokassa($vh, $kisa, $luokka){
      
	    $this->db->select('*');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('VH', $vh);
	    $this->db->where('kisa_id', $kisa);
	    $query = $this->db->get('vrlv3_kisat_kisaosallis');
	
	    $result = $query->result_array();
	 
	    if (empty($result)){
		  return false;
	    
	    }
	    
	    else {
		  
		  return true;
	    }
	
     }
     
      private function _montako_hevosta_ratsastajalla($vrl, $kisa, $luokka){
      
	    $this->db->select('count(*) as kpl');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('VRL', $vrl);
	    $this->db->where('kisa_id', $kisa);
	    $this->db->from('vrlv3_kisat_kisaosallis');
	    $lkm = $this->db->count_all_results();
	
	    return $lkm;
	
     }
     
     
      private function _montako_hevosta_luokassa($kisa, $luokka){
      
	    
	    $this->db->select('count(*) as kpl');       
	    $this->db->where('kisaluokka_id', $luokka);
	    $this->db->where('kisa_id', $kisa);
	     $this->db->from('vrlv3_kisat_kisaosallis');
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