<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Hevonen_model extends Base_module_model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $genders = array(1=>'tamma', 2=>'ori', 3=>'ruuna');
    var $allowed_time_for_delete = 60*60*24; //24h
    var $allowed_time_for_delete_admin = 60*60*24*30; //30 pv
    
    function get_just_registered($nro){
        $date = new DateTime();
        $allowed_time = $this->allowed_time_for_delete;
        $date->setTimestamp(time() - $allowed_time);
        $oldest_possible = $date->format('Y-m-d H:i:s');

        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli,
                          IFNULL(kotitalli,'') as kotitalli, r.rotunro, syntymaaika, kuollut, h.porr_kilpailee, h.sakakorkeus, h.painotus as painotusid,
                          t.nimi as tallinimi, p.painotus, r.rotunro");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join('vrlv3_hevosrekisteri_omistajat as o', 'h.reknro = o.reknro');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_painotus as p", "h.painotus = p.pid", 'left outer');
        $this->db->join("vrlv3_tallirekisteri as t", "t.tnro = h.kotitalli", 'left outer');
        $this->db->where("rekisteroity >", $oldest_possible);
        $this->db->where('o.omistaja', $nro);
        $this->db->where('o.taso', 1);
        $this->db->where('h.hyvaksyi', $nro);
        $query=$this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        else {
            return array();
        }
        
    }
    
    
    function mass_insert_available(){
        $date = new DateTime();
        $allowed_time = 60*60*24*(364/2);
        $date->setTimestamp(time() - $allowed_time);
        $oldest_possible = $date->format('Y-m-d H:i:s');

        
        $this->db->select("*");
        $this->db->where("rekisteroity >", $oldest_possible);
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('hyvaksyi', $this->CI->ion_auth->user()->row()->tunnus);
        
        
        $query = $this->db->get();
        
        if ($query->num_rows() >= 10)
        {
            return true;
        }else {
            return false;
        }
        
    }
    
    
    
    function delete_hevonen($reknro, &$msg, $admin = false){
        $date = new DateTime();
        $allowed_time = $this->allowed_time_for_delete;
        if($admin){
            $allowed_time = $this->allowed_time_for_delete_admin;
        }
        $date->setTimestamp(time() - $allowed_time);
        $oldest_possible = $date->format('Y-m-d H:i:s');

        
        $this->db->select("*");
        $this->db->where("reknro", $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->where("rekisteroity >", $oldest_possible);
        $this->db->from('vrlv3_hevosrekisteri');
        
        if(!$admin){
            $this->db->where('hyvaksyi', $this->CI->ion_auth->user()->row()->tunnus);
        }
        
        $query = $this->db->get();
        $hevonen = array();
        
        if ($query->num_rows() > 0)
        {
            $this->db->where('reknro', $this->CI->vrl_helper->vh_to_number($reknro));
            $this->db->delete('vrlv3_hevosrekisteri');
            
            if($this->db->affected_rows() >0){
                return true;
             
            }else {
                $msg = "Mikäli hevosella on jälkeläisiä, sitä ei voi poistaa.";
;
                return false;
            }
        }else {
            if($admin){
                $msg = "Hevosta ei ole olemassa tai poistoajan takaraja on mennyt umpeen.";
            }else {
                $msg = "Jos et ole ylläpitäjä, voit poistaa ainoastaan itse rekisteröimiäsi hevosia 24h sisällä rekisteröinnistä.";
            }
            return false;
        }
        return true;
        
    }


    
    function get_hevonen($reknro)
    {
        
        $hevonen = array();
        $this->db->select("reknro, h.nimi as h_nimi, r.rotu as h_rotunimi, sukupuoli, sakakorkeus, syntymaaika, v.vari as h_varinimi, p.painotus as h_painotusnimi, syntymamaa, h.url as h_url, rekisteroity,
                          kotitalli, t.url as t_url, t.nimi as t_nimi, kuollut, kuol_pvm, rotunro, maa, vrlv3_lista_maat.id as maaid, vid, pid, kasvattajanimi, kasvattajanimi_id, kasvattaja_tunnus, kasvattaja_talli,
                          polv_tark, polv_tark_vrl, polv_pros, polv_tark_date, rekisteroity
                          ");
        $this->db->where("reknro", $this->CI->vrl_helper->vh_to_number($reknro));

        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_maat", "h.syntymamaa = vrlv3_lista_maat.id", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_lista_painotus as p", "h.painotus = p.pid", 'left outer');
        $this->db->join("vrlv3_tallirekisteri as t", "h.kotitalli = t.tnro", 'left outer');


        $query = $this->db->get();
        $hevonen = array();
        
        if ($query->num_rows() > 0)
        {
            $hevonen = $query->row_array();
            $query->result_array();
            
            $hevonen['reknro'] = $reknro;
            $hevonen['syntymaaika'] = $this->vrl_helper->sql_date_to_normal($hevonen['syntymaaika']);
            $hevonen['rekisteroity'] = $this->vrl_helper->sql_date_to_normal($hevonen['rekisteroity']);
            $hevonen['kuol_pvm'] = $this->vrl_helper->sql_date_to_normal($hevonen['kuol_pvm']);
            $hevonen['sukupuoli'] = $this->genders[$hevonen['sukupuoli']];
            if(isset( $hevonen['polv_tark_date'])){
                $hevonen['polv_tark_date'] = $this->vrl_helper->sql_date_to_normal($hevonen['polv_tark_date']);
            }
        }
        
        
        return $hevonen;        
        
       
    }
    
    function get_hevonen_edit($reknro)
    {
        
        $hevonen = array();
        $this->db->select("h.*, s.i_nro, s.e_nro, i.*, h.reknro as reknro");
        $this->db->where("h.reknro", $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_ikaantyminen as i", "i.reknro = h.reknro", 'left outer');                



        $query = $this->db->get();
        $hevonen = array();

        if ($query->num_rows() > 0)
        {
            $hevonen = $query->row_array();
        
        }
        
        
        return $hevonen;        
        
       
    }
    
    function get_hevonen_ages($reknro)
    {
        
        $hevonen = array();
        $this->db->select("*");
        $this->db->where("reknro", $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->from('vrlv3_hevosrekisteri_ikaantyminen');


        $query = $this->db->get();
        $hevonen = array();

        if ($query->num_rows() > 0)
        {
            $hevonen = $query->row_array();
        
        }
        
        
        return $hevonen;        
        
       
    }
    
    
    public function add_hevonen($hevonen, $tunnus, &$msg){
        unset ($hevonen['luin_saannot']);
        
        $suku = array();
        if(isset($hevonen['i_nro'])){
            $suku['i_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['i_nro']);
            unset($hevonen['i_nro']);
        }
        if(isset($hevonen['e_nro'])){
            $suku['e_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['e_nro']);
            unset($hevonen['e_nro']);

        }
        

        
        $hevonen['syntymaaika'] = $this->CI->vrl_helper->normal_date_to_sql($hevonen['syntymaaika']);
        if (isset($hevonen['kuol_pvm'])){
            $hevonen['kuol_pvm'] = $this->CI->vrl_helper->normal_date_to_sql($hevonen['kuol_pvm']);
        }
        
        if(isset($hevonen['kasvattaja_tunnus'])){
            $hevonen['kasvattaja_tunnus'] = $this->CI->vrl_helper->vrl_to_number($hevonen['kasvattaja_tunnus']);
        }
        
        $ikaantymistiedot = $this->_birthday_dates($hevonen);
        
        $vh_nro = $this->db->where(array("YEAR(rekisteroity)"=> date("Y"), "rotu"=>$hevonen['rotu'] ))->count_all_results("vrlv3_hevosrekisteri");
        if($vh_nro >= 9999){
            $msg = "Tälle rodulle on tänä vuonna rekisteröity jo 9999 hevosta. Rekisterinumerot ovat loppuneet. Ole yhteydessä ylläpitoon, tai odota vuoden vaihtumista.";
            return false;
                                    
        }
        
        
        $vh_ok = false;
        $vh = "";
        while ($vh_ok == false){
            $vh_nro = $vh_nro + 1;
            $vh = $this->CI->vrl_helper->generate_vh($hevonen['rotu'], $vh_nro);
            if ($this->CI->vrl_helper->check_vh_syntax($vh) === false){
                $msg = "Hevoselle generoitui virheellinen VH-tunnus (".$vh."), ilmoita tästä ylläpidolle. Kerro myös hevosesi rotu.";
                return false;
                break;
            }
            else if ($this->onko_tunnus($this->CI->vrl_helper->vh_to_number($vh)) == false){
                $vh_ok = true;
            }
            else if ($vh_nro >= 9999){
                $msg = "Virhe VH-numeron generoinnissa. Sopivaa ei saatu generoitua. Ole yhteydessä ylläpitoon. Kerro myös hevosesi rotu.";
                return false;
                break;
            }
        }

        $hevonen['reknro'] = $this->CI->vrl_helper->vh_to_number($vh);
        $suku['reknro'] = $this->CI->vrl_helper->vh_to_number($vh);
        

        
        
        //porrastettujen ominaisuudet
        $ominaisuudet = array();
        if(sizeof($suku)>0){
            $this->CI =& get_instance();
            $this->load->library('Porrastetut');
            
            $i = $suku['i_nro'] ?? null;
            $e = $suku['e_nro'] ?? null;
            
            $foals_traits = $this->porrastetut->get_foals_traitlist($i, $e);
            
            foreach ($foals_traits as $t_id=>$trait){
                $row = array();
                if($trait > 0){
                    $row['reknro'] = $hevonen['reknro'];
                    $row['ominaisuus'] = $t_id;
                    $row['arvo'] = $trait;
                    $ominaisuudet[]=$row;
                }      
                
            }
            
        }
        
        
        
        $hevonen['hyvaksyi'] = $tunnus;
        if($hevonen['kuollut']){
            $hevonen['kuol_merkkasi'] = $tunnus;
        }
        if(isset($hevonen['kasvattajanimi'])){
            $this->db->select('id');
            $this->db->from('vrlv3_kasvattajanimet');
            $this->db->where('kasvattajanimi', $hevonen['kasvattajanimi']);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                $hevonen['kasvattajanimi_id'] = $query->result_array()[0]['id'];
            }
        
        }
    
        $this->db->trans_start();
        $this->db->insert('vrlv3_hevosrekisteri', $hevonen);
        $this->add_owner_to_horse($hevonen['reknro'], $tunnus);
        
        if(sizeof($ikaantymistiedot)>0){
            $ikaantymistiedot['reknro'] = $hevonen['reknro'];
            $this->db->insert('vrlv3_hevosrekisteri_ikaantyminen', $ikaantymistiedot);
        }

        if(sizeof($suku)>0){
            $this->db->insert('vrlv3_hevosrekisteri_sukutaulut', $suku);

        }
        
        if(sizeof($ominaisuudet)>0){
            $this->db->insert_batch('vrlv3_hevosrekisteri_ominaisuudet', $ominaisuudet); 

        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE)
        {
            $msg     = "Jotain meni pieleen tietokantaan lisäämisessä. Ota yhteys ylläpitoon. Kerro mitä hevosta yritit rekisteröidä.";
            
            return false;
            }
        else {
            return $vh;
        }
        
        //

        
    
        
    }
    
    private function _birthday_dates(&$hevonen){
        $vuodet = array(3, 4, 5, 6, 7, 8);
        $ikaantymistiedot = array();
        if(isset($hevonen['ikaantyminen_d'])){
            $ikaantymistiedot['ikaantyminen_d'] = $hevonen['ikaantyminen_d'];
            unset($hevonen['ikaantyminen_d']);
        }
        if(isset($ikaantymistiedot['ikaantyminen_d']) && $ikaantymistiedot['ikaantyminen_d'] != 0){
            foreach($vuodet as $vuosi){
                $ikaantymistiedot[$vuosi.'vuotta'] = date('Y-m-d', strtotime($hevonen['syntymaaika']. ' + '.$vuosi*$ikaantymistiedot['ikaantyminen_d'].' days'));
                unset($hevonen[$vuosi.'vuotta']);
                                                           
            }
            
            
        }else {

            foreach($vuodet as $vuosi){
                if(isset($hevonen[$vuosi.'vuotta'])){
                    $ikaantymistiedot[$vuosi.'vuotta'] = $this->CI->vrl_helper->normal_date_to_sql($hevonen[$vuosi.'vuotta']);    
                    unset($hevonen[$vuosi.'vuotta']);
                }
                   
            }
        }
        return $ikaantymistiedot;
    }
    
      public function edit_hevonen($hevonen, $vh, &$msg){
        unset ($hevonen['luin_saannot']);

                  
        $hevonen['syntymaaika'] = $this->CI->vrl_helper->normal_date_to_sql($hevonen['syntymaaika']);
        if (isset($hevonen['kuol_pvm'])){
            $hevonen['kuol_pvm'] = $this->CI->vrl_helper->normal_date_to_sql($hevonen['kuol_pvm']);
        }

        $reknro = $this->CI->vrl_helper->vh_to_number($vh);
        
        $suku = array();
        if(isset($hevonen['i_nro'])){
            $suku['i_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['i_nro']);
            unset($hevonen['i_nro']);
        }
        if(isset($hevonen['e_nro'])){
            $suku['e_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['e_nro']);
            unset($hevonen['e_nro']);

        }
        

        if(isset($hevonen['kasvattajanimi'])){
            $this->db->select('id');
            $this->db->from('vrlv3_kasvattajanimet');
            $this->db->where('kasvattajanimi', $hevonen['kasvattajanimi']);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0)
            {
                $hevonen['kasvattajanimi_id'] = $query->result_array()[0]['id'];
            }
            else {
                $hevonen['kasvattajanimi_id'] = null;
            }
        
        }
        
        $ikaantymistiedot = $this->_birthday_dates($hevonen);
        if(sizeof($ikaantymistiedot) > 0) {
            $this->db->where('reknro', $reknro);
            $this->db->update('vrlv3_hevosrekisteri_ikaantyminen', $ikaantymistiedot);
        }

    
        $this->db->where('reknro', $reknro);
        $this->db->update('vrlv3_hevosrekisteri', $hevonen);
        $this->edit_suku($suku, $reknro);
        return true;
        //

        
    }
    
    
    public function edit_suku($hevonen, $vh){
        $reknro = $this->CI->vrl_helper->vh_to_number($vh);
        $suku = array();
        if(isset($hevonen['i_nro'])) {
            $suku['i_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['i_nro']);
        }
        if(isset($hevonen['e_nro'])) {
            $suku['e_nro'] = $this->CI->vrl_helper->vh_to_number($hevonen['e_nro']);
        }
        
        $this->db->select('reknro');
        $this->db->where("reknro", $this->CI->vrl_helper->vh_to_number($vh));
        $this->db->from('vrlv3_hevosrekisteri_sukutaulut');
        $query = $this->db->get();
        $hevonen = array();
        //echo $this->db->last_query();

        if ($query->num_rows() ==  0 && sizeof($suku) >0){
            $suku['reknro'] = $reknro;
            $this->db->insert('vrlv3_hevosrekisteri_sukutaulut', $suku);
        }
        else if (sizeof($suku) == 0) {
            $this->db->where('reknro', $reknro);
            $this->db->delete('vrlv3_hevosrekisteri_sukutaulut');

        }
        else {
            $this->db->where('reknro', $reknro);
            $this->db->update('vrlv3_hevosrekisteri_sukutaulut', array('i_nro' => $suku['i_nro']?? null, 'e_nro'=> $suku['e_nro'] ?? null));
            
        }
        
        return true;        
        //

        
    }
    
    
    
    
    
    public function onko_tunnus($tunnus){
        $this->db->where('reknro', $tunnus);
        $this->db->from('vrlv3_hevosrekisteri');
        $amount = $this->db->count_all_results();
        
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
        
    }
    
    public function onko_nimi($nimi, $rotu){
        $this->db->select('tunnus');
        $this->db->where('nimi', $nimi);
        $this->db->where('rotu', $rotu);
        $this->db->from('vrlv3_hevosrekisteri');
        $amount = $this->db->count_all_results();
        
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
    }
        
    public function get_hevonen_basic($reknro){
        $this->db->where('reknro', $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
       if ($query->num_rows() > 0)
        {
            return $query->result_array()[0];
        }
        
        else {
            return array();
        }
        
    }    
        
    
    
    function get_horse_owners($reknro)
    {
        $this->db->select('omistaja, nimimerkki, taso, CONCAT(vrlv3_hevosrekisteri_omistajat.omistaja, "/", vrlv3_hevosrekisteri_omistajat.reknro) as id');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_hevosrekisteri_omistajat.omistaja');
        $this->db->where('reknro', $this->CI->vrl_helper->vh_to_number($reknro));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_owners_horses($nro, $only_alive = false, $rotu = null)
    {
        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli,
                          IFNULL(kotitalli,'') as kotitalli, r.rotunro, syntymaaika, kuollut, h.porr_kilpailee, h.sakakorkeus, h.painotus as painotusid,
                          t.nimi as tallinimi, p.painotus, r.rotunro");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join('vrlv3_hevosrekisteri_omistajat as o', 'h.reknro = o.reknro');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_painotus as p", "h.painotus = p.pid", 'left outer');
        $this->db->join("vrlv3_tallirekisteri as t", "t.tnro = h.kotitalli", 'left outer');


        if(isset($rotu)){
            $this->db->where('h.rotu', $rotu);
        }
        if($only_alive){
             $this->db->where('h.kuollut', 0);
        }

        $this->db->where('o.omistaja', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    
    function get_owners_porrastettu_horses($nro, $rotu = null, $kotitalli = null, $minheight=null, $min_ika = null)
    {
        $this->db->select("h.reknro, h.nimi, rotu as rotunro, h.porr_kilpailee, h.sakakorkeus, h.kotitalli, h.painotus, i.*");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join('vrlv3_hevosrekisteri_omistajat as o', 'h.reknro = o.reknro');
        $this->db->join('vrlv3_hevosrekisteri_ikaantyminen as i', 'h.reknro = i.reknro');
        $this->db->where('h.kuollut', 0);
        $this->db->where('h.porr_kilpailee', 1);

        if(isset($rotu)){
            $this->db->where('h.rotu', $rotu);
        }
        
        if(isset($kotitalli)){
            $this->db->where('h.kotitalli', $kotitalli);
        }
        
        if(isset($min_ika)){
            $vuodet = array(3,4,5,6,7,8);
            
            foreach($vuodet as $vuosi){
                if($vuosi == $min_ika || $vuosi > $min_ika){
                    
                    $this->db->where('i.'.$vuosi.'vuotta <=', date("Y-m-d"));
                    break;
                }
            }
        }
        
        if(isset($minheight)){
            $this->db->where('h.sakakorkeus >', $minheight);
        }else {
            $this->db->where('h.sakakorkeus >', 10);
        }

        $this->db->where('o.omistaja', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_owners_breeds($nro)
    {
        $this->db->select("distinct(r.rotunro) as rotunro, r.lyhenne");
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join('vrlv3_hevosrekisteri_omistajat as o', 'h.reknro = o.reknro');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro");
        $this->db->where('o.omistaja', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_owners_horses_amount($nro, $alive = 1)
    {
                $this->db->select('COUNT(vrlv3_hevosrekisteri.reknro) as kaikki,
    COUNT(CASE WHEN kuollut = 0 then 1 ELSE NULL END) as elossa');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->join('vrlv3_hevosrekisteri', 'vrlv3_hevosrekisteri.reknro = vrlv3_hevosrekisteri_omistajat.reknro');
        $this->db->where('omistaja', $nro);
                $query = $this->db->get();

         if ($query->num_rows() > 0)
        {
            return $query->result_array()[0];
        }
        
        return array();
    }
    
        function get_users_foals_full($user, $admin = false){
            
            $stables = array();
            
            
            $this->db->select('vrlv3_tallirekisteri.tnro');
            $this->db->from('vrlv3_tallirekisteri');
            $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
            $this->db->where('omistaja', $user);
            if(!$admin){
                $this->db->where('lopettanut', 0);

            }
            $query = $this->db->get();
            foreach($query->result_array() as $talli){
                $stables[] = $talli['tnro'];
            }
            
            $names = array();
            
            $this->db->select('vrlv3_kasvattajanimet.id');
            $this->db->from('vrlv3_kasvattajanimet');
            $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
            $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $user);
            $query = $this->db->get();
            
            foreach($query->result_array() as $nimi){
                $names[] = $nimi['id'];
            }
        
            $this->db->select("reknro, nimi, r.lyhenne as rotu, vari, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli, syntymaaika,
                              IFNULL(kasvattaja_talli, '') as kasvattaja_talli, IFNULL(kasvattaja_tunnus, '') as kasvattaja_tunnus, IFNULL(kasvattajanimi, '') as kasvattajanimi");        
            $this->db->from('vrlv3_hevosrekisteri as h');
            $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
    
            $this->db->where('kasvattaja_tunnus', $user);
            if(sizeof($stables)>0){
            $this->db->or_where_in('kasvattaja_talli', $stables);
            }
            if(sizeof($names)>0){
            $this->db->or_where_in('kasvattajanimi_id', $names);
            }

        
        
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
     function get_users_foals_full_amount($user){
            
            $stables = array();
            
            
            $this->db->select('vrlv3_tallirekisteri.tnro');
            $this->db->from('vrlv3_tallirekisteri');
            $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
            $this->db->where('omistaja', $user);
            
            $query = $this->db->get();
            foreach($query->result_array() as $talli){
                $stables[] = $talli['tnro'];
            }
            
            $names = array();
            
            $this->db->select('vrlv3_kasvattajanimet.id');
            $this->db->from('vrlv3_kasvattajanimet');
            $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
            $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $user);
            $query = $this->db->get();
            
            foreach($query->result_array() as $nimi){
                $names[] = $nimi['id'];
            }
        
            $this->db->select("Count(*) as kpl");        
            $this->db->from('vrlv3_hevosrekisteri as h');    
            $this->db->where('kasvattaja_tunnus', $user);
            if(sizeof($stables)>0){
            $this->db->or_where_in('kasvattaja_talli', $stables);
            }
            if(sizeof($names)>0){
            $this->db->or_where_in('kasvattajanimi_id', $names);
            }

        
        
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]['kpl']; 
        }
        
        return 0;
    }
    
    
    function get_users_foals($user){
        $this->db->select("reknro, nimi, r.lyhenne as rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');

        $this->db->where('kasvattaja_tunnus', $user);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    
    function get_names_foals_by_id($id){
        $this->db->select("reknro, nimi, r.lyhenne as rotu, vari, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->join("vrlv3_lista_rodut as r", "vrlv3_hevosrekisteri.rotu = r.rotunro", 'left outer');

        $this->db->where('kasvattajanimi_id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    function get_names_foals_by_name($name){
        $this->db->select("reknro, nimi, rotu, vari, sukupuoli, syntymaaika");        
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi', $name);
        $this->db->where('kasvattajanimi_id IS NULL', NULL, FALSE);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
     function get_horses_newest()
    {
        
        $date = new DateTime();
        $date->setTimestamp(time() - 30*24*60*60);
        $oldest_possible = $date->format('Y-m-d H:i:s');
        
        
        $this->db->select("vrlv3_hevosrekisteri.reknro, nimi, syntymaaika, rekisteroity, hyvaksyi, r.lyhenne as rotu, kuollut, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli");
        $this->db->join("vrlv3_lista_rodut as r", "vrlv3_hevosrekisteri.rotu = r.rotunro", 'left');
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('rekisteroity >', $oldest_possible);
        $this->db->order_by('rekisteroity', 'desc');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
      function get_stables_horses($nro, $only_alive = false)
    {
        $this->db->select("vrlv3_hevosrekisteri.reknro, nimi, syntymaaika, r.lyhenne as rotu, kuollut, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli");
        $this->db->join("vrlv3_lista_rodut as r", "vrlv3_hevosrekisteri.rotu = r.rotunro", 'left');

        $this->db->from('vrlv3_hevosrekisteri');
                    $this->db->where('kotitalli', $nro);

        if($only_alive){
            $this->db->where('kuollut', 0);

        }
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function get_horses_foals($nro)
    {
        $nro = $this->CI->vrl_helper->vh_to_number($nro);
        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, v.lyhenne as vari, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli, syntymaaika");
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
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
        function is_horse_owner($pinnumber, $id)
    {
        $this->db->select('omistaja');
        $this->db->from('vrlv3_hevosrekisteri_omistajat');
        $this->db->where('reknro', $id);
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function get_stables_foals($user){
        $this->db->select("vrlv3_hevosrekisteri.reknro, nimi, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli,
                          syntymaaika, CONCAT('VRL-', omistaja) as omistaja, r.lyhenne as rotu, kuollut");
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->join("vrlv3_lista_rodut as r", "vrlv3_hevosrekisteri.rotu = r.rotunro", 'left');
        $this->db->join('vrlv3_hevosrekisteri_omistajat', 'vrlv3_hevosrekisteri_omistajat.reknro = vrlv3_hevosrekisteri.reknro', 'left' );
        $this->db->where('kasvattaja_talli', $user);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    //functions for search
    function search_horse($reknro = null, $name = null, $rotu=-1, $gender=-1, $dead=null, $color=-1, $birthyear=null){
        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, h.vari, IF(sukupuoli='1', 'tamma', IF(sukupuoli='2', 'ori', 'ruuna')) as sukupuoli, syntymaaika");
        
        //jos haetaan rekisterinumerolla, millään muulla ei ole väliä
        if (strlen($reknro) > 5){
            $this->db->where("reknro", $reknro);
        }
        else {
            if (isset($dead) && $dead == 1){
                $this->db->where("kuollut", 1);
            }
            else if (isset($dead))
            { $this->db->where("kuollut", 0);}
            
            if($rotu > -1){
                $this->db->where("h.rotu", $rotu);
            }
            if($color > -1){
                $this->db->where("vari", $color);
            }
            if($gender > -1){
                $this->db->where("sukupuoli", $gender);
            }
            if(isset($birth_year) && $birth_year > 1000){
                $this->db->where("syntymaaika <", ($birth_year+1)."-01-01");
                $this->db->where("syntymaaika >", ($birth_year-1)."-12-31");
            }
            
            if(isset($breeder)){
                $this->db->where("kasvattaja_tunnus", $breeder);
            }
        
            if(!empty($name))
            {
                if(strpos($name, '*') !== false)
                    $this->db->where('nimi LIKE "' . str_replace('*', '%', $name) . '"');
                else
                    $this->db->where('nimi', $name);
            }
        }
        
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');

        $this->db->limit(1000);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
        
    }
    
    
    function add_owner_to_horse($id, $applicant, $taso = 1)
    {
        $data = array('reknro' => $id, 'omistaja' => $applicant, 'taso' => $taso);
        
        $this->db->insert('vrlv3_hevosrekisteri_omistajat', $data);
    }
    
    //functions for pedigree
    
    function get_suku($reknro, $tunnus ="", &$suku){
        
        //turvamekanismi, jos on vahingossa joku asettanut hevosen isovanhemmakseen tms...
        if (strlen($tunnus) < 11){
            $hevonen = $this->_get_suku_info($reknro);
        
            if($tunnus != ""){
                $suku[$tunnus] = $hevonen;
            }
            
            if (isset($hevonen["i_nro"])){
                $this->get_suku($this->CI->vrl_helper->get_vh($hevonen["i_nro"]), $tunnus."i", $suku);
            }
            
            if (isset($hevonen["e_nro"])){
                $this->get_suku($this->CI->vrl_helper->get_vh($hevonen["e_nro"]), $tunnus."e", $suku);
            }
        }
        
    }
    
    function get_unelmasuku($i_nro, $e_nro, &$suku){
        
            if ($i_nro){
                $this->get_suku($i_nro, "i", $suku);
            }
            
            if ($e_nro){
                $this->get_suku($e_nro, "e", $suku);
            }
        
        
    }
    
    //porrastetut
    function get_horse_traits($reknro)
    {
        $this->db->select('m.reknro, l.id, l.ominaisuus, m. arvo');
        $this->db->from('vrlv3_hevosrekisteri_ominaisuudet as m');
        $this->db->join('vrlv3_lista_ominaisuudet as l', 'l.id = m.ominaisuus');
        $this->db->where('m.reknro', $this->CI->vrl_helper->vh_to_number($reknro));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
       //porrastetut
    function get_horse_sport_info_by_jaos($reknro)
    {
        $this->db->select('*');
        $this->db->from('vrlv3_hevosrekisteri_kisatiedot');
        $this->db->where('reknro', $this->CI->vrl_helper->vh_to_number($reknro));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $result = array();
            foreach($query->result_array() as $row){
                $result[$row['jaos']] = $row;
            }
            return $result;
        }
        
        return array();
    }
    
    //stats
    function get_stats_breed($rotu){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("rotu", $rotu);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
    
    
    function get_stats_colour($color){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("vari", $color);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
    
    function get_stats_sport($sport){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("painotus", $sport);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
    
     
    function count_breedingname_amount($id){
        
        $this->db->select("COUNT(kasvattajanimi_id) as amount");
        $this->db->where("kasvattajanimi_id", $id);
  
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $breedingnames = $query->row()->amount;

        return $breedingnames;
        
    }
    
    
        
    function get_stats_country($country){
        
        $this->db->select("COUNT(reknro) as amount, sukupuoli");
        $this->db->where("syntymamaa", $country);
        $this->db->group_by("sukupuoli");
        
        $this->db->from('vrlv3_hevosrekisteri');
        $query = $this->db->get();
        
        $genders = $this->sort_gender_stats($query->result_array());

        return $genders;
        
    }
        
        
    private function sort_gender_stats($array){
        $genders = array();
        $genders['tammat'] = 0;
        $genders['orit'] = 0;
        $genders['ruunat'] = 0;
        
        foreach ($array as $row){
            if($row['sukupuoli'] == 1){
                $genders['tammat'] = $row['amount'];
            }
            if($row['sukupuoli'] == 2){
                $genders['orit'] = $row['amount'];
            }
            if($row['sukupuoli'] == 3){
                $genders['ruunat'] = $row['amount'];
            }
        }
        
        $genders['total'] = $genders['tammat'] + $genders['orit'] + $genders['ruunat'];
        
        return $genders;
        
    }
    
    function get_stats_year_list($year = null){
        
        if(isset($year)){
            $this->db->select("COUNT(reknro) as amount, r.rotu as rotu, r.lyhenne as lyhenne, r.rotunro as rotunro");
            $this->db->from('vrlv3_hevosrekisteri as h');
            $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro");
            $this->db->group_by("h.rotu");     

            $this->db->where("YEAR(rekisteroity)", $year);
        }
        else {
            $this->db->select("COUNT(reknro) as amount, YEAR(rekisteroity) as year");
            $this->db->group_by("YEAR(rekisteroity)");     
            $this->db->from('vrlv3_hevosrekisteri');
        }
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    function get_stats_breed_year_list($breed){
        
        $this->db->select("COUNT(reknro) as amount, YEAR(rekisteroity) as year");
        $this->db->group_by("YEAR(rekisteroity)");     
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('rotu', $breed);
        
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    function get_stats_name_year_list($name){
        
        $this->db->select("COUNT(reknro) as amount, YEAR(syntymaaika) as year");
        $this->db->group_by("YEAR(syntymaaika)");     
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('kasvattajanimi_id', $name);
        
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    function get_stats_country_year_list($country){
        
        $this->db->select("COUNT(reknro) as amount, YEAR(rekisteroity) as year");
        $this->db->group_by("YEAR(rekisteroity)");     
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('syntymamaa', $country);
        
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    function get_stats_sport_year_list($country){
        
        $this->db->select("COUNT(reknro) as amount, YEAR(rekisteroity) as year");
        $this->db->group_by("YEAR(rekisteroity)");     
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('painotus', $country);
        
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    function get_stats_color_year_list($color){
        
        $this->db->select("COUNT(reknro) as amount, YEAR(rekisteroity) as year");
        $this->db->group_by("YEAR(rekisteroity)");     
        $this->db->from('vrlv3_hevosrekisteri');
        $this->db->where('vari', $color);
        
        $query = $this->db->get();
        
        
        return $query->result_array();

        
    }
    
    
    

    private function _get_suku_info($reknro){
        $this->db->select("h.reknro, h.nimi, r.lyhenne as rotu, v.lyhenne as vari, v.vid as vid, sukupuoli, sakakorkeus, i_nro, e_nro");
        $this->db->where("h.reknro", $this->CI->vrl_helper->vh_to_number($reknro));
        $this->db->from('vrlv3_hevosrekisteri as h');
        $this->db->join("vrlv3_lista_rodut as r", "h.rotu = r.rotunro", 'left outer');
        $this->db->join("vrlv3_lista_varit as v", "h.vari = v.vid", 'left outer');
        $this->db->join("vrlv3_hevosrekisteri_sukutaulut as s", "s.reknro = h.reknro", 'left outer');
        
        $query = $this->db->get();
        $array = $query->row_array();
        $array['reknro'] = $this->CI->vrl_helper->get_vh($array['reknro']);
        return $array;
    }
    

       public function get_country_list(){
        
        $this->db->select('id, maa, lyh');
        $this->db->from('vrlv3_lista_maat');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    public function get_country($id){
        
        $this->db->select('id, maa, lyh');
        $this->db->from('vrlv3_lista_maat');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0];
        }
        
        return array();
                
    }
    
    
    
    public function get_breed_list(){
        
        $this->db->select('rotunro, rotu, lyhenne, roturyhma, harvinainen');
        $this->db->from('vrlv3_lista_rodut');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
                
    }
    
    
    ////functions for form option lists
    
    
    public function get_breed_option_list(){
        $data = array();
        
        $this->db->select('rotunro, rotu');
        $this->db->from('vrlv3_lista_rodut');
        $this->db->order_by("rotu", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['rotunro']] = $row['rotu'];
            }
        }
        
        return $data;
    }
    

    
	public function get_gender_option_list(){
        return $this->genders;
    }
    
	public function get_color_option_list(){
        $data = array();
        
        $this->db->select('vid, vari');
        $this->db->from('vrlv3_lista_varit');
        $this->db->order_by("vari", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['vid']] = $row['vari'];
            }
        }
        
        return $data;
        
    }
    
    
    public function get_skill_option_list(){
        $data = array();
        
        $this->db->select('pid, painotus');
        $this->db->from('vrlv3_lista_painotus');
        $this->db->order_by("painotus", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['pid']] = $row['painotus'];
            }
        }
        
        return $data;
        
    }
    
    
    public function get_country_option_list(){
        $data = array();
        
        $this->db->select('id, maa');
        $this->db->from('vrlv3_lista_maat');
        $this->db->order_by("maa", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['id']] = $row['maa'];
            }
        }
        
        return $data;
        
    }
	
    
    
    
}

