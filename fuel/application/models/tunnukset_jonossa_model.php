<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Tunnukset_jonossa_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct('vrlv3_tunnukset_jonossa'); // table name
    }
 
    function add_new($nimimerkki, $email, $syntymavuosi, $sijainti)
    {
        $data = array('nimimerkki' => $nimimerkki, 'email' => $email, 'syntymavuosi' => $syntymavuosi, 'sijainti' => $sijainti);
        
        $data['salasana'] = $this->_generate_random_string(8);
        $data['rekisteroitynyt'] = date("Y-m-d H:i:s");
        $data['varmistus'] = $this->_generate_random_string(10);
        $data['ip'] = $this->input->ip_address();
        
        $this->insert($data);
        
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
