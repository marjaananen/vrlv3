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
    
    //Tallit
    function get_users_stables($pinnumber)
    {
        $this->db->select('vrlv3_tallirekisteri.tnro, nimi');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_stable($tnro)
    {
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
    
    function edit_stable($name, $desc, $url, $tnro, $new_tnro=-1)
    {
        $data = array('nimi' => $name, 'kuvaus' => $desc, 'url' => $url);
        
        if($new_tnro != -1)
            $data['tnro'] = $new_tnro;

        $this->db->where('tnro', $tnro);
        $this->db->update('vrlv3_tallirekisteri', $data);
    }
    
    function add_owner_to_stable($tnro, $applicant, $level)
    {
        $data = array('tnro' => $tnro, 'omistaja' => $applicant, 'taso' => $level);
        
        $this->db->insert('vrlv3_tallirekisteri_omistajat', $data);
    }
    
    function is_stable_owner($pinnumber, $tnro)
    {
        $this->db->from('vrlv3_tallirekisteri_omistajat');
        $this->db->where('tnro', $tnro);
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function get_users_stable_stats($pinnumber)
    {
        $data = array();
        
        $this->db->select('lopettanut');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        $data['all'] = $query->num_rows();
        $data['active'] = 0;
        
        if ($query->num_rows() > 0)
        {
            $result = $query->result_array();
            
            foreach($result as $r)
            {
                if($r['lopettanut'] == 0)
                    $data['active']++;
            }
        }
        
        $this->db->where('lisaaja', $pinnumber);
        $this->db->from('vrlv3_tallirekisteri_jonossa');
        $data['queued'] = $this->db->count_all_results();
        
        return $data;
    }
    
    function search_stables($name, $category, $tnro)
    {
        $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro');
        
        if(!empty($name))
            $this->db->where('nimi', $name);
            
        if($category != '-1')
            $this->db->where('kategoria', $category);
            
        if(!empty($tnro))
            $this->db->where('vrlv3_tallirekisteri.tnro', $tnro);
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
    
    //Kategoria
    function add_category_to_stable($tnro, $category, $applicant)
    {
        $data = array('tnro' => $tnro, 'kategoria' => $category, 'anoi' => $applicant, 'tarkistaja' => $this->ion_auth->user()->row()->tunnus);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['kasitelty'] = $data['lisatty'];
        
        $this->db->insert('vrlv3_tallirekisteri_kategoriat', $data);
    }
    
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

