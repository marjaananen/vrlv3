<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Tunnukset_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    function add_new($nimimerkki, $email, $syntymavuosi, $sijainti)
    {
        $data = array('nimimerkki' => $nimimerkki, 'email' => $email, 'sijainti' => $sijainti);
        
        $data['salasana'] = $this->_generate_random_string(8);
        $data['rekisteroitynyt'] = date("Y-m-d H:i:s");
        $data['varmistus'] = $this->_generate_random_string(10);
        $data['ip'] = $this->input->ip_address();
        
        //syntymä on muodossa dd.mm.yyyy joten pitää muuttaa
        $data['syntymavuosi'] = date('Y-m-d', strtotime($syntymavuosi));
        
        $this->db->insert('vrlv3_tunnukset_jonossa', $data);
        
        return $data;
    }
    
    function validate($email, $varmistus)
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
