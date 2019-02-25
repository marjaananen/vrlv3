<?php
class Profiili extends Loggedin_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //Kaikki funktiot täällä vaativat kirjautuneen käyttäjän, joten:
        if(!($this->ion_auth->logged_in()))
        {
            redirect('/');
        }
    }
    
    function index()
    {
        $this->load->model('tallit_model');
	$user = $this->ion_auth->user()->row();
	
	$vars = array();
	$vars['nimimerkki'] =  $user->nimimerkki;
        $vars['email'] = $user->email;
	$dateofbirth = date("d.m.Y", strtotime($user->syntymavuosi));
	$dateofaccept = date("d.m.Y", strtotime($user->hyvaksytty));
	$vars['syntymavuosi'] =  $dateofbirth;
	$vars['sijainti'] = $user->laani;
	$vars['hyvaksytty'] = $dateofaccept;
        
        $vars['stable_stats'] = $this->tallit_model->get_users_stable_stats($user->tunnus);
	
	$this->fuel->pages->render('profiili/index', $vars);
    }
    
    //OMAT-TIEDOT
    
    function tiedot()
    {
        $vars = array();
	$this->load->library('form_validation');
        $this->load->model('tunnukset_model');
        $user = $this->ion_auth->user()->row();
        
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $valid = true;
            $change_password = false;
            $previous_nick = $user->nimimerkki;
            $vars['fail_msg'] = '';
            
            $this->load->helper(array('form', 'url'));
            
            if($this->input->post('email') != $user->email) //validointi katsoo tietokannasta duplikaatit joten tee se vain jos vaihdetaan email
                $this->form_validation->set_rules('email', 'Sähköpostiosoite', 'valid_email|is_unique[vrlv3_tunnukset.email]|is_unique[vrlv3_tunnukset_jonossa.email]');
            
            $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "min_length[1]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]|numeric');
            $this->form_validation->set_rules('nayta_email', 'Sähköpostin näkyvyys', 'min_length[1]|max_length[1]|numeric|regex_match[/^[01]*$/]');
            $this->form_validation->set_rules('nayta_vuosi', 'Iän näkyvyys', 'min_length[1]|max_length[1]|numeric|regex_match[/^[01]*$/]');
            $this->form_validation->set_rules('nayta_laani', 'Sijainnin näkyvyys', 'min_length[1]|max_length[1]|numeric|regex_match[/^[01]*$/]');
            $this->form_validation->set_rules('salasana', 'Salasana', "min_length[6]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('uusi_salasana1', 'Uusi salasana', "min_length[6]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('uusi_salasana2', 'Toistettu uusi salasana', "min_length[6]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            
            if(!empty($this->input->post('salasana'))) //onko salasana ok
            {
                $valid = $this->ion_auth->hash_password_db($user->id, $this->input->post('salasana'));
                if($valid == false)
                    $vars['fail_msg'] = 'Annoit väärän salasanan!';
            }
            
            if($this->input->post('uusi_salasana1') != $this->input->post('uusi_salasana2'))
            {
                $valid = false;
                $vars['fail_msg'] = 'Uudet salasanat eivät vastaa toisiaan!';
            }
            else if(!empty($this->input->post('uusi_salasana1')) && empty($this->input->post('salasana')))
            {
                $valid = false;
                $vars['fail_msg'] = 'Salasanan vaihtaminen vaatii vanhan antamisen!';
            }
            else if(!empty($this->input->post('uusi_salasana1')) && !empty($this->input->post('salasana')))
                $change_password = true;
            
            if ($this->form_validation->run() == true && $valid == true)
            {
                $vars['success'] = true;
                $update_data = array();
                
                if(!empty($this->input->post('nimimerkki')))
                    $update_data['nimimerkki'] = $this->input->post('nimimerkki');
                    
                if(!empty($this->input->post('email')))
                    $update_data['email'] = $this->input->post('email');
                    
                if(!empty($this->input->post('sijainti')))
                    $update_data['laani'] = $this->input->post('sijainti');
                    
                $update_data['nayta_email'] = $this->input->post('nayta_email');    
                $update_data['nayta_vuosi'] = $this->input->post('nayta_vuosi');
                $update_data['nayta_laani'] = $this->input->post('nayta_laani');

                if(!empty($update_data))
                {
                    $vars['success'] = $this->ion_auth->update($user->id, $update_data);
                    
                    if($vars['success'] == true && !empty($this->input->post('nimimerkki')) && $this->input->post('nimimerkki') != $user->nimimerkki)
                        $this->tunnukset_model->add_previous_nickname($previous_nick, $user->tunnus);
                }
                
                if($change_password == true && $vars['success'] == true)
                {
                    $change_password = $this->ion_auth->change_password($user->tunnus, $this->input->post('salasana'), $this->input->post('uusi_salasana1'));
                    
                    if($change_password == false)
                        $vars['success'] = false;
                }
            }
        }
        
        // load form_builder
        $this->load->library('form_builder', array('submit_value' => 'Päivitä tiedot'));
        $user = $this->ion_auth->user()->row();

        $dateofbirth = date("d.m.Y", strtotime($user->syntymavuosi));
        $options = $this->tunnukset_model->get_location_option_list();
        $contacts_html = '';
        
        foreach($this->tunnukset_model->get_users_contacts($user->tunnus) as $row)
        {
            if(empty($contacts_html))
                $contacts_html .= "<ul>";
                
            $contacts_html .= "<li><b>" . $row['tyyppi'] . ": </b>" . $row['tieto'] . "</li>";
        }
    
        if(!empty($contacts_html))
            $contacts_html .= "</ul><br />";
        
        // create fields
        $fields['nimimerkki'] = array('type' => 'text', 'value' => $user->nimimerkki, 'class'=>'form-control');
        $fields['email'] = array('type' => 'text', 'value' => $user->email, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">Anna toimiva osoite tai saatat menettää tunnuksesi!</span>', 'class'=>'form-control');
        $fields['nayta_email'] = array('type' => 'checkbox', 'checked' => $user->nayta_email, 'label' => 'Näytetäänkö sähköposti julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö sähköposti julkisesti profiilissasi.</span>', 'class'=>'form-control');
        $fields['muut_yhteystiedot'] = array('type' => 'section', 'tag' => 'label', 'value' => 'Muut yhteystiedot', 'display_label' => false, 'after_html' => $contacts_html . '<span class="form_comment"><a href="' . site_url('/profiili/muokkaa_yhteystietoja') . '">Muokkaa yhteystietoja</a></span>');
        $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'disabled' => 'disabled', 'size' => '10', 'value' => $dateofbirth, 'class'=>'form-control');
        $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'value' => $user->laani, 'class'=>'form-control');
        $fields['nayta_vuosi'] = array('type' => 'checkbox', 'checked' => $user->nayta_vuosi, 'label' => 'Näytetäänkö ikä julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö tiedot julkisesti profiilissasi. Huom! Vain yli 16-vuotiaiden tiedot voidaan näyttää profiilissa.</span>', 'class'=>'form-control');
        $fields['nayta_laani'] = array('type' => 'checkbox', 'checked' => $user->nayta_laani, 'label' => 'Näytetäänkö sijainti julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö tiedot julkisesti profiilissasi. Huom! Vain yli 16-vuotiaiden tiedot voidaan näyttää profiilissa.</span>', 'class'=>'form-control');
        $fields['salasana'] = array('type' => 'password', 'class'=>'form-control');
        $fields['uusi_salasana1'] = array('type' => 'password', 'label' => 'Uusi salasana', 'class'=>'form-control');
        $fields['uusi_salasana2'] = array('type' => 'password', 'label' => 'Toista uusi salasana', 'after_html' => '<span class="form_comment">Täytä salasanakentät vain jos haluat vaihtaa salasanasi. Salasanassa tulee olla vähintään 6 merkkiä. Salasana tulee voimaan heti, eikä sinun tarvitse kirjautua sisään uudelleen.</span>', 'class'=>'form-control');
        
        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('profiili/tiedot'));

        // render the page
        $vars['profile_form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields );
        
        $this->fuel->pages->render('profiili/tiedot', $vars);
    }
    
    function muokkaa_yhteystietoja()
    {
        $vars = array();
        $vars['success'] = false;
	$this->load->library('form_validation');
        $this->load->model('tunnukset_model');
        $user = $this->ion_auth->user()->row();
        
        // load form_builder
        $this->load->library('form_builder', array('submit_value' => 'Lisää'));
        $options = array('irc' => 'IRC', 'line' => 'Line', 'skype' => 'Skype', 'www' => 'WWW');
        
        // create fields
        $fields['tyyppi'] = array('type' => 'select', 'options' => $options, 'value' => 'www', 'class'=>'form-control');
        $fields['yhteystieto'] = array('type' => 'text', 'class'=>'form-control');
        $fields['nayta'] = array('type' => 'checkbox', 'checked' => 1, 'label' => 'Näytetäänkö julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö yhteystieto julkisesti profiilissasi. Huom! Vain yli 16-vuotiaiden tiedot voidaan näyttää profiilissa.</span>', 'class'=>'form-control');

        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('profiili/muokkaa_yhteystietoja'));

        // render the page
        $vars['add_contacts_form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields );
            
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $this->form_validation->set_rules('tyyppi', 'Tyyppi', "required|min_length[1]|max_length[10]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('yhteystieto', 'Yhteystieto', "required|min_length[1]|max_length[128]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('nayta', 'Näkyvyys', 'min_length[1]|max_length[1]|numeric|regex_match[/^[01]*$/]');
            
            if ($this->form_validation->run() == true)
            {
                $this->tunnukset_model->add_contact($user->tunnus, $this->input->post('tyyppi'), $this->input->post('yhteystieto'), $this->input->post('nayta'));
                $vars['success'] = true;
            }
        }
        
        $vars['contact_info'] = $this->tunnukset_model->get_users_contacts($user->tunnus);
        $this->fuel->pages->render('profiili/muokkaa_yhteystietoja', $vars);
    }
    
    function poista_yhteystieto($id)
    {
        if(!empty($id))
        {
            $this->load->model('tunnukset_model');
            $user = $this->ion_auth->user()->row();
            $this->tunnukset_model->delete_contact($user->tunnus, $id);
        }
        
        redirect('profiili/muokkaa_yhteystietoja');
    }
    
    //PIKAVIESTIT
    
    function pikaviestit()
    {
        $vars = array();
        $vars['success'] = false;
	$this->load->library('form_validation');
        $this->load->model('tunnukset_model');
        $user = $this->ion_auth->user()->row();
     
        // load form_builder
        $this->load->library('form_builder', array('submit_value' => 'Lähetä'));
        
        // create fields
        $fields['vastaanottaja'] = array('type' => 'text', 'class'=>'form-control');
        $fields['viesti'] = array('type' => 'text', 'class'=>'form-control');

        $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('profiili/pikaviestit'));

        // render the page
        $vars['quick_messages_form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields );
            
        if($this->input->server('REQUEST_METHOD') == 'POST')
        {
            $this->form_validation->set_rules('vastaanottaja', 'Vastaanottaja', "required|min_length[5]|max_length[9]|callback__pinnumber_check");
            $this->form_validation->set_rules('viesti', 'Viesti', "required|min_length[1]|max_length[360]|regex_match[/^[A-Za-zÄäÖöÅå0-9_\-.:,; *~#&'@()]*$/]");
            
            if ($this->form_validation->run() == true)
            {
		$this->load->library('Vrl_helper');
		
		$recipient = $this->vrl_helper->vrl_to_number($this->input->post('vastaanottaja'));
                $this->tunnukset_model->send_message($user->tunnus, $recipient, $this->input->post('viesti'));
                $vars['success'] = true;
		
            }
        }
        
        $vars['messages'] = $this->tunnukset_model->get_users_messages($user->tunnus);
        $this->fuel->pages->render('profiili/pikaviestit', $vars);
    }
    
    function poista_pikaviesti($id)
    {
        if(!empty($id))
        {
            $this->load->model('tunnukset_model');
            $user = $this->ion_auth->user()->row();
            $this->tunnukset_model->delete_message($user->tunnus, $id);
        }
        
        redirect('profiili/pikaviestit');
    }
    
    function aseta_tarkeys($id, $important)
    {
        if(!empty($id))
        {
	    $this->load->model('tunnukset_model');
	    $user = $this->ion_auth->user()->row();
	    if ($important == "0" || empty($important)){
		$this->tunnukset_model->mark_as_important($user->tunnus, $id);
	    }
            else if ($important == "1"){
		$this->tunnukset_model->mark_as_unimportant($user->tunnus, $id);
	    }  
        }        
        redirect('profiili/pikaviestit');
    }
    

    
    //MISC
    
    function _pinnumber_check($id)
    {
	$this->load->library('Vrl_helper');
	 

	if ($this->vrl_helper->check_vrl_syntax($id) && $this->ion_auth->identity_check($id))
	{
	    return TRUE;
	}
	else
	{
	    $this->form_validation->set_message('pinnumber_check', 'Kyseistä VRL-tunnusta ei ole olemassa!');
	    return FALSE;
		
	}

    }
}
?>






