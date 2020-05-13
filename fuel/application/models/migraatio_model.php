<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Migraatio_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    
    
    
     //Applications
    function migrate_tiedotukset()
    {

        $this->db->select("kategoria");
        $this->db->distinct();
        $query = $this->db->get('tiedotukset_kategoriat');
        
        foreach ($query->result() as $row){
            
            if (!$this->it_is_there_already('vrlv3_lista_tiedotuskategoriat', array('kategoria'=> $row->kategoria))){
                $this->db->insert('vrlv3_lista_tiedotuskategoriat', array("kategoria" => $row->kategoria ));
            }
        }
        
        $query = $this->db->get('tiedotukset');
        foreach ($query->result() as $row)
        {
        
            if (!$this->it_is_there_already('vrlv3_tiedotukset', array('aika' => $row->aika, 'otsikko' => $row->otsikko, 'lahettaja' => $row->lahettaja))){
                
                $data_tiedotus['aika']= $row->aika;
                $data_tiedotus['otsikko']= $row->otsikko;
                $data_tiedotus['teksti']= $row->teksti;
                $data_tiedotus['lahettaja']= $row->lahettaja;
                $data_tiedotus['julkinen']= $row->julkinen;
                $data_tiedotus['muokkaaja']= $row->muokkaaja;
                $data_tiedotus['muokpvm']= $row->muokpvm;
                
                if (!$this->onko_tunnus($row->lahettaja)){
                    $data_tiedotus['lahettaja'] = 00000;
                }
                else {
                    $data_tiedotus['lahettaja'] = $row->lahettaja;
                }
            
                if (!$this->onko_tunnus($row->muokkaaja)){
                    $data_tiedotus['muokkaaja'] = 00000;
                }
                else {
                    $data_tiedotus['muokkaaja'] = $row->muokkaaja;
                }
                
                $this->db->insert('vrlv3_tiedotukset', $data_tiedotus);
            }
            
            //haetaan sen käsitellyn tid
            $this->db->select("tid");
            $this->db->where(array('aika' => $row->aika, 'otsikko' => $row->otsikko));
            $tid_query = $this->db->get('vrlv3_tiedotukset');
            $tid = $tid_query->row()->tid;
                
            //Insertataan kaikki kys. tiedotuksen kategoriat
            $this->db->select("kategoria");
            $this->db->where('tid', $row->tid);
            $query2 = $this->db->get('tiedotukset_kategoriat');
                
            foreach ($query2->result() as $row2)
            {
                //haetaan jokaiselle kategorialle ID
                $this->db->select("kid");
                $this->db->where('kategoria', $row2->kategoria);
                $query3 = $this->db->get('vrlv3_lista_tiedotuskategoriat');
                                
                $data_tiedotus_kategoria = array();
                $data_tiedotus_kategoria['kid'] = $query3->row()->kid;
                $data_tiedotus_kategoria['tid'] = $tid;
                
                if (!$this->it_is_there_already('vrlv3_tiedotukset_kategoriat', $data_tiedotus_kategoria)){
                    $this->db->insert('vrlv3_tiedotukset_kategoriat', $data_tiedotus_kategoria);
                }
                
            }               
                             

        }
        
       
        
        
    }
    
    

 
 
    //Applications
    function migrate_tunnukset()
    {
        //VRLV3_TUNNUKSET
        $query = $this->db->query("SELECT * from tunnukset WHERE NOT EXISTS(SELECT * FROM vrlv3_tunnukset WHERE tunnukset.tunnus = vrlv3_tunnukset.tunnus)");
        foreach ($query->result() as $row)
        {

            //Data vrlvä_tunnukset tauluun
            $data['email'] = $row->email;
            $data['tunnus'] = $row->tunnus;

            if ($this->it_is_there_already('vrlv3_tunnukset', array('tunnus'=>$data['tunnus']))){
                echo "tunnus";
                continue;
            }
            $data['ip_address'] = $row->rek_ip;
            $data['username'] = $row->tunnus;
            $data['password'] = $row->salasana;
            $data['created_on'] = $row->rekisteroitynyt;
            $data['active'] = 1;
            $data['nimimerkki'] = $row->nimimerkki;

            if($row->nayta_email == 1 || $row->nayta_email == 2)
                $data['nayta_email'] = 0;
            else
                $data['nayta_email'] = 1;
                
            if ($this->it_is_there_already('vrlv3_tunnukset', array('email'=>$data['email']))){                
                if (strlen($data['email']) == 0){
                    $data['email'] = $row->tunnus. "@tämä_puuttui_kokonaan_vanhasta.fuuu";
                }
                
                else {
                    $data['email'] =  "TUPLAMAILIOSOITE:" . $data['email'];
                }
            }
            
            $data['jaahylla'] = $row->jaahylla;
            $data['frozen'] = $row->frozen;
            $data['reason'] = $row->reason;            
            $data['hyvaksytty'] = $row->hyvaksytty;
            
            if (!$this->onko_tunnus($row->hyvaksyi)){
                $data['hyvaksyi'] = 00000;
            }
            else {
                $data['hyvaksyi'] = $row->hyvaksyi;
            }
            
            if (!$this->onko_tunnus($row->tunnus)) {
                $this->db->insert('vrlv3_tunnukset', $data);
                $this->_add_tunnus_data($row->tunnus);
            }
            else {
                $this->db->where('tunnus', $row->tunnus);
                $this->db->update('vrlv3_tunnukset', $data);
            }
                     
            
        }
        
        
    }
    
    private function _add_tunnus_data($tunnus){
        
        
        //VRLV3_TUNNUKSET_KIRJAUTUMISET
        $this->db->select("*");
        $this->db->where('tunnus', $tunnus);
        $this->db->from('tunnukset_epa');
        $query = $this->db->get();      
        $this->vrlv3_tunnukset_kirjautumiset($query, 0);
        
        $this->db->where('tunnus', $tunnus);
        $this->db->from('tunnukset_kirjautunut');
        $query = $this->db->get();
        $this->vrlv3_tunnukset_kirjautumiset($query, 1);
        
        
            
        //VRLV3_TUNNUKSET_NIKIT
        $this->db->select("*");
        $this->db->where('tunnus', $tunnus);
        $this->db->from('tunnukset_nimimerkit');
        $query = $this->db->get();
        
        foreach ($query->result() as $row)
        {
            $data_nick['tunnus'] = $row->tunnus;
            $data_nick['nimimerkki'] = $row->nimimerkki;
            $data_nick['vaihtanut'] = $row->vaihtanut;
            $data_nick['piilotettu'] = $row->piilotettu;
        
            
            $this->db->where(array('tunnus'=>$row->tunnus, 'vaihtanut'=>$row->vaihtanut));
            $this->db->from('vrlv3_tunnukset_nimimerkit');
            $amount = $this->db->count_all_results();
        
            if ($amount == 0){
                $this->db->insert('vrlv3_tunnukset_nimimerkit', $data_nick);              
            }
        }
        
       
        $this->db->select("*");
        $this->db->where('tunnus', $tunnus);
        $this->db->from('tunnukset_yhteystiedot');
        $query = $this->db->get();
        foreach ($query->result() as $row)
        {
            if ($this->onko_tunnus($row->tunnus)) { 
                $data_contact['tunnus'] = $row->tunnus;
                $data_contact['tyyppi'] = $row->tyyppi;
                $data_contact['tieto'] = $row->tieto;
                
                $this->db->where($data_contact);
                $this->db->from('vrlv3_tunnukset_yhteystiedot');
                $amount = $this->db->count_all_results();
        
                if ($amount == 0){
                    $this->db->insert('vrlv3_tunnukset_yhteystiedot', $data_contact);              
                }
            }
        }

    }
    
    public function migrate_pikaviestit(){
         //PIKAVIESTIT
        $query = $this->db->get('tunnukset_pikaviestit');
        foreach ($query->result() as $row)
        {
            if ($this->onko_tunnus($row->vastaanottaja)) {                           
                if (!$this->onko_tunnus($row->lahettaja)){
                    $data_message['lahettaja'] = 00000;
                }
                else {
                    $data_message['lahettaja'] = $row->lahettaja;
                }
                $data_message['vastaanottaja'] = $row->vastaanottaja;
                $data_message['aika'] = $row->aika;
                $data_message['viesti'] = $row->viesti;
                $data_message['luettu'] = $row->luettu;
                $data_message['tarkea'] = $row->tarkea;
                
                $this->db->where($data_message);
                $this->db->from('vrlv3_tunnukset_pikaviestit');
                $amount = $this->db->count_all_results();
        
                if ($amount === 0){
                    $this->db->insert('vrlv3_tunnukset_pikaviestit', $data_message);              
                }
            }
        }
    }
    
    public function migrate_tallit(){
        
        
        $query = $this->db->query("SELECT * from tallirekisteri WHERE NOT EXISTS(SELECT * FROM vrlv3_tallirekisteri WHERE tallirekisteri.tnro = vrlv3_tallirekisteri.tnro)");
        foreach ($query->result() as $row)
        {
            $data_talli['tnro'] = $row->tnro;
            $data_talli['nimi'] = $row->nimi;
            $data_talli['url'] = $row->url;
            $data_talli['kuvaus'] = $row->kuvaus;
            $data_talli['perustettu'] = $row->perustettu;
            $data_talli['hyvaksyi'] =  $this->clean_tunnus($row->hyvaksyi);
            $data_talli['piilotettu'] = $row->piilotettu;
            $data_talli['hyvaksytty'] = $row->hyvaksytty;
            

            //LOPETUS            
            $this->db->select("*");
            $this->db->from("tallirekisteri_lopettaneet");
            $this->db->where("tnro", $row->tnro);
            $query2 = $this->db->get();  
            $row2 = $query2->row();

            if (isset($row2)){          
                $data_talli['lopettanut'] = 1;
                $data_talli['lopetti_pvm'] = $row2->lopetti;
                $data_talli['lopetti_tunnus'] = $this->clean_tunnus($row2->merkitsi);
                
            }
            else {
                $data_talli['lopettanut'] = 0;
                $data_talli['lopetti_pvm'] = "0000-00-00 00:00:00";
                $data_talli['lopetti_tunnus'] = "00000";
            
            }
            
            

            
            $this->db->insert('vrlv3_tallirekisteri', $data_talli);
            
            $this->_add_tallikategoriat($row->tnro);
            $this->_add_tallinomistajat($row->tnro);

        }        
        
        
    }
    
    private function _add_tallikategoriat($tnro){
        $this->db->select("*");
        $this->db->from("tallirekisteri_kategoriat");
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        foreach ($query->result() as $row){
            //onko kategoria olemassa
            if (!$this->it_is_there_already('vrlv3_lista_tallikategoriat', array('kat'=>$row->kategoria))){
                continue;
            }
            //onko kategoriat jo kasassa
            if ($this->it_is_there_already('vrlv3_tallirekisteri_kategoriat', array('tnro'=>$row->tnro, 'kategoria'=>$row->kategoria))){
                continue;
            }
            
            $data_kat['tnro'] = $row->tnro;
            $data_kat['anoi'] = $this->clean_tunnus($row->anoi);
            $data_kat['kategoria'] = $row->kategoria;
            $data_kat['hyvaksyi'] = $this->clean_tunnus($row->tarkistaja);
            $data_kat['lisatty'] = $row->lisatty;
            $data_kat['kasitelty'] = $row->kasitelty;
            
            $this->db->insert('vrlv3_tallirekisteri_kategoriat', $data_kat); 
            
        }
    }
    
    
        private function _add_tallinomistajat($tnro){
        $this->db->select("*");
        $this->db->from("tallirekisteri_omistajat");
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        foreach ($query->result() as $row){
            //onko talli tai tunnus olemassa
            if (!$this->onko_tunnus($row->omistaja)){
                continue;
            }
            //onko omistaja jo merkitty
            if ($this->it_is_there_already('vrlv3_tallirekisteri_omistajat', array('tnro'=>$row->tnro, 'omistaja'=>$row->omistaja))){
                continue;
            }
            
            $data_kat['tnro'] = $row->tnro;
            $data_kat['omistaja'] = $row->omistaja;
            $data_kat['taso'] = $row->taso;
            
            $this->db->insert('vrlv3_tallirekisteri_omistajat', $data_kat);
            
        }
    }
    
    public function count_kaakit(){
        $old = $this->db->count_all_results('hevosrekisteri_perustiedot');
        $new = $this->db->count_all_results('vrlv3_hevosrekisteri');
        return $old-$new;
    }
    
    
    
     public function migrate_hevoset($limit1 = 0, $limit2 = NULL){
        $query = null;
        
        if (isset($limit2)){
            $this->db->order_by("reknro", "asc");
            $this->db->limit($limit1, $limit2);
            $query = $this->db->get('hevosrekisteri_perustiedot');
        }       
        
        else {
            $query = $this->db->query("SELECT * from hevosrekisteri_perustiedot WHERE NOT EXISTS(SELECT * FROM vrlv3_hevosrekisteri WHERE hevosrekisteri_perustiedot.reknro = vrlv3_hevosrekisteri.reknro)");
        }
        
        foreach ($query->result() as $row)
        {
            if ($this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->reknro))){
                continue;
            }

            $data['reknro'] = $row->reknro;
            $data['nimi'] = $row->nimi;
            $data['rotu'] = $row->rotu;
            
            $skp = array("tamma"=>1, "ori" => 2, "ruuna" => 3);
            
            $data['sukupuoli'] = $skp[$row->sukupuoli];
            $data['sakakorkeus'] = $row->sakakorkeus;
            $data['syntymaaika'] =  $row->syntymaaika;

            $data['url'] = $row->url;
            $data['rekisteroity'] = $row->rekisteroity;
            $data['hyvaksyi'] = $this->clean_tunnus($row->hyvaksyi);
            $data['kotitalli'] = $this->clean_tallitunnus($row->kotitalli);
            $data['kuollut'] = $row->kuollut;
            
            $this->db->select("*");
            $this->db->from("hevosrekisteri_lisatiedot");
            $this->db->where("reknro", $row->reknro);
            $query2 = $this->db->get();

            $lisatiedot = $query2->row();
            
            if(isset($lisatiedot)){
            
                $color = $this->check_color($lisatiedot->vari);
                if (isset($color)){          
                        $data['vari'] = $color;
                }
                
                $painotus = $lisatiedot->painotus;
                if($painotus !="0"){
                    if ($this->it_is_there_already('vrlv3_lista_painotus', array('pid'=>$painotus))){
                    $data['painotus'] = $painotus;
                    }
                }
                
                $syntmaa = $lisatiedot->syntymamaa;
                if($syntmaa !="0"){
                    if ($this->it_is_there_already('vrlv3_lista_maat', array('id'=>$syntmaa))){
                    $data['syntymamaa'] = $syntmaa;
                    }
                }
            }

    
            $this->db->insert('vrlv3_hevosrekisteri', $data); 
        }        
        
        
    }
    
    
    public function migrate_kuolleet(){
        $query = $this->db->get('hevosrekisteri_kuolleet');
        foreach ($query->result() as $row)
        {
            if ($this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->reknro, 'kuollut'=>1, 'kuol_merkkasi'=>$row->merkkasi, 'kuol_pvm'=>$row->kuoli))){
                continue;
            }

            $data['kuollut'] = 1;
            $data['kuol_merkkasi'] = $this->clean_tunnus($row->merkkasi);
            $data['kuol_pvm'] = $row->kuoli;
            
            $this->db->where('reknro', $row->reknro);
            $this->db->update('vrlv3_hevosrekisteri', $data);
            
        }        
        
        
    }
    
        public function migrate_hevosenomistajat(){
        $query = $this->db->query("SELECT * from hevosrekisteri_omistajat WHERE NOT EXISTS(SELECT * FROM vrlv3_hevosrekisteri_omistajat WHERE hevosrekisteri_omistajat.reknro = vrlv3_hevosrekisteri_omistajat.reknro)");
        foreach ($query->result() as $row){
            //onko hevo tai tunnus olemassa
            if (!$this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->reknro))||!$this->onko_tunnus($row->omistaja)){
                continue;
            }
            //onko omistajuus jo olemassa
            if ($this->it_is_there_already('vrlv3_hevosrekisteri_omistajat', array('reknro'=>$row->reknro, 'omistaja'=>$row->omistaja))){
                continue;
            }
            
            $data_kat['reknro'] = $row->reknro;
            $data_kat['omistaja'] = $row->omistaja;
            $data_kat['taso'] = $row->taso;
            
            $this->db->insert('vrlv3_hevosrekisteri_omistajat', $data_kat);
            
        }
    }
    
    
    public function migrate_hevosuvut(){
        $query = $this->db->query("SELECT * from hevosrekisteri_sukutaulut WHERE NOT EXISTS(SELECT * FROM vrlv3_hevosrekisteri_sukutaulut WHERE hevosrekisteri_sukutaulut.reknro = vrlv3_hevosrekisteri_sukutaulut.reknro)");
        foreach ($query->result() as $row){
            //onko hevo olemassa
            if (!$this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->reknro))){
                continue;
            }
            //onks suku jo laitettu
            if ($this->it_is_there_already('vrlv3_hevosrekisteri_sukutaulut', array('reknro'=>$row->reknro))){
                continue;
            }
            
            $ok = false;
            $data = array("reknro"=>$row->reknro, "i_nro" => null, "e_nro" => null);
            
            //tsek papa
            //onko omistajuus jo olemassa
            if ($this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->i_nro))){
                $data["i_nro"] = $row->i_nro;
                $ok = true;
            }
            
            if ($this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->e_nro))){
                $data["e_nro"] = $row->e_nro;
                $ok = true;
            }
            
            if($ok){
                
                $this->db->insert('vrlv3_hevosrekisteri_sukutaulut', $data);
            }
            
            
        }
    }
    
        public function migrate_kasvattajatiedot(){
        $query = $this->db->query("SELECT * from hevosrekisteri_kasvattaja WHERE kasvattajanimi = 'Karkurannan'");
        foreach ($query->result() as $row){
            $kasvattajainfo = array();
            //tää ei jaksa pyöriä läpi
            
            //onko hevo olemassa
            
            if ($this->it_is_there_already('vrlv3_hevosrekisteri', array('reknro'=>$row->reknro, 'kasvattaja_talli'=>$row->kasvattajatalli, 'kasvattajanimi' => $row->kasvattajanimi))){
                continue;
            }
            
            
    
            if (isset($row->kasvattajanimi) && strlen($row->kasvattajanimi)>0){
               $kasvattajainfo['kasvattajanimi'] = $row->kasvattajanimi;
            }
            
            if (isset($row->kasvattajahlo) && $this->onko_tunnus($row->kasvattajahlo) && $row->kasvattajahlo != "00000") {
               $kasvattajainfo['kasvattaja_tunnus'] = $row->kasvattajahlo;
            }
            
            
            if (isset($row->kasvattajatalli) && $this->it_is_there_already('vrlv3_tallirekisteri', array('tnro'=>$row->kasvattajatalli))) {
               $kasvattajainfo['kasvattaja_talli'] = $row->kasvattajatalli;

            }
            
            if(sizeof($kasvattajainfo)>0){
                $this->db->where('reknro', $row->reknro);
                $this->db->update('vrlv3_hevosrekisteri', $kasvattajainfo);
            }
            
        }
        
        $query = $this->db->query("SELECT * from vrlv3_kasvattajanimet");
        foreach ($query->result() as $row){
            
            $this->db->where('kasvattajanimi', $row->kasvattajanimi);
            $this->db->update('vrlv3_hevosrekisteri', array("kasvattajanimi_id"=>$row->id));
            
        }
                                  
        
    }
    
    
    public function migrate_kasvattajanimet(){
        /*//siirretään kasvattajanimet sellaisenaan
        $this->db->query("INSERT INTO vrlv3_kasvattajanimet (id, kasvattajanimi, rekisteroity, tila) SELECT id, kasvattajanimi, rekisteroity, tila FROM kasvattajanimet");
        //lisätään kasvattajanimelle tallit, mutta tarkistetaan onko talli olemassa
        $this->db->query("UPDATE vrlv3_kasvattajanimet
            INNER JOIN kasvattajanimet ON vrlv3_kasvattajanimet.id = kasvattajanimet.id
            SET vrlv3_kasvattajanimet.tnro = kasvattajanimet.tallinid
            WHERE EXISTS(SELECT * FROM vrlv3_tallirekisteri WHERE vrlv3_tallirekisteri.tnro = kasvattajanimet.tallinid)");
        echo "kasvattajanimet done";

        
        //lisätään kasvattajanimelle rodut, mutta tarkistetaan että nimi ja rotu on olemassa
        $this->db->query("INSERT INTO vrlv3_kasvattajanimet_rodut (kid, rotu)
            (SELECT distinct id, rotu
                FROM kasvattajanimet_rodut 
                WHERE EXISTS (
                        SELECT * FROM vrlv3_lista_rodut 
                        WHERE vrlv3_lista_rodut.rotunro = kasvattajanimet_rodut.rotu) 
                    AND EXISTS (SELECT * FROM vrlv3_kasvattajanimet 
                        WHERE vrlv3_kasvattajanimet.id = kasvattajanimet_rodut.id))");
        echo "kasvattajanimen rodut done";

        //Lisätään kasvattajanimelle omistaja, mutta tarkistetaan että omistaja ja nimi on olemassa
        $this->db->query("INSERT INTO vrlv3_kasvattajanimet_omistajat (kid, tunnus, taso)
            (SELECT distinct id, tunnus, '1'
                FROM kasvattajanimet 
                WHERE EXISTS (
                        SELECT * FROM vrlv3_tunnukset 
                        WHERE vrlv3_tunnukset.tunnus = kasvattajanimet.tunnus) 
                    AND EXISTS (SELECT * FROM vrlv3_kasvattajanimet 
                        WHERE vrlv3_kasvattajanimet.id = kasvattajanimet.id))");
        echo "kasvatajanimen omistajat done";
                

        $this->db->query("UPDATE vrlv3_hevosrekisteri
            INNER JOIN hevosrekisteri_kasvattaja ON vrlv3_hevosrekisteri.reknro = hevosrekisteri_kasvattaja.reknro
            SET vrlv3_hevosrekisteri.kasvattajanimi = hevosrekisteri_kasvattaja.kasvattajanimi");
        
        $this->db->query("UPDATE vrlv3_hevosrekisteri
            INNER JOIN hevosrekisteri_kasvattaja ON vrlv3_hevosrekisteri.reknro = hevosrekisteri_kasvattaja.reknro
            SET vrlv3_hevosrekisteri.kasvattaja_tunnus = hevosrekisteri_kasvattaja.kasvattajahlo
            WHERE hevosrekisteri_kasvattaja.kasvattajahlo != '00000' AND EXISTS(SELECT * FROM vrlv3_tunnukset 
                        WHERE vrlv3_tunnukset.tunnus = hevosrekisteri_kasvattaja.kasvattajahlo)");
        
        $this->db->query("UPDATE vrlv3_hevosrekisteri
            INNER JOIN hevosrekisteri_kasvattaja ON vrlv3_hevosrekisteri.reknro = hevosrekisteri_kasvattaja.reknro
            SET vrlv3_hevosrekisteri.kasvattaja_talli = hevosrekisteri_kasvattaja.kasvattajatalli
            WHERE EXISTS(SELECT * FROM vrlv3_tallirekisteri 
                        WHERE vrlv3_tallirekisteri.tnro = hevosrekisteri_kasvattaja.kasvattajatalli)");
        */
        
                
        $this->db->query("UPDATE vrlv3_hevosrekisteri
            INNER JOIN vrlv3_kasvattajanimet ON vrlv3_hevosrekisteri.kasvattajanimi = vrlv3_kasvattajanimet.kasvattajanimi
            SET vrlv3_hevosrekisteri.kasvattajanimi_id = vrlv3_kasvattajanimet.id
            WHERE vrlv3_hevosrekisteri.kasvattajanimi = vrlv3_kasvattajanimet.kasvattajanimi");
        
            
            
                        
        
        
        echo "done";
        
    }
    
    

    
        
        
    


    public function check_color($vari){
        
        $duplicates = array();
        $duplicates[42] = 121;
        $duplicates[43] = 122;
        $duplicates[44] = 123;
        $duplicates[45] = 124;
        $duplicates[165] = 132;
        

        if (isset($duplicates[$vari])){ $vari = $duplicates[$vari]; }

        if ($this->it_is_there_already('vrlv3_lista_varit', array("vid"=>$vari))){
            return $vari;
        }
        else return null;
        
    }    
    
    
    
    
    
    ////////////////////////////////////////////////////
    
    
    public function onko_tunnus($tunnus){
        $this->db->where('tunnus', $tunnus);
        $this->db->from('vrlv3_tunnukset');
        $amount = $this->db->count_all_results();
        
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
        
    }
    
    public function clean_tallitunnus($tunnus){
        $this->db->where('tnro', $tunnus);
        $this->db->from('vrlv3_tallirekisteri');
        $amount = $this->db->count_all_results();
        
        if ($amount != 1){
            return null;
        }
        
        else {
            return $tunnus;
        }
        
    }
    
    public function clean_tunnus($tunnus){
        
        if (!$this->onko_tunnus($tunnus)){
            return "00000";
        }
        
        else {
            return $tunnus;
        }
        
    }
    
    
    public function it_is_there_already ($taulu, $array){
        $this->db->where($array);
        $this->db->from($taulu);
        $amount = $this->db->count_all_results();
            
        if ($amount != 1){
            return false;
        }
        
        else {
            return true;
        }
        
    }
    
    public function vrlv3_tunnukset_kirjautumiset($query, $success){
        foreach ($query->result() as $row)
        {
            $data_login['tunnus'] = $row->tunnus;
            $data_login['ip'] = $row->ip;
            $data_login['onnistuiko'] = $success;
            $data_login['aika'] = $row->kirjautunut;
            
            
            $this->db->where(array('tunnus'=>$row->tunnus, 'aika'=>$row->kirjautunut));
            $this->db->from('vrlv3_tunnukset_kirjautumiset');
            $amount = $this->db->count_all_results();
    
            if ($amount == 0){
               $this->db->insert('vrlv3_tunnukset_kirjautumiset', $data_login);
            }
                               
        }
    }
    
    function count_by_keys($old_table, $new_table, $countable_old, $countable_new){
        $query = $this->db->query("SELECT COUNT(DISTINCT(". $countable_new .")) as kpl from " . $new_table);
        $new = $query->row()->kpl;
        
        $query = $this->db->query("SELECT COUNT(DISTINCT(". $countable_old . ")) as kpl from " . $old_table);
        $old = $query->row()->kpl;
        
        return array("new"=> $new, "old"=> $old);
    }

    function count_all_rows($old_table, $new_table){
        $query = $this->db->query("SELECT COUNT(*) as kpl from " . $new_table);
        $new = $query->row()->kpl;
        
        $query = $this->db->query("SELECT COUNT(*) as kpl from " . $old_table);
        $old = $query->row()->kpl;
        
        return array("new"=> $new, "old"=> $old);
    }
}

