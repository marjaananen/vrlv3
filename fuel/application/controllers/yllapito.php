<?php
class Yllapito extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    //salainen adminin funktio, jolla voi lisätä käyttäjän johonkin käyttöoikeusryhmään
    //parametrit query stringinä
    function add_user_to_group()
    {
        if ($this->ion_auth->logged_in())
        {
            if(!$this->ion_auth->is_admin())
                $vars['validate_msg'] = "Et ole admin";
            else
            {
                $userid = $this->input->get('userid', TRUE);
                $groupid = $this->input->get('groupid', TRUE);
                
                if($userid != false && $groupid != false)
                {
                    if($this->ion_auth->add_to_group($groupid, $userid) == true)
                        $vars['validate_msg'] = "Onnistui";
                    else
                        $vars['validate_msg'] = "Epäonnistui";
                }
                else
                    $vars['validate_msg'] = "Kämmäsit parametrit";
            }
        }
        else
            $vars['validate_msg'] = "Et ole kirjautunut sisään";

        $this->fuel->pages->render('jasenyys/vahvista', $vars); //pelkkä viestinnäyttösivu
    }
}
?>