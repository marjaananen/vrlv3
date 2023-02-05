<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Jaos_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    function add_jaos(&$msg, $jaos){
            $this->db->insert('vrlv3_kisat_jaokset', $jaos);
            $id = $this->db->insert_id();
            return $id;
        }
    
    function edit_jaos($id, $jaos){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_kisat_jaokset', $jaos);
    }
    function set_jaos_online($id, $online = true){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_kisat_jaokset', array('toiminnassa' => $online));
    }
    
    function add_owner_to_jaos($id, $tunnus)
    {
        $data = array('jid' => $id, 'tunnus' => $tunnus);      
        $this->db->insert('vrlv3_kisat_jaokset_omistajat', $data);
    }

    function delete_jaos($id, &$msg){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id);
        $this->db->delete('vrlv3_kisat_jaokset', $data);
        return true;
    }
    
    //names
    function get_users_jaos($pinnumber)
    {
        $this->db->select('vrlv3_kisat_jaokset.id, nimi, lyhenne, toiminnassa, taso');
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->join('vrlv3_kisat_jaokset_omistajat', 'vrlv3_kisat_jaokset.id = vrlv3_kisat_jaokset_omistajat.jid');
        $this->db->where('vrlv3_kisat_jaokset_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_event_list_by_type($jaos = false, $pulju_type = null)
    {
        $this->db->select('vrlv3_tapahtumat.*, j.lyhenne as lyhenne');
        $this->db->from('vrlv3_tapahtumat');
        if($jaos){
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = vrlv3_tapahtumat.jaos_id');
        }else {
        $this->db->join('vrlv3_puljut as j', 'j.id = vrlv3_tapahtumat.pulju_id');
        }

        if($jaos){
            $this->db->where('vrlv3_tapahtumat.jaos_id IS NOT NULL');
        }

        else if (isset($pulju_type)){
            $this->db->where('vrlv3_tapahtumat.jaos_id IS NULL');
            $this->db->where('j.tyyppi', $pulju_type);
        }
        
        $this->db->where('vrlv3_tapahtumat.tulos', 1);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
     function get_event_list($jaos = null, $pulju = null)
    {
        $this->db->select('*, ');
        $this->db->from('vrlv3_tapahtumat');
        if(isset($jaos)){
            $this->db->where('vrlv3_tapahtumat.jaos_id', $jaos);
        } else if (isset($pulju)){
            $this->db->where('vrlv3_tapahtumat.pulju_id', $pulju);
        }
        $this->db->where('vrlv3_tapahtumat.tulos', 1);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_event($id, $jaos = null, $pulju = null)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_tapahtumat');
        $this->db->where('vrlv3_tapahtumat.id', $id);
        if(isset($jaos)){
            $this->db->where('vrlv3_tapahtumat.jaos_id', $jaos);
        } else if (isset($pulju)){
            $this->db->where('vrlv3_tapahtumat.pulju_id', $pulju);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
    
    
    function get_event_horses($event){
        $this->db->select('o.oid, o.vh, o.tulos, o.palkinto, o.kommentti, h.nimi');
        $this->db->from('vrlv3_tapahtumat_osallistujat as o');
        $this->db->join('vrlv3_hevosrekisteri as h', 'o.vh = h.reknro');
        $this->db->where('o.tapahtuma', $event);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();

    }

    function get_event_stables($event){
        $this->db->select('o.oid, o.tnro, o.tulos, o.palkinto, o.kommentti, h.nimi');
        $this->db->from('vrlv3_tapahtumat_osallistujat_talli as o');
        $this->db->join('vrlv3_tallirekisteri as h', 'o.tnro = h.tnro');
        $this->db->where('o.tapahtuma', $event);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();

    }

    function get_event_ppl($event){
        $this->db->select('o.oid, o.tunnus, o.tulos, o.palkinto, o.kommentti, h.nimimerkki');
        $this->db->from('vrlv3_tapahtumat_osallistujat_tunnus as o');
        $this->db->join('vrlv3_tunnukset as h', 'o.tunnus = h.tunnus');
        $this->db->where('o.tapahtuma', $event);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();

    }
    
    function delete_event($id, $jaos_id = null, $pulju_id = null){
        $data = array('id' => $id, 'jaos_id'=>$jaos_id, 'pulju_id'=>$pulju_id);
        $this->db->delete('vrlv3_tapahtumat', $data);
        return true;
    }
    
    function delete_event_horse($id, $event_id){
        $data = array('oid' => $id, 'tapahtuma'=>$event_id);
        $this->db->delete('vrlv3_tapahtumat_osallistujat', $data);
        return true;
    }

    function delete_event_stable($id, $event_id){
        $data = array('oid' => $id, 'tapahtuma'=>$event_id);
        $this->db->delete('vrlv3_tapahtumat_osallistujat_talli', $data);
        return true;
    }

    function delete_event_ppl($id, $event_id){
        $data = array('oid' => $id, 'tapahtuma'=>$event_id);
        $this->db->delete('vrlv3_tapahtumat_osallistujat_tunnus', $data);
        return true;
    }
    
    function add_event($pvm, $title, $user, $jaos, $participant_data = array(), $pulju = false){
        $event_data['otsikko'] = $title;
        $event_data['pv'] = $this->CI->vrl_helper->normal_date_to_sql($pvm);
        $event_data['vastuu'] = $user;
        $jaos_info = "";
        if($pulju){
            $event_data['pulju_id'] = $jaos;
            $jaos_info = $this->get_pulju($jaos);
        }else {
            $event_data['jaos_id'] = $jaos;
            $jaos_info = $this->get_jaos($jaos);

        }
        
        $event_data['jaos'] = $jaos_info['lyhenne'];


    
     $this->db->insert('vrlv3_tapahtumat', $event_data);
     $id = $this->db->insert_id();
        
     $this->add_event_participants($id, $participant_data);
        
        
        return $id;   
    }
    
    function edit_event($id, $jaos, $title, $date, $pulju = false){
        $where = array();
        if($pulju){
            $where = array('id'=> $id, 'pulju_id' => $jaos);

        }else {
            $where = array('id'=> $id, 'jaos_id' => $jaos);

        }
        $this->db->where($where);
        $this->db->update('vrlv3_tapahtumat', array("otsikko"=>$title, "pv" => $this->CI->vrl_helper->normal_date_to_sql($date)));
    
        return true;
    }
    
    
    
    function add_event_participants($id, $participant_data = array()){
        
     foreach($participant_data as $horse){
        $horse['tapahtuma'] = $id;
        $horse['hyv'] = 1;
        $this->db->insert('vrlv3_tapahtumat_osallistujat', $horse);

        
     }
        
        
        return true;
    }

    function add_event_participants_ppl($id, $participant_data = array()){
        
        foreach($participant_data as $horse){
           $horse['tapahtuma'] = $id;
           $horse['hyv'] = 1;
           $this->db->insert('vrlv3_tapahtumat_osallistujat_tunnus', $horse);
   
           
        }
           
           
           return true;
    }

     function add_event_participants_stable($id, $participant_data = array()){
        
        foreach($participant_data as $horse){
           $horse['tapahtuma'] = $id;
           $horse['hyv'] = 1;
           $this->db->insert('vrlv3_tapahtumat_osallistujat_talli', $horse);
   
           
        }
           
           
           return true;
       }
    
    function get_event_horse_prizes($reknro){
        $this->db->select('t.pv, t.otsikko, o.tulos, o.palkinto, o.kommentti, t.jaos');
        $this->db->from('vrlv3_tapahtumat_osallistujat as o');
        $this->db->join('vrlv3_tapahtumat as t', 't.id = o.tapahtuma');
        $this->db->where('o.vh', $reknro);
        $this->db->where("o.hyv", 1);
        $this->db->where("t.tulos", 1);
        $this->db->order_by("t.pv", 'desc');
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }

        
    function get_event_stable_prizes($reknro){
        $this->db->select('t.pv, t.otsikko, o.tulos, o.palkinto, o.kommentti, t.jaos');
        $this->db->from('vrlv3_tapahtumat_osallistujat_talli as o');
        $this->db->join('vrlv3_tapahtumat as t', 't.id = o.tapahtuma');
        $this->db->where('o.tnro', $reknro);
        $this->db->where("o.hyv", 1);
        $this->db->where("t.tulos", 1);
        $this->db->order_by("t.pv", 'desc');
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }

        
    function get_event_ppl_prizes($reknro){
        $this->db->select('t.pv, t.otsikko, o.tulos, o.palkinto, o.kommentti, t.jaos');
        $this->db->from('vrlv3_tapahtumat_osallistujat_tunnus as o');
        $this->db->join('vrlv3_tapahtumat as t', 't.id = o.tapahtuma');
        $this->db->where('o.tunnus', $reknro);
        $this->db->where("o.hyv", 1);
        $this->db->where("t.tulos", 1);
        $this->db->order_by("t.pv", 'desc');
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }

    
    
    function get_show_horse_prizes($reknro, $nimi = null){
        $this->db->select('o.*, t.*, tu.paatuomari_nimi');
        $this->db->from('vrlv3_kisat_bis_tulosrivit as o');
        $this->db->join('vrlv3_kisat_nayttelykalenteri as t', 't.kisa_id = o.nayttely_id');
        $this->db->join('vrlv3_kisat_nayttelytulokset as tu', 'tu.nayttely_id = o.nayttely_id');

        
        if(isset($nimi) && strlen($nimi)>1){
            $this->db->where('o.vh_id', NULL);
            $this->db->like('o.vh_nimi', $nimi);
        }else {
            $this->db->where('o.vh_id', $reknro);

        }
        
        $this->db->where('tu.hyvaksytty IS NOT NULL');
        $this->db->where('tu.hyvaksytty != \'0000-00-00 00:00:00\'');
        $this->db->order_by("t.kp", 'desc');
        
         $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    
    function get_jaos_list($only_active = false, $only_porrastetut = false, $skip_shows = true, $only_shows = false)
    {
        $this->db->select("id, nimi, lyhenne, kuvaus, url, IF(toiminnassa='1', 'toiminnassa', 'ei toiminnassa') as toiminnassa, nayttelyt as nayttelyjaos, IF(nayttelyt='1', 'nayttelyjaos', 'kisajaos') as nayttelyt");
        if($only_active){
            $this->db->where("toiminnassa", 1);
        }if($only_porrastetut){
                    $this->db->where("s_salli_porrastetut", 1);

        }if ($skip_shows){
            $this->db->where("nayttelyt", 0);
        }else if ($only_shows){
            $this->db->where("nayttelyt", 1);
        }
        $this->db->from('vrlv3_kisat_jaokset');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaos_option_list($only_active = false, $only_porrastetut = false, $skip_shows = true, $only_show = false){
        $options = array();
        
        //jos näyttelyt, NJ on oletus, muilla tyhjä on oletus
        if(!$only_show){
            $options[0] = "";
        }
        
        $jaos_list = $this->get_jaos_list($only_active, $only_porrastetut, $skip_shows, $only_show);
        
        foreach ($jaos_list as $jaos){
            $options[$jaos['id']] = $jaos['lyhenne'];
        }
        
        return $options;
    }
    function get_jaos_porr_list()
    {
        $this->db->select("id, nimi, lyhenne, laji, kuvaus, url, IF(toiminnassa='1', 'toiminnnassa', 'ei toiminnassa') as toiminnassa");
        $this->db->from('vrlv3_kisat_jaokset');
        $this->db->where("s_salli_porrastetut", 1);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaokset_all($only_nayttelyt = false, $only_basic = false)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_jaokset');
        if($only_nayttelyt){
        $this->db->where('nayttelyt', 1) ;
        }
        if($only_basic){
            $this->db->where('nayttelyt', 0) ;

        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_jaokset_full($only_nayttelyt = false, $only_basic = false)
    {
        $jaokset = $this->get_jaokset_all($only_nayttelyt, $only_basic);
        foreach ($jaokset as &$jaos){
            $jaos['yllapito'] = $this->get_jaos_handlers($jaos['id']);
            $jaos['luokat_porr'] = $this->get_class_list($jaos['id'], true, true);
        }
        
        return $jaokset;
    }
    
    function get_jaos_handlers($id){
        return $this->get_jaos_owners($id, true);
        
    }
    
    function get_jaos($id)
    {
        $jaos = array();
        $this->db->select('*');
        $this->db->from('vrlv3_kisat_jaokset as j');
        //$this->db->join('vrlv3_kisat_jaokset_omistajat', 'vrlv3_kisat_jaokset.id = vrlv3_kisat_jaokset_omistajat.jid');
        //$this->db->join('vrlv3_lista_painotus as l', 'j.laji = l.pid', 'left');
        $this->db->where('j.id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $result = $query->result_array();
            $jaos = $result[0];
            
            
            return $jaos;
        }
        
        return array();
    }
    
    
    function get_jaos_owners($id, $only_yp = false)
    {
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, nimimerkki, taso, CONCAT(vrlv3_kisat_jaokset_omistajat.tunnus, "/", vrlv3_kisat_jaokset_omistajat.jid) as id');
        $this->db->from('vrlv3_kisat_jaokset_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_kisat_jaokset_omistajat.tunnus');
        if($only_yp){
          $this->db->where('vrlv3_kisat_jaokset_omistajat.taso', 1);

        }
        $this->db->where('jid', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function edit_jaos_user_rights($tunnus){
        
        
        $this->db->trans_start();
        
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, vrlv3_tunnukset.id as id, taso, count(vrlv3_kisat_jaokset_omistajat.jid)');
        $this->db->from('vrlv3_kisat_jaokset_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_kisat_jaokset_omistajat.tunnus');
        $this->db->where('vrlv3_tunnukset.tunnus', $tunnus);
        $this->db->group_by('vrlv3_kisat_jaokset_omistajat.jid');

        $query = $this->db->get();
        
        
        if ($query->num_rows() > 0)
        {
            $id = $query->result_array()[0]['id'];
            //henkilöllä on jaoksiin yp tai kalenterioikeuksia (tai molempia)
            //exclude jaos-yp (9) and kisakalenteri (10)
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>9));
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>10));
            
            $yp_done = false;
            $cal_done = false;
            foreach ($query->result_array() as $rivi){
                if ($rivi['taso'] == 1 && !$yp_done){
                    
                    $this->db->insert('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>9));
                    $yp_done = true;
                }
                else if ($rivi['taso'] == 0 && !$cal_done){
                    $this->db->insert('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>10));
                    $cal_done = true;
                }
                
            }
            
            


        } else {
            
            
           //otetaan jaosoikat pois
           $id = 0;

           $this->db->select('id');
           $this->db->from('vrlv3_tunnukset');
           $this->db->where('tunnus', $tunnus);
           $query = $this->db->get();
           
           if ($query->num_rows() > 0)
           {
               $id = $query->row_array()['id']; 
           }
            //exclude jaos-yp (9) and kisakalenteri (10)
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>9));
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>10));


        }
        
        $this->db->trans_complete();

        return true;
        
        
        
    }

    function is_name_in_use($name, $id = null, $pulju = false)
    {
        $this->db->select('id');
        if($pulju){
            $this->db->from('vrlv3_puljut');

        }else {
            $this->db->from('vrlv3_kisat_jaokset');
        }
        $this->db->where('nimi', $name);
        if (isset($id)){
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function is_lyhenne_in_use($name, $id = null, $pulju = false)
    {
        
        $this->db->select('id');
        if($pulju){
            $this->db->from('vrlv3_puljut');

        }else {
            $this->db->from('vrlv3_kisat_jaokset');
        }
        $this->db->where('lyhenne', $name);
        if (isset($id)){
            $this->db->where('id !=', $id);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function is_jaos_owner($pinnumber, $id, $taso = null)
    {
        $this->db->select('tunnus');
        $this->db->from('vrlv3_kisat_jaokset_omistajat');
        $this->db->where('jid', $id);
        $this->db->where('tunnus', $pinnumber);
        if(isset($taso)){
            $this->db->where('taso', $taso);

        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function get_class_options($jaos = null, $only_usable = true, $only_porrastetut = true){
         $this->db->select("j.lyhenne, l.nimi, l.id, l.taso");
        $this->db->from('vrlv3_kisat_luokat as l');
        $this->db->join('vrlv3_kisat_jaokset as j', 'j.id = l.jaos');
        if(isset($jaos)){
            $this->db->where("l.jaos", $jaos);
        }else {
            $this->db->order_by("l.jaos");
        }
        $this->db->order_by("l.jarjnro");
        if($only_porrastetut){
            $this->db->where("j.s_salli_porrastetut", 1);
            $this->db->where("l.porrastettu", 1);
        }
        if($only_usable){
            $this->db->where("l.kaytossa", 1);
        }
        
        $options = array();
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
           foreach ($query->result_array() as $class){
            $options[$class['id']] = $class['lyhenne'] . ": " . $class['nimi'] . " (vt. " . $class['taso'] . ")";
           }
        }
        
        return $options;
    }
    
    function get_class_list($jaos, $only_usable = true, $only_porrastetut = false)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_luokat');
        $this->db->where("jaos", $jaos);
        $this->db->order_by("jarjnro");
        if($only_porrastetut){
            $this->db->where("porrastettu", 1);
        }
        if($only_usable){
            $this->db->where("kaytossa", 1);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_class($id, $jaos_id)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_luokat');
        $this->db->where("id", $id);
        $this->db->where("jaos", $jaos_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            
            $array = $query->result_array();
            return $array[0];
        }
        
        return array();
    }
    
    
    
    function add_class($jaos_id, $laji_id, $class){
        $class['jaos'] = $jaos_id;
        $class['laji'] = $laji_id;
        $this->db->insert('vrlv3_kisat_luokat', $class);
        $id = $this->db->insert_id();
        return $id;
    }
    
     function edit_class($class_id, $class){        
        unset($class['jaos']);
        unset($class['laji']);
        $this->db->where('id', $class_id);
        $this->db->update('vrlv3_kisat_luokat', $class);
    }
    
    function delete_class($id, $jaos_id){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id, 'jaos'=>$jaos_id);
        $this->db->delete('vrlv3_kisat_luokat', $data);
        return true;
    }
    
    //////////////////////////////////////////////////////7
    // PALKINNOT
    ///////////////////////////////////////////////////77/
    
    function get_reward_list($jaos, $only_usable = true)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_jaokset_palkinnot');
        $this->db->where("jaos", $jaos);
        $this->db->order_by("jarjnro");
    
        if($only_usable){
            $this->db->where("kaytossa", 1);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_reward($id, $jaos_id)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_kisat_jaokset_palkinnot');
        $this->db->where("id", $id);
        $this->db->where("jaos", $jaos_id);

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $array = $query->result_array();
            return $array[0];
        }
        
        return array();
    }
    
    
    
    function add_reward($jaos_id, $class){
        $class['jaos'] = $jaos_id;
        $this->db->insert('vrlv3_kisat_jaokset_palkinnot', $class);
        $id = $this->db->insert_id();
        return $id;
    }
    
     function edit_reward($class_id, $class){        
        unset($class['jaos']);
        $this->db->where('id', $class_id);
        $this->db->update('vrlv3_kisat_jaokset_palkinnot', $class);
        return true;
    }
    
    function delete_reward($id, $jaos_id){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id, 'jaos'=>$jaos_id);
        $this->db->delete('vrlv3_kisat_jaokset_palkinnot', $data);
        return true;
    }
    
    
    ///////////////////////////////////////////////////////
    // Kisakalenterit
    //////////////////////////////////////////////////////
        
  
    
    function getEtuuspisteet($jaos, $tunnus){
        $this->db->select('*');
        $this->db->from('vrlv3_kisat_etuuspisteet');
        $this->db->where('tunnus', $tunnus);
        $this->db->where('jaos', $jaos);
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]; 
        }else {
            return array();
        }
    			
        
    }
    
    
    function usersOpenCompetitions($jaos, $tunnus, $porrastettu = false){
        $this->db->select('count(kisa_id) as avoimia');
        $this->db->from('vrlv3_kisat_kisakalenteri');
        $this->db->where('jaos', $jaos);
        $this->db->where('tunnus', $tunnus);
        if($porrastettu){
            $this->db->where('porrastettu', 1);

        }else {
            $this->db->where('porrastettu', 0);

        }
        $this->db->where('vanha', 0);
        $this->db->where('tulokset', 0);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]['avoimia']; 
        }else {
            return 0;
        }
    }
    
    
    //////////////////////////////////////////////////////////////////7
    // PULJUT
    /////////////////////////////////////////////////////////////////
    
     function add_pulju(&$msg, $pulju){
            $this->db->insert('vrlv3_puljut', $pulju);
            $id = $this->db->insert_id();
            return $id;
        }
    
    function edit_pulju($id, $pulju){
        unset($pulju['pulju']);
        $this->db->where('id', $id);
        $this->db->update('vrlv3_puljut', $pulju);
    }
    function set_pulju_online($id, $online = true){
        $this->db->where('id', $id);
        $this->db->update('vrlv3_puljut', array('toiminnassa' => $online));
    }
    
    function add_owner_to_pulju($id, $tunnus)
    {
        $data = array('jid' => $id, 'tunnus' => $tunnus);      
        $this->db->insert('vrlv3_puljut_omistajat', $data);
    }

    function delete_pulju($id, &$msg){
        //todo delete luokat, ominaisuudet
        $data = array('id' => $id);
        $this->db->delete('vrlv3_puljut', $data);
        return true;
    }
    
    //names
    function get_users_pulju($pinnumber)
    {
        $this->db->select('vrlv3_puljut.id, nimi, lyhenne, toiminnassa, taso');
        $this->db->from('vrlv3_puljut');
        $this->db->join('vrlv3_puljut_omistajat', 'vrlv3_puljut.id = vrlv3_puljut_omistajat.jid');
        $this->db->where('vrlv3_puljut_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    
    
    
    
    function get_pulju_list($only_active = false, $type = null)
    {
        $this->db->select("id, nimi, lyhenne, kuvaus, url, IF(toiminnassa='1', 'toiminnassa', 'ei toiminnassa') as toiminnassa,
                          CASE WHEN  tyyppi='1' THEN 'ktk'
                          WHEN tyyppi='3' THEN 'laatis'
                          WHEN tyyppi='2' THEN 'rotuyhdistys'
                          ELSE 'tuntematon' END as tyyppi");
        if($only_active){
            $this->db->where("toiminnassa", 1);
        }if(isset($type)){
                    $this->db->where("$tyyppi", $type);

        }
        $this->db->from('vrlv3_puljut');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
        
    function get_pulju_type_option_list(){
        $options = array();
        
        $query = $this->db->get('vrlv3_lista_puljutyyppi');
                
        if ($query->num_rows() > 0)
        {
             foreach ($query->result_array() as $pulju){
                $options[$pulju['pid']] = $pulju['tyyppi'];
            } 
        }       
        
        return $options;
    }
    
    function get_pulju_option_list($only_active = false, $type = null){
        $options = array();
        
        $pulju_list = $this->get_pulju_list($only_active, $type);
        
        foreach ($pulju_list as $pulju){
            $options[$pulju['id']] = $pulju['lyhenne'];
        }
        
        return $options;
    }
    
    function get_puljut_all($type = null, $online = null)
    {
        $this->db->select("*");
        $this->db->from('vrlv3_puljut');
        
        
        if(isset($online)){
            $this->db->where("toiminnassa", $online);
        }
        
        if(isset($type)){
            $this->db->where("tyyppi", $type);
        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_puljut_full($type = null, $online = null)
    {
        $jaokset = $this->get_puljut_all($type, $online);
        foreach ($jaokset as &$pulju){
            $pulju['yllapito'] = $this->get_pulju_handlers($pulju['id']);
            $pulju['rodut'] = $this->get_pulju_breeds($pulju['id']);
        }
        
        return $jaokset;
    }
    
    function get_pulju_breeds($id){
        $this->db->select('r.*');
        $this->db->from('vrlv3_lista_rodut as r');
        $this->db->join('vrlv3_puljut_rodut as p', 'p.rotu = r.rotunro');
        $this->db->where('p.pulju', $id);
        
         $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_pulju_handlers($id){
        return $this->get_pulju_owners($id, true);
        
    }
    
    function get_pulju($id)
    {
        $pulju = array();
        $this->db->select('*');
        $this->db->from('vrlv3_puljut as j');
        //$this->db->join('vrlv3_puljut_omistajat', 'vrlv3_puljut.id = vrlv3_puljut_omistajat.jid');
        //$this->db->join('vrlv3_lista_painotus as l', 'j.laji = l.pid', 'left');
        $this->db->where('j.id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $result = $query->result_array();
            $pulju = $result[0];
            
            
            return $pulju;
        }
        
        return array();
    }
    
    
    function get_pulju_owners($id, $only_yp = false)
    {
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, nimimerkki, taso, CONCAT(vrlv3_puljut_omistajat.tunnus, "/", vrlv3_puljut_omistajat.jid) as id');
        $this->db->from('vrlv3_puljut_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_puljut_omistajat.tunnus');
        if($only_yp){
          $this->db->where('vrlv3_puljut_omistajat.taso', 1);

        }
        $this->db->where('jid', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function edit_pulju_user_rights($tunnus){
        
        
        $this->db->trans_start();
        
        $this->db->select('vrlv3_tunnukset.tunnus as omistaja, vrlv3_tunnukset.id as id, taso, count(vrlv3_puljut_omistajat.jid)');
        $this->db->from('vrlv3_puljut_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_puljut_omistajat.tunnus');
        $this->db->where('vrlv3_tunnukset.tunnus', $tunnus);
        $this->db->group_by('vrlv3_puljut_omistajat.jid');

        $query = $this->db->get();
        
        
        if ($query->num_rows() > 0)
        {
            $id = $query->result_array()[0]['id'];
            //henkilöllä on jaoksiin yp tai kalenterioikeuksia (tai molempia)
            //exclude pulju-yp (11) and puljuuduunari (12)
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>11));
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>12));
            
            $yp_done = false;
            $cal_done = false;
            foreach ($query->result_array() as $rivi){
                if ($rivi['taso'] == 1 && !$yp_done){
                    
                    $this->db->insert('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>11));
                    $yp_done = true;
                }
                else if ($rivi['taso'] == 0 && !$cal_done){
                    $this->db->insert('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>12));
                    $cal_done = true;
                }
                
            }
            
            


        } else {
            
            
           //otetaan puljuoikat pois
           $id = 0;

           $this->db->select('id');
           $this->db->from('vrlv3_tunnukset');
           $this->db->where('tunnus', $tunnus);
           $query = $this->db->get();
           
           if ($query->num_rows() > 0)
           {
               $id = $query->row_array()['id']; 
           }
            //exclude pulju-yp (11) and puljuduunari (12)
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>11));
            $this->db->delete('vrlv3_users_groups', array('user_id' => $id, 'group_id'=>12));


        }
        
        $this->db->trans_complete();

        return true;
        
        
        
    }

   
    function is_pulju_owner($pinnumber, $id, $taso = null)
    {
        $this->db->select('tunnus');
        $this->db->from('vrlv3_puljut_omistajat');
        $this->db->where('jid', $id);
        $this->db->where('tunnus', $pinnumber);
        if(isset($taso)){
            $this->db->where('taso', $taso);

        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    

}


