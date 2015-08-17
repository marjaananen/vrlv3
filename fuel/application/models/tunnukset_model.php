<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Tunnukset_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    function add_new_application($nickname, $email, $dateofbirth, $location)
    {
        $data = array('nimimerkki' => $nickname, 'email' => $email, 'sijainti' => $location);
        
        $data['salasana'] = $this->_generate_random_string(8);
        $data['rekisteroitynyt'] = date("Y-m-d H:i:s");
        $data['varmistus'] = $this->_generate_random_string(10);
        $data['ip'] = $this->input->ip_address();
        
        //syntymä on muodossa dd.mm.yyyy joten pitää muuttaa
        if($syntymavuosi == '')
            $data['syntymavuosi'] = '0000-00-00';
        else
            $data['syntymavuosi'] = date('Y-m-d', strtotime($dateofbirth));
        
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
        
        $this->db->select('nimimerkki, email, syntymavuosi, sijainti, salasana');
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
    
    function get_location($id)
    {
        $data = 'Ei saatavilla';
        
        $this->db->select('maakunta');
        $this->db->from('vrlv3_lista_maakunnat');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 
            $data = $data['maakunta'];
        }
        
        return $data;
    }
	
    function get_location_option_list()
    {
        $data = array();
        
        $this->db->select('id, maakunta');
        $this->db->from('vrlv3_lista_maakunnat');
		$this->db->order_by("maakunta", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
			{
			   $data[$row['id']] = $row['maakunta'];
			}
        }
        
        return $data;
    }
    
    function get_next_application()
    {
        $data = array('success' => false);
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa hakemusta uudestaan käsittelyyn 15 minuuttiin
        
        $this->db->select('id, nimimerkki, email, syntymavuosi, rekisteroitynyt, sijainti, ip');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('vahvistettu', 1);
        $this->db->where('kasitelty IS NULL OR kasitelty < "' . $date->format('Y-m-d H:i:s') . '"');
        $this->db->order_by("rekisteroitynyt", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 

            $date->setTimestamp(time());
            $update_data = array('kasitelty' => $date->format('Y-m-d H:i:s'));
            
            $this->db->where('id', $data['id']);
            $this->db->update('vrlv3_tunnukset_jonossa', $update_data);
            
            $data['success'] = true;
        }
        
        return $data;
    }
    
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
    
    function add_successful_login($pinnumber)
    {
        $data = array('tunnus' => $pinnumber);
        $data['aika'] = date("Y-m-d H:i:s");
        $data['ip'] = $this->input->ip_address();
        
        $this->db->insert('vrlv3_tunnukset_kirjautumiset', $data);
        
        return true;
    }

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
    
    //Messages
    function send_message($user, $recipient, $message)
    {
        $data = array('lahettaja' => $user, 'vastaanottaja' => $recipient, 'viesti' => $message);   
        $this->db->insert('vrlv3_tunnukset_pikaviestit', $data);
    }
    
    function unread_messages($user)
    {
        $data = array('vastaanottaja' => $user, 'luettu' => 0);   
        $this->db->where($data);
        $this->db->from('vrlv3_tunnukset_pikaviestit');
        return $this->db->count_all_results();
    }
    
    function get_users_messages($user)
    {
        $this->clean_messages($user);
        
        $data = array();
        $this->db->where('vastaanottaja', $user);
        $this->db->from('vrlv3_tunnukset_pikaviestit');
        $this->db->order_by("aika", "desc"); 
        $query = $this->db->get();
        
        $this->mark_all_as_read($user);
        return $query->result_array();
    }
    
        
    function delete_message($user, $id)
    {
        $this->db->delete('vrlv3_tunnukset_pikaviestit', array('id' => $id, "vastaanottaja"=> $user)); 
    }
    
    function clean_messages($user)
    {
        //seitsemän päivää vanhat poistetaan
        $date = new DateTime();
        $date->setTimestamp(time() - 7*24*60*60);
        $oldest_possible = $date->format('Y-m-d H:i:s');
        $this->db->delete('vrlv3_tunnukset_pikaviestit', array("vastaanottaja"=> $user, "tarkea" => 0, "luettu" => 1, "aika <" => $oldest_possible)); 
    }
    

    function mark_all_as_read($user)
    {
        $data = array('luettu' => 1);
        $this->db->where('vastaanottaja', $user);
        $this->db->update('vrlv3_tunnukset_pikaviestit', $data);
    }
    
    function mark_as_important($user, $id)
    {
        $data = array('tarkea' => 1);
        $this->_edit_message($id, $user, $data);
    }
    
    function mark_as_unimportant($user, $id)
    {
        $data = array('tarkea' => 0);
        $this->_edit_message($id, $user, $data);
    }

    function _edit_message($id, $user, $data){
        $where = array('id' => $id, 'vastaanottaja' => $user);
        $this->db->where($where);
        $this->db->update('vrlv3_tunnukset_pikaviestit', $data);
    }

?>
