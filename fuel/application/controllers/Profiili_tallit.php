<?php
class Profiili_tallit extends Loggedin_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //Kaikki funktiot täällä vaativat kirjautuneen käyttäjän, joten:
        if(!($this->ion_auth->logged_in()))
        {
            redirect('/');
        }
		
		$this->load->model('tallit_model');

    }
    
    
}
?>






