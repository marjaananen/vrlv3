<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');
 
class Tallit_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    //Hakemus
    function add_new_application($name, $desc, $url, $category, $abbreviation)
    {
        $data = array('nimi' => $name, 'kuvaus' => $desc, 'url' => $url, 'kategoria' => $category, 'lyhenne' => $abbreviation);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['lisaaja'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->insert('vrlv3_tallirekisteri_jonossa', $data);
    }
    
    //Kategoria
    function get_category($kat)
    {
        $data = 'Ei saatavilla';
        
        $this->db->select('katelyh');
        $this->db->from('vrlv3_lista_tallikategoriat');
        $this->db->where('kat', $kat);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 
            $data = $data['katelyh'];
        }
        
        return $data;
    }

    function get_category_option_list()
    {
        $data = array();
        
        $this->db->select('kat, kategoria');
        $this->db->from('vrlv3_lista_tallikategoriat');
	$this->db->order_by("kategoria", "asc"); 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
               $data[$row['kat']] = $row['kategoria'];
            }
        }
        
        return $data;
    }
    
    //Sekalaisia
    function is_tnro_in_use($tnro)
    {
        $this->db->select('tnro');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
}

