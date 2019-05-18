<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/Base_module_model.php');
 
class Kasvattajanimi_model extends Base_module_model
{ 
    function __construct()
    {
        parent::__construct();
    }
    
    
    //names
    function get_users_names($pinnumber)
    {
        $this->db->select('vrlv3_kasvattajanimet.id, kasvattajanimi, rekisteroity, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_omistajat', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_omistajat.kid');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        $this->db->where('vrlv3_kasvattajanimet_omistajat.tunnus', $pinnumber);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
    
    function get_name($nro)
    {
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->where('id', $nro);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row_array(); 
        }
        
        return array();
    }
        

    
    function search_names($name, $breed)
    {
        $this->db->select('vrlv3_kasvattajanimet.id, kasvattajanimi, rekisteroity, lyhenne');
        $this->db->from('vrlv3_kasvattajanimet');
        $this->db->join('vrlv3_kasvattajanimet_rodut', 'vrlv3_kasvattajanimet.id = vrlv3_kasvattajanimet_rodut.kid', 'left');
        $this->db->join('vrlv3_lista_rodut', 'vrlv3_lista_rodut.rotunro = vrlv3_kasvattajanimet_rodut.rotu');
        
        if(!empty($name))
        {
            if(strpos($name, '*') !== false)
                $this->db->where('kasvattajanimi LIKE "' . str_replace('*', '%', $name) . '"');
            else
                $this->db->where('kasvattajanimi', $name);
        }
            
        if($breed != '-1')
            $this->db->where('vrlv3_kasvattajanimet_rodut.rotu', $breed);
        
   
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        
        return array();
    }
    
  /*  
    
    function search_stables_updated()
    {
        $this->db->select('MAX(vrlv3_tallirekisteri_paivitetty.aika) as aika, vrlv3_tallirekisteri_paivitetty.tnro, nimi, perustettu');
        $this->db->from('vrlv3_tallirekisteri');
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
        $this->db->select('tnro, nimi, perustettu');
        $this->db->from('vrlv3_tallirekisteri');
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
        $this->db->select('katelyh, vrlv3_tallirekisteri_kategoriat.id, vrlv3_tallirekisteri_kategoriat.kategoria');
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
    */

    
}

