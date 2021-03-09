<?php
class Yllapito_tiedotukset extends CI_Controller
{
    
    private $allowed_user_groups = array('admin', 'tiedotukset', 'tunnukset', 'jaos',  'alayhdistys','tyovoima', 'jaos-yp', 'alayhdistys-yp');
    private $url;
    
    function __construct()
    {
        parent::__construct();
              
        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
        if (!$this->user_rights->is_allowed()){       
            redirect($this->user_rights->redirect());
        }
        $this->load->model('Uutiset_model');
        $this->url = "yllapito/tiedotukset/";
        

    }

    function index(){
$this->lisaa();
    }
   
    
    
	
    
    function lisaa ($msg = array()){
        
         $data = $msg;
         $data['title'] = "Tiedotukset";
         $data['text_view'] = $this->load->view('yllapito/tiedotukset', NULL, TRUE);
         

        //start the form
            
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $tid = 0;
            if ($this->_validate_form('new') == FALSE)
                {
                    $data['msg'] = "Julkaisu epäonnistui!";
                    $data['msg_type'] = "danger";
                    $data['form'] = $this->_get_form('new', 0, array("otsikko"=>$this->input->post('otsikko'), "teksti"=>$this->input->post('teksti')));

                }

            else
                {

                    $data['form'] = $this->_get_form('new');

                    //add 
                    $tid = $this->Uutiset_model->laheta_tiedotus($this->input->post('otsikko'), $this->input->post('teksti'), $this->input->post('kategoriat'));
                    $data['msg'] = "Julkaisu onnistui! Katso uusi tiedote <a href=\"".site_url('liitto/tiedotus/'.$tid) ."\">täältä</a>.";
                    $data['msg_type'] = "success";
                }
        }
        
        else  {
            $data['form'] = $this->_get_form('new');
        }
                
        //start the list      
        $data['tulokset'] = $this->_tiedotustaulukko();
        $this->fuel->pages->render('misc/haku', $data);
             
    
    }
    
    
    
    function poista ($id){
        $msg = "";
        if($this->_is_editing_allowed($id, $msg)){
            $this->Uutiset_model->delete_tiedotus($id);
            $this->lisaa(array('msg_type' => 'success', 'msg' => "Poisto onnistui!"));
            
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
        }
        
    }
    
    function muokkaa ($id){
        $msg = "";
        if($this->_is_editing_allowed($id, $msg)){
             $data = array();
            $data['title'] = "Tiedotukset";
            $data['text_view'] = $this->load->view('yllapito/tiedotukset', NULL, TRUE);
    
        //start the form
            
            if($this->input->server('REQUEST_METHOD') == 'POST'){
                if ($this->_validate_form('edit') == FALSE)
                    {
                        $data['msg'] = "Julkaisu epäonnistui!";
                        $data['msg_type'] = "danger";
                        $data['form'] = $this->_get_form('edit', $id, array("otsikko"=>$this->input->post('otsikko'), "teksti"=>$this->input->post('teksti')));    
                        $this->fuel->pages->render('misc/haku', $data);

                    }

                else
                    {           
                        $this->Uutiset_model->muokkaa_tiedotus($id, $this->input->post('otsikko'), $this->input->post('teksti'), $this->input->post('kategoriat'));
                        $msg = "Muokkaus onnistui! Katso uusi tiedote <a href=\"".site_url('liitto/tiedotus/'.$id) ."\">täältä</a>.";
                        $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'success', 'msg' => $msg));
                    }
            }
        
            else  {
                $data['form'] = $this->_get_form('edit', $id);
                $this->fuel->pages->render('misc/haku', $data);
            }
                
 
        } else {
            $this->fuel->pages->render('misc/naytaviesti', array('msg_type' => 'danger', 'msg' => $msg));
        }
    }
    
    private function _get_form($mode, $id = 0, $newsitem = array()){
        if ($mode == "edit"){           
            $newsitem = $this->Uutiset_model->hae_tiedotus($id);
            $newsitem = $newsitem[0];
            $this->load->library('form_builder', array('submit_value' => "Tallenna", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'muokkaa/'.$id));
           
        }
        else {
            if (!isset ($newsitem['otsikko'])) {$newsitem['otsikko'] ="";}
            if (!isset ($newsitem['teksti'])) {$newsitem['teksti'] ="";}
            if (!isset ($newsitem['kategoriat'])) {$newsitem['kategoriat'] =array();}
            //$newsitem['kategoriat'] = array ("1", "2");
            $this->load->library('form_builder', array('submit_value' => "Julkaise", 'required_text' => '*Pakollinen kenttä'));
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url($this->url .'lisaa'));
        }     
            
        $fields['otsikko'] = array('type' => 'text', 'required' => TRUE, 'value' => $newsitem['otsikko'], 'class'=>'form-control');
        $fields['teksti'] = array('type' => 'textarea', 'required' => TRUE, 'value' => $newsitem['teksti'], 'cols' => 40, 'rows' => 3, 'class'=>'form-control');      
        $options = $this->Uutiset_model->hae_kategoriat_options();
        $kategorialista = array();
        foreach ($newsitem['kategoriat'] as $kat){
            $kategorialista[]=$kat['kid'];
        }
        $fields['kategoriat'] = array('type' => 'multi', 'required' => TRUE, 'options' => $options, 'value' => $kategorialista, 'class'=> 'form-control custom-select');
        return  $this->form_builder->render_template('_layouts/basic_form_template', $fields);
    }
    
    private function _validate_form()
    {
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('otsikko', 'Otsikko', "required|min_length[1]|max_length[240]");
        $this->form_validation->set_rules('teksti', 'Teksti', "required|max_length[2048]");
        return $this->form_validation->run();
    }
    
    
    private function _tiedotustaulukko(){
                                //start the list
        $vars['headers'][1] = array('title' => 'ID', 'key' => 'tid', 'key_link' => site_url('liitto/tiedotus/'));
        $vars['headers'][2] = array('title' => 'Pvm', 'key' => 'aika', 'type' => 'date');
        $vars['headers'][3] = array('title' => 'Otsikko', 'key' => 'otsikko');
        $vars['headers'][4] = array('title' => 'Poista', 'key' => 'tid', 'key_link' => site_url($this->url.'poista/'), 'image' => site_url('assets/images/icons/delete.png'));
        $vars['headers'][5] = array('title' => 'Editoi', 'key' => 'tid', 'key_link' => site_url($this->url.'muokkaa/'), 'image' => site_url('assets/images/icons/edit.png'));
        
        
        $vars['headers'] = json_encode($vars['headers']);
        
        $vars['data'] = json_encode($this->Uutiset_model->hae_tiedotukset(10,0));
        
        return  $this->load->view('misc/taulukko', $vars, TRUE);
        
    }
    
    private function _is_editing_allowed($tid, &$msg){
        $newsitem = $this->Uutiset_model->hae_tiedotus($tid);
        
        if (sizeof($newsitem) != 1){
            $msg = "Tiedotetta jota yrität muokata ei ole olemassa.";
            return false;
        }
        
        
        $start = new DateTime();
        $start->setTimestamp(strtotime($newsitem[0]['aika']));
        
        $end   = new DateTime();
        $end->setTimestamp(time());
        $diff  = $start->diff($end);


        $months =  $diff->format('%y') * 12 + $diff->format('%m');
        
        if($months > 6){
            $msg = "Tiedote on liian vanha muokattavaksi. Vanhat tiedotteet jätetään arkistoon osana harrastuksen historiaa.";
            return false;
       }
       
       return true;
        
        
        
    }
    
}
    

?>