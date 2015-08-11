<?php
class Loggedin_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //Kaikkien tämän lapsi kontrollerien funktiot vaativat kirjautuneen käyttäjän:
        if(!$this->ion_auth->logged_in())
        {
            redirect('/');
        }
    }
}
?>