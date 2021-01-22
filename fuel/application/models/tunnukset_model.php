<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Tunnukset_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    //Applications
    function add_new_application($nickname, $email)
    {
        $data = array('nimimerkki' => $nickname, 'email' => $email);
        
        $data['salasana'] = $this->_generate_random_string(8);
        $data['rekisteroitynyt'] = date("Y-m-d H:i:s");
        $data['varmistus'] = $this->_generate_random_string(10);
        $data['ip'] = $this->input->ip_address();
                
        
        $this->db->insert('vrlv3_tunnukset_jonossa', $data);
        
        return $data;
    }
    
    function delete_application($id)
    {
        $this->db->delete('vrlv3_tunnukset_jonossa', array('id' => $id)); 
    }
    
    function validate_application($email, $validation)
    {
        $this->db->select('email, varmistus, id');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('email', $email); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $row = $query->row(); 
        
            if($row->email == $email && $row->varmistus == $validation)
            {
                $data = array('vahvistettu' => 1);
                
                $this->db->where('id', $row->id);
                $this->db->update('vrlv3_tunnukset_jonossa', $data);
                
                return true;
            }
        }
        
        return false;
    }
    
    function get_application_queue_length()
    {
        $this->db->where('vahvistettu', 1);
        $this->db->from('vrlv3_tunnukset_jonossa');
        return $this->db->count_all_results();
    }
    
    function get_application_queue_unlocked_num()
    {
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa hakemusta uudestaan käsittelyyn 15 minuuttiin
        
        $this->db->select('id');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('vahvistettu', 1);
        $this->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    function get_oldest_application()
    {
        $data = 0;
        
        $this->db->select('rekisteroitynyt');
        $this->db->where('vahvistettu', 1);
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->order_by("rekisteroitynyt", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 
            $data = $data['rekisteroitynyt'];
        }
        
        return $data;
    }
    
    function get_application($id)
    {
        $data = array('success' => false);
        
        $this->db->select('nimimerkki, email, salasana');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 
            $data['success'] = true;
        }
        
        return $data;
    }

    function get_next_application()
    {
        $data = array('success' => false);
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa hakemusta uudestaan käsittelyyn 15 minuuttiin
        
        $this->db->select('id, nimimerkki, email, rekisteroitynyt, ip');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('vahvistettu', 1);
        $this->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
        $this->db->order_by("rekisteroitynyt", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 

            $date->setTimestamp(time());
            $user = $this->ion_auth->user()->row();
            $update_data = array('kasitelty' => $date->format('Y-m-d H:i:s'), 'kasittelija' => $user->tunnus);
            
            $this->db->where('id', $data['id']);
            $this->db->update('vrlv3_tunnukset_jonossa', $update_data);
            
            $data['success'] = true;
        }
        
        return $data;
    }
    
   
    
    //Other/misc
    function get_next_pinnumber()
    {
        $data = 0;

        $this->db->select('tunnus');
        $this->db->from('vrlv3_tunnukset');
        $this->db->order_by("tunnus", "desc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array();
            $data = $data['tunnus']+1;
        }
        
        return $data;
    }
    
    function add_previous_nickname($nickname, $pinnumber, $hidden=0)
    {
        $data = array('nimimerkki' => $nickname, 'tunnus' => $pinnumber, 'piilotettu' => $hidden);
        $data['vaihtanut'] = date("Y-m-d H:i:s");
        
        $this->db->insert('vrlv3_tunnukset_nimimerkit', $data);
        
        return true;
    }
    
    function get_previous_nicknames($pinnumber)
    {
        $this->db->select('nimimerkki, vaihtanut');
        $this->db->from('vrlv3_tunnukset_nimimerkit');
        $this->db->where('tunnus', $pinnumber);
        $this->db->where('piilotettu', 0);
        $this->db->order_by("vaihtanut", "desc");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_latest_approvals()
    {
        $this->db->select('nimimerkki, hyvaksytty');
        $this->db->from('vrlv3_tunnukset');
        $this->db->order_by("hyvaksytty", "desc");
        $this->db->limit(5);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_pinnumbers_by_nickname($nick)
    {
        $this->db->select('tunnus');
        $this->db->from('vrlv3_tunnukset');
        $this->db->where('nimimerkki', $nick);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
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
    

    function get_users_id($pinnumber)
    {
        $this->db->select('id');
        $this->db->from('vrlv3_tunnukset');
        $this->db->where('tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array()['id']; 
        }
        
        return -1;
    }
    
    function search_users($pinnumber, $nick, $email=null, $admin=false)
    {
        if($admin){
            $this->db->select('tunnus, nimimerkki, email');
        }else {
            $this->db->select('tunnus, nimimerkki, IF(nayta_email = "1", email, "Ei julkinen") as email');
        }
        $this->db->from('vrlv3_tunnukset');
        
        if(!empty($pinnumber))
        {
            if(strpos($pinnumber, '*') !== false)
                $this->db->where('tunnus LIKE "' . str_replace('*', '%', $pinnumber) . '"');
            else
                $this->db->where('tunnus', $pinnumber);
        }
        
        if(!empty($nick))
        {
            if(strpos($nick, '*') !== false)
                $this->db->where('nimimerkki LIKE "' . str_replace('*', '%', $nick) . '"');
            else
                $this->db->where('nimimerkki', $nick);
        }
        if(!empty($email)){
             if(strpos($email, '*') !== false)
                $this->db->where('email LIKE "' . str_replace('*', '%', $email) . '"');
            else
                $this->db->where('email', $email);
                
            if(!$admin){
                $this->db->where('nayta_email', 1);
            }
            
        }
        $query = $this->db->get();
                
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    //Logins
    function get_latest_failed_logins()
    {
        $this->db->select('vrlv3_tunnukset.tunnus, nimimerkki, aika, ip');
        $this->db->from('vrlv3_tunnukset_kirjautumiset');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset_kirjautumiset.tunnus = vrlv3_tunnukset.tunnus');
        $this->db->where('onnistuiko', 0);
        $this->db->order_by("aika", "desc");
        $this->db->limit(10);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    function get_latest_logins($id=null, $limit=10)
    {
        $this->db->select('vrlv3_tunnukset.tunnus, nimimerkki, aika, ip');
        $this->db->from('vrlv3_tunnukset_kirjautumiset');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset_kirjautumiset.tunnus = vrlv3_tunnukset.tunnus');
        $this->db->where('onnistuiko', 1);
        if($id != null){
            $this->db->where('vrlv3_tunnukset.tunnus', $id);
        }
        $this->db->order_by("aika", "desc");
        $this->db->limit($limit);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_logins_by_ip($ip, $limit=5)
    {
        $this->db->select('nimimerkki, vrlv3_tunnukset.tunnus, aika, ip');
        $this->db->from('vrlv3_tunnukset_kirjautumiset');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset_kirjautumiset.tunnus = vrlv3_tunnukset.tunnus');
        $this->db->where('ip',  $ip);
        $this->db->order_by("aika", "desc");
        $this->db->limit($limit);
        $query = $this->db->get();
        echo $this->db->last_query();

        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function add_login($pinnumber, $ok = 1)
    {
        $data = array('tunnus' => $pinnumber);
        $data['aika'] = date("Y-m-d H:i:s");
        $data['ip'] = $this->input->ip_address();
        $data['onnistuiko'] = $ok;
        
        $this->db->insert('vrlv3_tunnukset_kirjautumiset', $data);
        
        return true;
    }

    //Private
    function _generate_random_string($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    
    
    //Contacts
    function add_contact($pinnumber, $type, $info, $nayta=0)
    {
        $data = array('tunnus' => $pinnumber, 'tyyppi' => $type, 'tieto' => $info, 'nayta' => $nayta);   
        $this->db->insert('vrlv3_tunnukset_yhteystiedot', $data);
    }
    
    function delete_contact($pinnumber, $id)
    {
        $this->db->delete('vrlv3_tunnukset_yhteystiedot', array('id' => $id, "tunnus"=> $pinnumber)); 
    }
    
    function edit_contact($pinnumber, $edit, $type, $info)
    {
        $where = array('id' => $id, 'tunnus' => $pinnumber);
        $data = array('tyyppi' => $type, 'tieto' => $info);   

        $this->db->where($where);
        $this->db->update('vrlv3_tunnukset_yhteystiedot', $data);
    }
    
    function get_users_contacts($pinnumber)
    {
        $this->db->where('tunnus', $pinnumber);
        $this->db->from('vrlv3_tunnukset_yhteystiedot');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_users_public_contacts($pinnumber)
    {
        $this->db->select('tyyppi, tieto');
        $this->db->where('tunnus', $pinnumber);
        $this->db->where('nayta', 1);
        $this->db->from('vrlv3_tunnukset_yhteystiedot');
        $query = $this->db->get();
        return $query->result_array();
    }    
    
    //Messages
    function send_message($pinnumber, $recipient, $message)
    {
        $data = array('lahettaja' => $pinnumber, 'vastaanottaja' => $recipient, 'viesti' => $message);   
        $this->db->insert('vrlv3_tunnukset_pikaviestit', $data);
    }
    
    function unread_messages($pinnumber)
    {
        $data = array('vastaanottaja' => $pinnumber, 'luettu' => 0);   
        $this->db->where($data);
        $this->db->from('vrlv3_tunnukset_pikaviestit');
        return $this->db->count_all_results();
    }
    
    function get_users_messages($pinnumber)
    {
        $this->clean_messages($pinnumber);
        
        $this->db->where('vastaanottaja', $pinnumber);
        $this->db->from('vrlv3_tunnukset_pikaviestit');
        $this->db->order_by("aika", "desc"); 
        $query = $this->db->get();
        
        $this->mark_all_as_read($pinnumber);
        return $query->result_array();
    }
    
        
    function delete_message($pinnumber, $id)
    {
        $this->db->delete('vrlv3_tunnukset_pikaviestit', array('id' => $id, "vastaanottaja"=> $pinnumber)); 
    }
    
    function clean_messages($pinnumber)
    {
        //seitsemän päivää vanhat poistetaan
        $date = new DateTime();
        $date->setTimestamp(time() - 7*24*60*60);
        $oldest_possible = $date->format('Y-m-d H:i:s');
        $this->db->delete('vrlv3_tunnukset_pikaviestit', array("vastaanottaja"=> $pinnumber, "tarkea" => 0, "luettu" => 1, "aika <" => $oldest_possible)); 
    }
    

    function mark_all_as_read($pinnumber)
    {
        $data = array('luettu' => 1);
        $this->db->where('vastaanottaja', $pinnumber);
        $this->db->update('vrlv3_tunnukset_pikaviestit', $data);
    }
    
    function mark_as_important($pinnumber, $id)
    {
        $data = array('tarkea' => 1);
        $this->_edit_message($id, $pinnumber, $data);
    }
    
    function mark_as_unimportant($pinnumber, $id)
    {
        $data = array('tarkea' => 0);
        $this->_edit_message($id, $pinnumber, $data);
    }

    function _edit_message($id, $pinnumber, $data)
    {
        $where = array('id' => $id, 'vastaanottaja' => $pinnumber);
        $this->db->where($where);
        $this->db->update('vrlv3_tunnukset_pikaviestit', $data);
    }
}

