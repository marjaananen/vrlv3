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
            
            $this->db->where('kategoria', $row->kategoria);
            $this->db->from('vrlv3_lista_tiedotuskategoriat');
            $amount = $this->db->count_all_results();
            
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
        $query = $this->db->get('tunnukset');       
        foreach ($query->result() as $row)
        {

            //Data vrlvä_tunnukset tauluun
            $data['email'] = $row->email;
            $data['tunnus'] = $row->tunnus;

            if ($this->it_is_there_already('vrlv3_tunnukset', array('email'=>$data['email']))){
                continue;
            }
            if ($this->it_is_there_already('vrlv3_tunnukset', array('tunnus'=>$data['tunnus']))){
                continue;
            }
            $data['ip_address'] = $row->rek_ip;
            $data['username'] = $row->tunnus;
            $data['password'] = $row->salasana;
            //$data['salt'] = 
            //$data['activation_code'] = "";
            //$data['forgotten_password_code'] = "";
            //$data['forgotten_password_time'] = "";
            //$data['remember_code'] = $old_data[''];
            $data['created_on'] = $row->rekisteroitynyt;
            //$data['last_login'] = $old_data[''];
            $data['active'] = 1;
            $data['nimimerkki'] = $row->nimimerkki;

            if($row->nayta_email == 1 || $row->nayta_email == 2)
                $data['nayta_email'] = 0;
            else
                $data['nayta_email'] = 1;

            if($row->nayta_vuosilaani == 1 || $row->nayta_vuosilaani == 2)
            {
                $data['nayta_vuosi'] = 0;
                $data['nayta_laani'] = 0;
            }
            else
            {
                $data['nayta_vuosi'] = 1;
                $data['nayta_laani'] = 1;
            }
            
            
            
            $this->db->where('id', $row->laani);
            $this->db->from('vrlv3_lista_maakunnat');
            $amount = $this->db->count_all_results();
            
            if ($amount != 1) {
                $data['laani'] = 0;
            }
            else {
                $data['laani'] = $row->laani;;
            }
            
            $data['syntymavuosi'] = $row->syntymavuosi;
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
            }
            else {
                $this->db->where('tunnus', $row->tunnus);
                $this->db->update('vrlv3_tunnukset', $data);
            }
                     
            
        }
        
        /*
        
        //VRLV3_TUNNUKSET_KIRJAUTUMISET
        $query = $this->db->get('tunnukset_epa');
        $this->vrlv3_tunnukset_kirjautumiset($query);
        $query = $this->db->get('tunnukset_kirjautumiset');
        $this->vrlv3_tunnukset_kirjautumiset($query);
        
        */
            
        //VRLV3_TUNNUKSET_NIKIT     
        $query = $this->db->get('tunnukset_nimimerkit');
        foreach ($query->result() as $row)
        {
            if ($this->onko_tunnus($row->tunnus)) {
                $data_nick['tunnus'] = $row->tunnus;
                $data_nick['nimimerkki'] = $row->nimimerkki;
                $data_nick['vaihtanut'] = $row->vaihtanut;
                $data_nick['piilotettu'] = $row->piilotettu;
            }
            
            $this->db->where(array('tunnus'=>$row->tunnus, 'vaihtanut'=>$row->vaihtanut));
            $this->db->from('vrlv3_tunnukset_nimimerkit');
            $amount = $this->db->count_all_results();
        
            if ($amount == 0){
                $this->db->insert('vrlv3_tunnukset_nimimerkit', $data_nick);              
            }
        }
        
        
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
        
                if ($amount == 0){
                    $this->db->insert('vrlv3_tunnukset_pikaviestit', $data_message);              
                }
            }
        }
        
        $query = $this->db->get('tunnukset_yhteystiedot');
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
    
    public function vrlv3_tunnukset_kirjautumiset($query){
        foreach ($query->result() as $row)
        {
            if ($this->onko_tunnus($row->tunnus)) {
                $data_login['tunnus'] = $row->tunnus;
                $data_login['ip'] = $row->ip;
                $data_login['onnistuiko'] = 0;
                $data_login['aika'] = $row->kirjautunut;
                
                
                $this->db->where(array('tunnus'=>$row->tunnus, 'aika'=>$row->aika));
                $this->db->from('vrlv3_tunnukset_kirjautumiset');
                $amount = $this->db->count_all_results();
        
                if ($amount == 0){
                   $this->db->insert('vrlv3_tunnukset_kirjautumiset', $data_login);
                }
                               
            }
        }
    }
}