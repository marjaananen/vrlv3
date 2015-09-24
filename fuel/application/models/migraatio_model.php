<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Migraatio_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    //Applications
    function migrate_tunnukset()
    {
        //VRLV3_TUNNUKSET
        $query = $this->db->get('tunnukset');       
        foreach ($query->result() as $row)
        {

            //Data vrlvä_tunnukset tauluun
            $data['ip_address'] = $row->rek_ip;
            $data['username'] = $row->tunnus;
            $data['password'] = $row->salasana;
            //$data['salt'] = 
            $data['email'] = $row->email;
            //$data['activation_code'] = "";
            //$data['forgotten_password_code'] = "";
            //$data['forgotten_password_time'] = "";
            //$data['remember_code'] = $old_data[''];
            $data['created_on'] = $row->rekisteroitynyt;
            //$data['last_login'] = $old_data[''];
            $data['active'] = 1;
            $data['tunnus'] = $row->tunnus;
            $data['nimimerkki'] = $row->nimimerkki;
            $data['nayta_email'] = $row->nayta_email;
            $data['nayta_vuosilaani'] = $row->nayta_vuosilaani; //MUUTA UUTEEN MUOTOON
            
            
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
        
        //VRLV3_TUNNUKSET_KIRJAUTUMISET
        $query = $this->db->get('tunnukset_epa');
        $this->vrlv3_tunnukset_kirjautumiset($query);
        $query = $this->db->get('tunnukset_kirjautumiset');
        $this->vrlv3_tunnukset_kirjautumiset($query);
            
        //VRLV3_TUNNUKSET_KIRJAUTUMISET       
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
                    $data['lahettaja'] = 00000;
                }
                else {
                    $data['lahettaja'] = $row->hyvaksyi;
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
                    $this->db->insert('vrlv3_tunnukset_nimimerkit', $data_message);              
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