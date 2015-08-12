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
        
        if($this->input->server('REQUEST_METHOD') == 'GET')
        {
            // load form_builder
            $this->load->library('form_builder', array('submit_value' => 'Päivitä tiedot'));
            $this->load->model('tunnukset_model');
            
            $user = $this->ion_auth->user()->row();
            $dateofbirth = date("d.m.Y", strtotime($user->syntymavuosi));
            $options = $this->tunnukset_model->get_location_option_list();
            
            // create fields
            $fields['nimimerkki'] = array('type' => 'text', 'value' => $user->nimimerkki, 'class'=>'form-control');
            $fields['email'] = array('type' => 'text', 'value' => $user->email, 'label' => 'Sähköpostiosoite', 'after_html' => '<span class="form_comment">Anna toimiva osoite tai saatat menettää tunnuksesi!</span>', 'class'=>'form-control');
            $fields['nayta_email'] = array('type' => 'checkbox', 'checked' => $user->nayta_email, 'label' => 'Näytetäänkö sähköposti julkisesti?', 'after_html' => '<span class="form_comment">Näytetäänkö sähköposti julkisesti profiilissasi.</span>', 'class'=>'form-control');
            $fields['syntymavuosi'] = array('type' => 'text', 'label' => 'Syntymäaika', 'disabled' => 'disabled', 'size' => '10', 'value' => $dateofbirth, 'class'=>'form-control');
            $fields['sijainti'] = array('type' => 'select', 'options' => $options, 'first_option' => $this->tunnukset_model->get_location($user->laani), 'class'=>'form-control');
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
            $this->load->helper(array('form', 'url'));
            $this->load->model('tunnukset_model');
            
            $this->form_validation->set_rules('nimimerkki', 'Nimimerkki', "min_length[1]|max_length[20]|regex_match[/^[A-Za-z0-9_\-.:,; *~#&'@()]*$/]");
            $this->form_validation->set_rules('email', 'Sähköpostiosoite', 'valid_email|is_unique[vrlv3_tunnukset.email]|is_unique[vrlv3_tunnukset_jonossa.email]');
            $this->form_validation->set_rules('sijainti', 'Sijainti', 'min_length[1]|max_length[2]|numeric');
            //loput validoinnit!!
            //mm. sähköpostin unique check vs oma olemassa oleva, salasanat ja uusien salasanojen vastaavuus keskenään, checkboxit
            
            if ($this->form_validation->run() != FALSE)
            {
                $vars['success'] = true;
                //muutokset!!
                //mutta ei syntymäaikaa
                
                //muista nimimerkin muutoksen talletus muuhun tauluun
            }
            
            $this->fuel->pages->render('profiili/tiedot', $vars);
        }
        else
            redirect('/', 'refresh');
    }
}
?>