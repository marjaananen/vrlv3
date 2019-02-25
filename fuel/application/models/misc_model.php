<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Misc_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
 
    function add_rejected_queue_item($type)
    {
        $data = array('tyyppi' => $type);
        
        $data['pvm'] = date("Y-m-d H:i:s");
        $data['tunnus'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->insert('vrlv3_hylatyt_hakemukset', $data);
    }
    
}

