<?php
class Profiili extends Loggedin_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function tiedot()
    {
        $vars = array();
	$this->load->library('form_validation');
        $this->load->model('tunnukset_model');
        $user = $this->ion_auth->user()->row();
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Päivitä tiedot'));

            $dateofbirth = date("d.m.Y", strtotime($user->syntymavuosi));
            $options = $this->tunnukset_model->get_location_option_list();
            
            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'value' => $user->nimimerkki, 'class'=>'form-control');
            $fields['email'] = array('type' => 'text', 'value' => $user->email, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">Anna toimiva osoite tai saatat menettää tunnuksesi!</span>', 'class'=>'form-control');
            $fields['nayta_email'] = array('type' => 'checkbox', 'checked' => $user->nayta_email, 'label' => 'Näytetäänkö sähköposti julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö sähköposti julkisesti profiilissasi.</span>', 'class'=>'form-control');
            $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'disabled' => 'disabled', 'size' => '10', 'value' => $dateofbirth, 'class'=>'form-control');
            $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'value' => $user->laani, 'class'=>'form-control');
            $fields['nayta_vuosilaani'] = array('type' => 'checkbox', 'checked' => $user->nayta_vuosilaani, 'label' => 'Näytetäänkö sijainti ja ikä julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö tiedot julkisesti profiilissasi. Huom! Vain yli 16-vuotiaiden tiedot voidaan näyttää profiilissa.</span>', 'class'=>'form-control');
            $fields['salasana'] = array('type' => 'password', 'class'=>'form-control');
            $fields['uusi_salasana1'] = array('type' => 'password', 'label' => 'Uusi salasana', 'class'=>'form-control');
            $fields['uusi_salasana2'] = array('type' => 'password', 'label' => 'Toista uusi salasana', 'after_html' => '<span class="form_comment">Täytä salasanakentät vain jos haluat vaihtaa salasanasi. Salasanassa tulee olla vähintään 6 merkkiä. Salasana tulee voimaan heti, eikä sinun tarvitse kirjautua sisään uudelleen.</span>', 'class'=>'form-control');
            
            $this->form_builder->form_attrs = array('method' => 'post', 'action' => site_url('/profiili'));
    
            // render the page
            $vars['profile_form'] = $this->form_builder->render_template('_layouts/basic_form_template', $fields );
            
            $this->fuel->pages->render('profiili/tiedot', $vars);
        }
        else if($this->input->server('REQUEST_METHOD') == 'POST')
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
            $this->form_validation->set_rules('nayta_vuosilaani', 'Sijainnin ja iän näkyvyys', 'min_length[1]|max_length[1]|numeric|regex_match[/^[01]*$/]');
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
                $update_data['nayta_vuosilaani'] = $this->input->post('nayta_vuosilaani');

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
            
            $this->fuel->pages->render('profiili/tiedot', $vars);
        }
        else
            redirect('/', 'refresh');
    }
}
?>