<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Tallit_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    //Hakemukset
    function add_new_application($name, $desc, $url, $category, $abbreviation)
    {
        $data = array('nimi' => $name, 'kuvaus' => $desc, 'url' => $url, 'kategoria' => $category, 'lyhenne' => $abbreviation);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['lisaaja'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->insert('vrlv3_tallirekisteri_jonossa', $data);
    }
    
    function add_new_category_application($tnro, $category)
    {
        $data = array('tnro' => $tnro, 'kategoria' => $category);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['lisaaja'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->insert('vrlv3_tallirekisteri_kategoriat_jonossa', $data);
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
        
        //päivityksestä jää jälki
        $data = array('tnro' => $tnro, 'paivitti' => $this->ion_auth->user()->row()->tunnus, 'aika' => date("Y-m-d H:i:s"));
        $this->db->insert('vrlv3_tallirekisteri_paivitetty', $data);
    }
    
    function is_stable_active($tnro)
    {
        $this->db->select('tnro');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->where('tnro', $tnro);
        $this->db->where('lopettanut', 0);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }

    function mark_stable_inactive($tnro)
    {
        $this->db->where('tnro', $tnro);
        $this->db->update('vrlv3_tallirekisteri', array('lopettanut' => 1));
    }
    
    function add_owner_to_stable($tnro, $applicant, $level)
    {
        $data = array('tnro' => $tnro, 'omistaja' => $applicant, 'taso' => $level);
        
        $this->db->insert('vrlv3_tallirekisteri_omistajat', $data);
    }
    
    function get_stables_owners($tnro)
    {
        $this->db->select('omistaja, nimimerkki');
        $this->db->from('vrlv3_tallirekisteri_omistajat');
        $this->db->join('vrlv3_tunnukset', 'vrlv3_tunnukset.tunnus = vrlv3_tallirekisteri_omistajat.omistaja');
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
    }
    
    function is_stable_owner($pinnumber, $tnro)
    {
        $this->db->select('omistaja');
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
        $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu, katelyh');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro');
        $this->db->join('vrlv3_lista_tallikategoriat', 'vrlv3_lista_tallikategoriat.kat = vrlv3_tallirekisteri_kategoriat.kategoria');
        
        if(!empty($name))
        {
            if(strpos($name, '*') !== false)
                $this->db->where('nimi LIKE "' . str_replace('*', '%', $name) . '"');
            else
                $this->db->where('nimi', $name);
        }
            
        if($category != '-1')
            $this->db->where('vrlv3_tallirekisteri_kategoriat.kategoria', $category);
            
        if(!empty($tnro))
        {
            if(strpos($tnro, '*') !== false)
                $this->db->where('vrlv3_tallirekisteri.tnro LIKE "' . str_replace('*', '%', $tnro) . '"');
            else
                $this->db->where('vrlv3_tallirekisteri.tnro', $tnro);
        }
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    function get_stables_likes($tnro)
    {  
        $this->db->where('tnro', $tnro);
        $this->db->where('aani', 1);
        $this->db->from('vrlv3_tallirekisteri_yesno');
        return $this->db->count_all_results();
    }
    
    function get_stables_like_by_user($tnro, $pinnumber)
    {
        $this->db->select('aika');
        $this->db->where('tnro', $tnro);
        $this->db->where('tunnus', $pinnumber);
        $this->db->from('vrlv3_tallirekisteri_yesno');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]['aika'];
        }
        
        return "0000-00-00";
    }
    
    function add_stable_like($tnro)
    {
        $data = array('tnro' => $tnro, 'aani' => 1);

        $data['aika'] = date("Y-m-d H:i:s");
        $data['tunnus'] = $this->ion_auth->user()->row()->tunnus;
        
        $this->db->insert('vrlv3_tallirekisteri_yesno', $data);
    }
    
    function delete_stable_like($tnro)
    {
        $this->db->delete('vrlv3_tallirekisteri_yesno', array('tunnus' => $this->ion_auth->user()->row()->tunnus, 'tnro' => $tnro));
    }
    
    //Kategoriat
    function add_category_to_stable($tnro, $category, $applicant)
    {
        $data = array('tnro' => $tnro, 'kategoria' => $category, 'anoi' => $applicant, 'hyvaksyi' => $this->ion_auth->user()->row()->tunnus);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['kasitelty'] = $data['lisatty'];
        
        $this->db->insert('vrlv3_tallirekisteri_kategoriat', $data);
        
        //päivityksestä jää jälki
        $data = array('tnro' => $tnro, 'paivitti' => $this->ion_auth->user()->row()->tunnus, 'aika' => date("Y-m-d H:i:s"));
        $this->db->insert('vrlv3_tallirekisteri_paivitetty', $data);
    }
    
    function delete_category($id)
    {
        $this->db->select('tnro');
        $this->db->from('vrlv3_tallirekisteri_kategoriat');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        //päivityksestä jää jälki
        $data = array('tnro' => $query->row()->tnro, 'paivitti' => $this->ion_auth->user()->row()->tunnus, 'aika' => date("Y-m-d H:i:s"));
        $this->db->insert('vrlv3_tallirekisteri_paivitetty', $data);
        
        $this->db->delete('vrlv3_tallirekisteri_kategoriat', array('id' => $id));
    }
    
    function is_category_owner($id, $pinnumber)
    {
        $this->db->select('omistaja');
        $this->db->from('vrlv3_tallirekisteri_omistajat');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri_omistajat.tnro = vrlv3_tallirekisteri_kategoriat.tnro');
        $this->db->where('vrlv3_tallirekisteri_kategoriat.id', $id);
        $this->db->where('omistaja', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function stable_has_category($tnro, $category)
    {
        $this->db->select('kategoria');
        $this->db->from('vrlv3_tallirekisteri_kategoriat');
        $this->db->where('tnro', $tnro);
        $this->db->where('kategoria', $category);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
    
    function get_stables_categories($tnro)
    {
        $this->db->select('katelyh, vrlv3_tallirekisteri_kategoriat.id');
        $this->db->from('vrlv3_tallirekisteri_kategoriat');
        $this->db->join('vrlv3_lista_tallikategoriat', 'vrlv3_lista_tallikategoriat.kat = vrlv3_tallirekisteri_kategoriat.kategoria');
        $this->db->where('tnro', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        
        return array();
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

