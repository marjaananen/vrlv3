<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Tunnukset_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    function add_new_application($nimimerkki, $email, $syntymavuosi, $sijainti)
    {
        $data = array('nimimerkki' => $nimimerkki, 'email' => $email, 'sijainti' => $sijainti);
        
        $data['salasana'] = $this->_generate_random_string(8);
        $data['rekisteroitynyt'] = date("Y-m-d H:i:s");
        $data['varmistus'] = $this->_generate_random_string(10);
        $data['ip'] = $this->input->ip_address();
        
        //syntymä on muodossa dd.mm.yyyy joten pitää muuttaa
        if($syntymavuosi == '')
            $data['syntymavuosi'] = '0000-00-00';
        else
            $data['syntymavuosi'] = date('Y-m-d', strtotime($syntymavuosi));
        
        $this->db->insert('vrlv3_tunnukset_jonossa', $data);
        
        return $data;
    }
    
    function delete_application($id)
    {
        $this->db->delete('vrlv3_tunnukset_jonossa', array('id' => $id)); 
    }
    
    function validate_application($email, $varmistus)
    {
        $this->db->select('email, varmistus, id');
        $this->db->from('vrlv3_tunnukset_jonossa');
        $this->db->where('email', $email); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $row = $query->row(); 
        
            if($row->email == $email && $row->varmistus == $varmistus)
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
}

?>
