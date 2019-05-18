<?php
class Migraatiot extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function tunnukset()
    {
        $this->load->model('migraatio_model');
        $vars = array();
        $vars['msg'] = $this->migraatio_model->migrate_tunnukset();
                echo "Valmis";

        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    
    function tiedotukset()
    {
        $this->load->model('migraatio_model');
        $vars = array();
        $vars['msg'] = $this->migraatio_model->migrate_tiedotukset();
                echo "Valmis";

        $this->fuel->pages->render('misc/showmessage', $vars);
    }
    function tallit (){
         $this->load->model('migraatio_model');
    $this->migraatio_model->migrate_tallit();
    echo "tallit done";
    $this->migraatio_model->migrate_tallikategoriat();
    echo "tallit kat done";
    $this->migraatio_model->migrate_tallilopetukset();
    echo "tallit lop done";
    $this->migraatio_model->migrate_talliomistajat();
    echo "tallit om done";
    }
    
    function hevoset(){
        $this->load->model('migraatio_model');
        /*
        $montako=$this->migraatio_model->count_kaakit();
        
        if ($montako > 100000){
            $limit = ceil($montako/10);
            $offset = $limit + $limit + $limit;
            while($offset <= $montako){
                $this->migraatio_model->migrate_hevoset($offset, $limit);
                $offset = $offset + $limit;
                echo "hop";
                
                
            }
        }
        else {
            $this->migraatio_model->migrate_hevoset();

        }
        
        
        
        
        echo "perustiedot done<br>";
        $this->migraatio_model->migrate_kuolleet();
        echo "kuolemat done<br>";
        
        $this->migraatio_model->migrate_hevosenomistajat();
        echo "omistajat done<br>";
        */
         //$this->migraatio_model->migrate_hevosuvut();
         //$this->migraatio_model->migrate_vari_painotus_maa();
        //$this->migraatio_model->migrate_kasvattajatiedot
        echo "pluh";
        $this->migraatio_model->migrate_kasvattajanimet();

        echo "lisatiedot done<br>";
    }
    
    

    
}
?>