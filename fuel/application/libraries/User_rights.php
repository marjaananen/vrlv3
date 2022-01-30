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
        $this->CI->load->model('Oikeudet_model');
    }

    public function is_allowed($list = array())
    {
        //Jos ei ole kirjautunut sisään, ei voi kuulua mihinkään, ja heitetään vain etusivulle
        if (!$this->CI->ion_auth->logged_in()){
            return false;
        }
        //Admin pääsee kaikkialle.
        else if ($this->CI->ion_auth->is_admin()){
            return true;            
        }
        
        else {
            $this->CI->load->model('Ion_auth_model');
            
            $user_is_in_one_of_the_groups = false;
            $allowed_user_groups = array();
            if (sizeof($list) > 0){
                $allowed_user_groups = $list;
            }
            else {
                $allowed_user_groups = $this->req_user_groups;
            }
            
            //Käydään läpi jokainen ryhmä
            foreach ($allowed_user_groups as $group){
                if($this->CI->Oikeudet_model->does_user_group_exist_by_name($group)){
                    //Onko ryhmä olemassa?
                    if($this->CI->ion_auth->in_group($group)){                        
                        $user_is_in_one_of_the_groups = true;
                        break;
                    }
                }
                
                else {
                    $this->CI->ion_auth->create_group($group, "Automatically added group");   
                }                           
            }
            
            return $user_is_in_one_of_the_groups;
              
        }
        
    }
    
   
    
    
    public function redirect(){
        return $this->redirect_address;
    }
}



