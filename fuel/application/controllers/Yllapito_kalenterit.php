<?php
class Yllapito_kalenterit extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'jaos', 'jaos-yp', 'kisakalenteri');
    private $url;
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
       
       $this->load->model('Jaos_model');
              $this->load->library('Kisajarjestelma');

        $this->url = "yllapito/kalenterit/";
        
    }
    
    private function _is_allowed_to_process_calendar($jaos, &$msg)	{
		//are you admin or editor?
        if(!($this->_is_jaos_admin() || ($this->Jaos_model->is_jaos_owner($this->ion_auth->user()->row()->tunnus, $jaos)))){
			$msg = "Jos et ole ylläpitäjä tai jaosvastaava, voit käsitellä vain omia jaoksiasi, tai niitä jaoksia joissa toimit kalenterityöntekijänä.";
			return false;
		}
		
		
		return true;		
		
	
    }
    
    private function _is_jaos_admin(){
        return $this->user_rights->is_allowed(array('admin', 'jaos'));
    }
    
    public function pipari(){
		$this->fuel->pages->render('misc/pipari');
	}
    

    public function index(){
        $data = array();
        $data['jaokset'] = $this->Jaos_model->get_jaos_list();
        $data['url'] = $this->url;
        
        foreach ($data['jaokset'] as &$jaos){
           // $this->_sort_panel_info_applications('hakemukset_porr', $this->raceApplicationsMaintenance($jaos['id'], true), $jaos);
           $this->_sort_panel_info_applications('hakemukset_norm', $this->raceApplicationsMaintenance($jaos['id'], false), $jaos);
           //$this->_sort_panel_info_applications('tulokset_porr', $this->resultApplicationsMaintenance($jaos['id'], true), $jaos);
           $this->_sort_panel_info_applications('tulokset_norm', $this->resultApplicationsMaintenance($jaos['id'], false), $jaos);   
        }      
    
    	$this->fuel->pages->render('yllapito/kisakalenteri/kisakalenterit_etusivu', $data);


    }
    
    private function _sort_panel_info_applications($key, $data, &$jaos){
        $jaos[$key] = $data['kpl'];
        if($jaos[$key] > 0){
            $jaos[$key . '_latest'] = $data['ilmoitettu'];
        }

    }
    
    

    
    public function hyvaksytyttulokset(){
        $this->pipari();
    }
    
    public function hyvaksytytkisat(){
        $this->pipari();
    }
    
    
    
    
    #### Ylläpidon funktiot ####

	function raceApplicationsMaintenance( $jaos, $leveled = false ) {
        
        $this->db->select('COUNT(kisa_id) as kpl, MIN(ilmoitettu) as ilmoitettu');
        $this->db->from('vrlv3_kisat_kisakalenteri');
        $this->db->where('jaos', $jaos);
        $this->db->where ('vanha', 0);
        $this->db->where('porrastettu', $leveled);
        $this->db->where('hyvaksytty', NULL);
        if($leveled){
            $this->load->library("Kisajarjestelma");     
            $this->db->where('ilmoitettu <', $this->kisajarjestelma->new_leveled_start_time());

        }
        
		 $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]; 
        }else {
            return array('kpl'=>0, 'ilmoitettu'=>NULL);
        }
		
	}
    
    function resultApplicationsMaintenance( $jaos, $leveled = false ) {
         $this->db->select('COUNT(t.tulos_id) as kpl, MIN(t.ilmoitettu) as ilmoitettu');
        $this->db->from('vrlv3_kisat_tulokset as t');
        $this->db->join('vrlv3_kisat_kisakalenteri as k', 't.kisa_id = k.kisa_id');
        $this->db->where('k.jaos', $jaos);
        $this->db->where ('k.vanha', 0);
        $this->db->where('k.porrastettu', $leveled);
        $this->db->where('t.hyvaksytty', NULL);

        
		 $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result_array()[0]; 
        }else {
            return array('kpl'=>0, 'ilmoitettu'=>NULL);
        }
    			
	}
	
   
   ///////////////////////////////////////////////////////////////////////////////
    // Kisakalenterin ylläpitofunktiot
    //////////////////////////////////////////////////////////////////////////////
    
   
    public function kisahyvaksynta ($jaos = null, $kasittele = null, $kisa_id = null){
        $data=array();
        $this->load->model('Tunnukset_model');
        $jaos_data = $this->Jaos_model->get_jaos($jaos);
        $msg = "";
        
        if(empty($jaos)){
            $data['jaokset'] = $this->Jaos_model->get_jaos_list();
            $data['url'] = $this->url;
            
            foreach ($data['jaokset'] as &$jaos){
               $this->_sort_panel_info_applications('hakemukset_norm', $this->raceApplicationsMaintenance($jaos['id'], false), $jaos);
            }      
        
        } 
        
        else if(sizeof($jaos_data) == 0){
            $data['msg'] = "Hakemaasi Jaosta (".$jaos.") ei ole olemassa.";
            $data['msg_type'] = "danger";
        }else if (!$this->_is_allowed_to_process_calendar($jaos, $msg)){
             $data['msg'] = "Sinulla ei ole oikeutta jaoksen ".$jaos_data['lyhenne']." kilpailukalenteriin. " . $msg;
            $data['msg_type'] = "danger";
        }else {

            if(isset($kasittele) && $kasittele == 'hyvaksy'){
                if($this->_approve_competition($jaos, $kisa_id, true)){
                    $data['msg'] = "Kutsu hyväksytty.";
                    $data['msg_type'] = "success";
                }else {
                    $data['msg'] = "Kutsua #".$kisa_id." ei enää löydy, tai se on jonkun toisen käsiteltävänä.";
                    $data['msg_type'] = 'danger';
    
                }
                
            }else if( isset($kasittele) && $kasittele == 'hylkaa'){
                if($this->_approve_competition($jaos, $kisa_id, false, $this->input->post("viesti", TRUE))){
                    $data['msg'] = "Kutsu hylätty.";
                    $data['msg_type'] = "success";
                }else {
                    $data['msg'] = "Kutsua #".$kisa_id." ei enää löydy, tai se on jonkun toisen käsiteltävänä.";
                    $data['msg_type'] = 'danger';
    
                }
            }
            
            $data['kutsu'] = $this->competitions_queue_get_next($jaos);
            
            if(isset($data['kutsu']) &&  sizeof($data['kutsu']) > 0){
                $this->load->model('Sport_model');
                $data['kutsu']['laji'] = $this->Sport_model->get_sport_info($data['kutsu']['laji'])['painotus'];
                $this->load->model("Tallit_model");
                $data['talli'] = $this->Tallit_model->get_stable($data['kutsu']['jarj_talli']);
                $data['jaos'] = $jaos_data;
                $data['kutsu']['arvontatapa'] = $this->kisajarjestelma->arvontatavat_options_legacy()[$data['kutsu']['arvontatapa']];
                $user = $this->ion_auth->user($this->Tunnukset_model->get_users_id($data['kutsu']['tunnus']))->row();
                $data['username'] = $user->nimimerkki;
                $data['user_email'] = $user->email;
                $data['user_vrl'] = $this->vrl_helper->get_vrl($user->tunnus);
            }
            

        }
        
        if(isset($data['kutsu'])){
                        $this->fuel->pages->render('yllapito/kisakalenteri/kisahyvaksynta', $data);
        }else {
                $this->fuel->pages->render('yllapito/kisakalenteri/kisakalenteri_kisahyvaksynta_main', $data);
        }

        
    }
    

    public function tuloshyvaksynta ($jaos = null, $kasittele = null, $tulos_id = null){
        $data=array();
        $this->load->model('Tunnukset_model');
        $jaos_data = $this->Jaos_model->get_jaos($jaos);
        $msg = "";
        
        if(empty($jaos)){
            $data['jaokset'] = $this->Jaos_model->get_jaos_list();
            $data['url'] = $this->url;
            
            foreach ($data['jaokset'] as &$jaos){
               $this->_sort_panel_info_applications('tulokset_norm', $this->resultApplicationsMaintenance($jaos['id'], false), $jaos);
               $this->_sort_panel_info_applications('tulokset_porr', $this->resultApplicationsMaintenance($jaos['id'], true), $jaos);

            }      
        
        } 
        
        else if(sizeof($jaos_data) == 0){
            $data['msg'] = "Hakemaasi Jaosta (".$jaos.") ei ole olemassa.";
            $data['msg_type'] = "danger";
        }else if (!$this->_is_allowed_to_process_calendar($jaos, $msg)){
             $data['msg'] = "Sinulla ei ole oikeutta jaoksen ".$jaos_data['lyhenne']." kilpailukalenteriin. " . $msg;
            $data['msg_type'] = "danger";
        }else {

            if(isset($kasittele) && $kasittele == 'hyvaksy'){
                if($this->_approve_result($jaos, $result_id, true)){
                    $data['msg'] = "Tulokset hyväksytty.";
                    $data['msg_type'] = "success";
                }else {
                    $data['msg'] = "Tulosta #".$tulos_id." ei enää löydy, tai se on jonkun toisen käsiteltävänä.";
                    $data['msg_type'] = 'danger';
    
                }
                
            }else if( isset($kasittele) && $kasittele == 'hylkaa'){
                if($this->_approve_result($jaos, $result_id, false, $this->input->post("viesti", TRUE))){
                    $data['msg'] = "Tulos hylätty.";
                    $data['msg_type'] = "success";
                }else {
                    $data['msg'] = "Tulosta #".$result_id." ei enää löydy, tai se on jonkun toisen käsiteltävänä.";
                    $data['msg_type'] = 'danger';
    
                }
            }
            
            $data['tulos'] = $this->result_queue_get_next($jaos);
            
            if(isset($data['tulos']) &&  sizeof($data['tulos']) > 0){
                $this->load->model('Sport_model');
                $data['kutsu']['laji'] = $this->Sport_model->get_sport_info($data['kutsu']['laji'])['painotus'];
                $this->load->model("Tallit_model");
                $data['talli'] = $this->Tallit_model->get_stable($data['kutsu']['jarj_talli']);
                $data['jaos'] = $jaos_data;
                $data['kutsu']['arvontatapa'] = $this->kisajarjestelma->arvontatavat_options_legacy()[$data['kutsu']['arvontatapa']];
                $user = $this->ion_auth->user($this->Tunnukset_model->get_users_id($data['kutsu']['tunnus']))->row();
                $data['username'] = $user->nimimerkki;
                $data['user_email'] = $user->email;
                $data['user_vrl'] = $this->vrl_helper->get_vrl($user->tunnus);
            }
            

        }
        
        if(isset($data['tulos'])){
                        $this->fuel->pages->render('yllapito/kisakalenteri/tuloshyvaksynta', $data);
        }else {
                $this->fuel->pages->render('yllapito/kisakalenteri/kisakalenteri_tuloshyvaksynta_main', $data);
        }

        
    }

    
    
    public function competitions_queue_get_next($jaos, $porrastettu = false){
      return $this->_get_next('vrlv3_kisat_kisakalenteri', $jaos, $porrastettu);
    }
    
    public function results_queue_get_next($jaos, $porrastettu = false){
      return $this->_get_next('vrl_kisat_tulokset', $jaos, $porrastettu);
    }
    
    
     private function _get_next($table, $jaos, $porrastettu)
    {
        $data = array();
        $date = new DateTime();
        $date->setTimestamp(time() - 60*15); //nykyhetki miinus 15min, eli ei saa ottaa samaa jonoitemiä uudestaan käsittelyyn 15 minuuttiin
        
        $this->db->from($table);
        $this->db->where('jaos', $jaos);
        $this->db->where('porrastettu', $porrastettu);
        $this->db->where('vanha', 0);
        $this->db->group_start();
        $this->db->where('hyvaksytty IS NULL OR hyvaksytty = \'0000-00-00 00:00:00\'');
        $this->db->group_end();
        $this->db->group_start();

        $this->db->where('kasitelty IS NULL OR kasitelty < \'' . $date->format('Y-m-d H:i:s') . '\'');
                $this->db->group_end();

        $this->db->order_by("ilmoitettu", "asc"); 
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $data = $query->row_array(); 

            $date->setTimestamp(time());
            $user = $this->ion_auth->user()->row();
            $update_data = array('kasitelty' => $date->format('Y-m-d H:i:s'), 'kasittelija'=> $this->ion_auth->user()->row()->tunnus);
            
            $id_key = "tulos_id";
            if($table == 'vrlv3_kisat_kisakalenteri'){
                $id_key = 'kisa_id';
            }
            $this->db->where($id_key, $data[$id_key]);
            $this->db->update($table, $update_data);
                        
        }
        
        return $data;
    }
    
    private function _approve_competition ($jaos, $kisa_id, $approve, $disapprove_msg = false){
        $processing_ok = false;
        $vrl = $this->ion_auth->user()->row()->tunnus;
        $this->db->trans_start();
        $this->db->select('*');
        $this->db->from('vrlv3_kisat_kisakalenteri');
        $this->db->where('jaos', $jaos);
        $this->db->where('kisa_id', $kisa_id);
        $this->db->where('kasittelija', $vrl);
        $this->db->where('hyvaksytty', NULL);
                $query = $this->db->get();

        
        if ($query->num_rows() > 0)
        {
            if($approve){
                
                $date = new DateTime();
                $date->setTimestamp(time());
                $insert_data = array('hyvaksytty'=> $date->format('Y-m-d H:i:s'), 'hyvaksyi'=>$vrl);
                $where_data = array('jaos'=> $jaos, 'kisa_id' => $kisa_id, 'kasittelija' => $vrl);
                $this->db->where($where_data);
                $this->db->update('vrlv3_kisat_kisakalenteri', $insert_data);

                $this->load->model('Tunnukset_model');
                $this->Tunnukset_model->send_message($vrl, $query->result_array()[0]['tunnus'] , "Kilpailukutsu #".$query->result_array()[0]['kisa_id'])." on hyväksytty kalenteriin!";
            }
            
            else {
                    $where_data = array('jaos'=> $jaos, 'kisa_id' => $kisa_id, 'kasittelija' => $vrl);
                    $this->db->delete('vrlv3_kisat_kisakalenteri', $where_data);
                    $this->load->model('Tunnukset_model');
                    $this->Tunnukset_model->send_message($vrl, $query->result_array()[0]['tunnus'] ,
                                                         "Kilpailukutsu #".$query->result_array()[0]['kisa_id'])." on hylätty! Syy: " . $disapprove_msg;


            }
            
            $this->db->trans_complete();    
            return true;
        }else {
             $this->db->trans_complete();
             return false;
        }

        
        
    }
   
    

    
}
    

?>