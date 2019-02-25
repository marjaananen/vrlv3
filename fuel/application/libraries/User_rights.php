<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

//Käyttöohje:

//Luokka tarkastaa, onko käyttäjä kirjautunut, ja onko hän admin vai kuuluuko johonkin vaadituista ryhmistä

//Jokainen käyttäjäoikeuksia vaativa luokka (controller) tarvitsee tällaisen privaattimuuttujan,
//jossa esimerkki1, esimerkki2 ja esimerkki3 ovat käyttäjäryhmiä, joille luokan toiminnot ovat sallittuja.
//ryhmiä saa olla yksi tai useampi. Jos listan jättää tyhjäksi array(), vain admin pääsee toimintoihin.

//    private $allowed_user_groups = array('esimerkki', 'esimerkki2', 'esimerkki3');

//Jokaisen luokan rakentajaan (constructor) tulee lisäksi lisätä nämä rivit:
        
//        $this->load->library('user_rights', array('groups' => $this->allowed_user_groups));
//        if (!$this->user_rights->is_allowed()){       
//            redirect($this->user_rights->redirect());
//        }


class User_rights
{
    private $CI = 0;
    private $req_user_groups;
    private $redirect_address = "/";
    
    public function __construct($param)
    {
        $this->CI =& get_instance();
        $this->req_user_groups = $param['groups'];
    }

    public function is_allowed()
    {
        //Jos ei ole kirjautunut sisään, ei voi kuulua mihinkään, ja heitetään vain etusivulle
        if (!$this->CI->ion_auth->logged_in()){
            $this->redirect_address ="/";
            return false;
        }
        //Admin pääsee kaikkialle.
        else if ($this->CI->ion_auth->is_admin()){
            return true;            
        }
        
        else {
            $this->CI->load->model('ion_auth_model');
            
            $user_is_in_one_of_the_groups = false;
            
            //Käydään läpi jokainen ryhmä
            foreach ($this->req_user_groups as $group){
                
                //Onko ryhmä olemassa?
                if($this->CI->ion_auth_model->group_exists($group)){
                    //Onko käyttäjä kys. ryhmässä
                    if($this->CI->ion_auth->in_group($group)){                        
                        $user_is_in_one_of_the_groups = true;
                        break;
                    }
                    
                    else {
                        //todo: parent grouppitoiminnallisuus
                    }
       
                }
                //Grouppia ei ollut, joten kukaan käyttäjä ei myöskään voi siinä ryhmässä olla.
                //Grouppi lisätään kuitenkin kantaan, ja tulevaisuudessa siihen voidaan lisätä ihmisiä.
                else {
                    $this->create_group($this->req_user_group, "Automatically added group");       
                }                           
            }
            
            return $user_is_in_one_of_the_groups;
              
        }
        
    }
    
    public function redirect(){
        return $this->redirect_address;
    }
}



