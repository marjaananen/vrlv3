<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Tallit_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    //Tallit
    function get_users_stables($pinnumber, $cats = false, $only_online = false)
    {
        $this->db->from('vrlv3_tallirekisteri');

        
        if($cats){
            $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu, lopettanut, katelyh, kuvaus');
            $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro', 'left');
            $this->db->join('vrlv3_lista_tallikategoriat', 'vrlv3_lista_tallikategoriat.kat = vrlv3_tallirekisteri_kategoriat.kategoria');
        }else {
            $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu, lopettanut, kuvaus');

        }
        $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
        $this->db->where('omistaja', $pinnumber);
        if($only_online){
            $this->db->where('lopettanut', 0);

        }
	
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
	function get_users_stables_amount($pinnumber, $active = 1)
    {
        

        $this->db->select('COUNT(vrlv3_tallirekisteri.tnro) as kaikki,
    COUNT(CASE WHEN lopettanut = 0 then 1 ELSE NULL END) as toiminnassa');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_omistajat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_omistajat.tnro');
        $this->db->where('omistaja', $pinnumber);
		$query = $this->db->get();

	
		if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]; 
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
    
    function add_stable($insert_data, $kategoria, $owner){
            $insert_data['hyvaksyi'] = $owner;
            
            $this->db->trans_start();
            $this->db->insert('vrlv3_tallirekisteri', $insert_data);
            $this->add_owner_to_stable($insert_data['tnro'], $owner, 1);
			$this->mass_edit_categories($insert_data['tnro'], $kategoria);
            $this->db->trans_complete();
			
			

        //päivityksestä jää jälki
		$this->mark_update($insert_data['tnro'], "Tallin lisääminen");
            
            
        }
        
    function edit_stable($name, $desc, $url, $tnro, $new_tnro=-1)
    {
        $data = array('nimi' => $name, 'kuvaus' => $desc, 'url' => $url);
        
        if($new_tnro != -1)
            $data['tnro'] = $new_tnro;

        $this->db->where('tnro', $tnro);
        $this->db->update('vrlv3_tallirekisteri', $data);
        
        //päivityksestä jää jälki
		$this->mark_update($tnro, "Tallin tietojen päivitys");
		

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
		
		//päivityksestä jää jälki
		$this->mark_update($tnro, "Tallin lopetus");
    }
    
    function add_owner_to_stable($tnro, $applicant, $level)
    {
        $data = array('tnro' => $tnro, 'omistaja' => $applicant, 'taso' => $level);       
        $this->db->insert('vrlv3_tallirekisteri_omistajat', $data);
		
		//päivityksestä jää jälki
		$this->mark_update($tnro, "Omistajan (Taso " . $level . ") lisäys");

		

    }
    
    function get_stables_owners($tnro)
    {
        $this->db->select('omistaja, nimimerkki, taso, vrlv3_tallirekisteri_omistajat.id as id');
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
        $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu, katelyh, lopettanut');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro', 'left');
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
    
    
    
    function search_stables_updated()
    {
        $this->db->select('MAX(vrlv3_tallirekisteri_paivitetty.aika) as aika, vrlv3_tallirekisteri_paivitetty.tnro, nimi, perustettu, katelyh, lopettanut');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro', 'left');
        $this->db->join('vrlv3_lista_tallikategoriat', 'vrlv3_lista_tallikategoriat.kat = vrlv3_tallirekisteri_kategoriat.kategoria');
        $this->db->join('vrlv3_tallirekisteri_paivitetty', 'vrlv3_tallirekisteri_paivitetty.tnro = vrlv3_tallirekisteri.tnro');
        $this->db->group_by('tnro');
        $this->db->order_by('aika', 'DESC');
        
        $this->db->limit(100);
       
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {                
            
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    function search_stables_newest()
    {
        $this->db->select('vrlv3_tallirekisteri.tnro, nimi, perustettu, katelyh, lopettanut');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->join('vrlv3_tallirekisteri_kategoriat', 'vrlv3_tallirekisteri.tnro = vrlv3_tallirekisteri_kategoriat.tnro', 'left');
        $this->db->join('vrlv3_lista_tallikategoriat', 'vrlv3_lista_tallikategoriat.kat = vrlv3_tallirekisteri_kategoriat.kategoria');
        $this->db->order_by('perustettu', 'DESC');
        
        $this->db->limit(100);
       
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {                
            
            return $query->result_array(); 
        }
        
        return array();
    }
    


    //Kategoriat
    
    public function mass_edit_categories($tnro, $new_cats = array()){
                
        $old_cats = $this->get_stables_categories($tnro);

        $checked_cats = array();
        
        //are old categories still on new list? Delete if not.
        foreach ($old_cats as $oc){
            if (!array_search($oc['kategoria'], $new_cats)){
                $this->delete_category($oc['id']);
            }
            $checked_cats[] = $oc['kategoria'];          
        }
        
        //are there some new categories?
        
        foreach ($new_cats as $nc){
            if (!array_search($nc, $checked_cats)){
                $this->add_category_to_stable($tnro, $nc);
            }
            $checked_cats[] = $nc;          
        }      
     
        
    }
    
    function add_category_to_stable($tnro, $category, $applicant=NULL)
    {
        $data = array('tnro' => $tnro, 'kategoria' => $category, 'anoi' => $this->ion_auth->user()->row()->tunnus, 'hyvaksyi' => $this->ion_auth->user()->row()->tunnus);

        $data['lisatty'] = date("Y-m-d H:i:s");
        $data['kasitelty'] = $data['lisatty'];
        
        $this->db->insert('vrlv3_tallirekisteri_kategoriat', $data);
        
        //päivityksestä jää jälki
		$this->mark_update($tnro, "Kategorian ".$category." lisäys.");

    }
    
    function delete_category($id)
    {
        $this->db->select('tnro');
        $this->db->from('vrlv3_tallirekisteri_kategoriat');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        //päivityksestä jää jälki
		$this->mark_update($query->row()->tnro, "Kategorian poisto.");
        
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
        $this->db->select('katelyh, vrlv3_tallirekisteri_kategoriat.id, vrlv3_lista_tallikategoriat.kategoria as nimi, vrlv3_tallirekisteri_kategoriat.kategoria');
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
	
	    //Sekalaisia
    function is_name_in_use($tnro)
    {
        $this->db->select('tnro');
        $this->db->from('vrlv3_tallirekisteri');
        $this->db->where('nimi', $tnro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return true;
        }
        
        return false;
    }
	
	
	function mark_update($tnro, $text=null){
		$data = array('tnro' => $tnro, 'paivitti' => $this->ion_auth->user()->row()->tunnus, 'aika' => date("Y-m-d H:i:s"), 'text' => $text);
		$this->db->insert('vrlv3_tallirekisteri_paivitetty', $data);
	}
    

    
}

