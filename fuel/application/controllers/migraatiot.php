<?php

class Migraatiot extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
        

        
        bis_general
        bis_tulosrivit
        chat
        kayttajaoikeudet_kayttajaoikeudet
        kayttajaoikeudet_muutokset
        kayttajaoikeudet_oikeudet
        kayttajaoikeudet_ryhmassa
        kayttajaoikeudet_ryhmat
        kilpailukalenteri_testitulokset
        kilpailukalenteri_vanhat
        kisat_kisahakemukset
        kisat_kisakalenteri
        kisat_tulokset
        lista_jaokset
        lista_lajit
        lista_luokat
        lista_maakunnat
        lista_maat
        lista_ominaisuudet
        lista_painotus
        lista_rodut
        lista_roturyhmat
        lista_tallikategoriat
        lista_vaikutukset
        lista_varit
        nj_kisakalenteri
        ranking_07
        ranking_08
        ranking_09
        ranking_12
        ranking_13
        rotulyhenteet
        seurarekisteri
        seurarekisteri_jasenet
        seurarekisteri_jasentallit
        seurarekisteri_jonossa
        seurarekisteri_kannattajajasenet
        seurarekisteri_lopettaneet
        seurarekisteri_omistajamuutokset
        seurarekisteri_omistajat
        
        tapahtumat
        tapahtumat_osallistujat

        

        
        */
    
    function index(){
        $situation = array();
        $this->load->model('migraatio_model');
        
        $situation["tunnukset"] = $this->migraatio_model->count_by_keys("tunnukset", "vrlv3_tunnukset", "tunnus", "tunnus");
        $situation["tunnukset_pikaviestit"] = $this->migraatio_model->count_all_rows("tunnukset_pikaviestit", "vrlv3_tunnukset_pikaviestit");
        $situation["tunnukset_yhteystiedot"] = $this->migraatio_model->count_all_rows("tunnukset_yhteystiedot", "vrlv3_tunnukset_yhteystiedot");
        $situation["tunnukset_nimimerki"] = $this->migraatio_model->count_all_rows("tunnukset_nimimerkit", "vrlv3_tunnukset_nimimerkit");
        $situation["tunnukset_kirjautumiset"] = $this->migraatio_model->count_all_rows("tunnukset_kirjautunut", "vrlv3_tunnukset_kirjautumiset");
        $situation["tunnukset_kirjautumiset_epa"] = $this->migraatio_model->count_all_rows("tunnukset_epa", "vrlv3_tunnukset_kirjautumiset");
        
        /*TUNNUKSET TODO
        tunnukset_etuuspisteet
        tunnukset_onlie


        TUNNUKSET IGNORE
        tunnukset_jaahyt
        tunnukset_kaverit
        tunnukset_porkkanat
        tunnukset_salasanat
        tunnukset_suositukset
        */
        
        $situation["tiedotukset"] = $this->migraatio_model->count_all_rows("tiedotukset", "vrlv3_tiedotukset");
        $situation["tiedotukset_kategoriat"] = $this->migraatio_model->count_all_rows("tiedotukset_kategoriat", "vrlv3_tiedotukset_kategoriat");
        
        $situation["tallirekisteri"] = $this->migraatio_model->count_by_keys("tallirekisteri", "vrlv3_tallirekisteri", "tnro", "tnro");
        $situation["tallirekisteri_kategoriat"] = $this->migraatio_model->count_all_rows("tallirekisteri_kategoriat", "vrlv3_tallirekisteri_kategoriat");
        $situation["tallirekisteri_omistajat"] = $this->migraatio_model->count_all_rows("tallirekisteri_omistajat", "vrlv3_tallirekisteri_omistajat");

                
        /*SKIP
        tallirekisteri_arvostelu
        tallirekisteri_arvostelujono
        tallirekisteri_jonossa
        tallirekisteri_mielipiteet
        tallirekisteri_vanhat
        tallirekisteri_yesno
        tallirekisteri_yhteistyo
        
        DONE BUT NO CHECK
        tallirekisteri_lopettaneet
        
        TODO
        tallirekisteri_omistajamuutokset
        tallirekisteri_paivitetty

    */
        
        $situation["kasvattajanimet"] = $this->migraatio_model->count_all_rows("kasvattajanimet", "vrlv3_kasvattajanimet");
        $situation["kasvattajanimet_rodut"] = $this->migraatio_model->count_all_rows("kasvattajanimet_rodut", "vrlv3_kasvattajanimet_rodut");
        $situation["kasvattajanimet_omistajat"] = $this->migraatio_model->count_all_rows("kasvattajanimet", "vrlv3_kasvattajanimet");
        
    /*
     *
        DONE
        kasvattajanimet
        kasvattajanimet_rodut

        
        SKIP
        kasvattajanimet_jono
        kasvattajanimet_rotujono
     **/
    
            $situation["hevosrekisteri"] = $this->migraatio_model->count_all_rows("hevosrekisteri_perustiedot", "vrlv3_hevosrekisteri");    
            $situation["hevosrekisteri_omistajat"] = $this->migraatio_model->count_all_rows("hevosrekisteri_omistajat", "vrlv3_hevosrekisteri_omistajat");
            $situation["hevosrekisteri_sukutaulut"] = $this->migraatio_model->count_all_rows("hevosrekisteri_sukutaulut", "vrlv3_hevosrekisteri_sukutaulut");    


    /*
     *
     *DONE BUT NO CHECK
     *        hevosrekisteri_kasvattaja
     *        hevosrekisteri_kuolleet
     *        hevosrekisteri_lisatiedot
     
     DONE        
        hevosrekisteri_ikaantyminen
        hevosrekisteri_jonossa
        hevosrekisteri_markkinat
        hevosrekisteri_ominaisuudet
        hevosrekisteri_omistajamuutokset
        hevosrekisteri_orikatalogi
        hevosrekisteri_rekisterointistat_2011-2023
        hevosrekisteri_statistiikka
        hevosrekisteri_statistiikka_new
        hevosrekisteri_vanhat
     *
     **/
    
        foreach ($situation as $key=>$situ){
            echo $key . ": " . $situ['new'] . "/" . $situ['old'] . "<br>";
            
        }


        //jonoa ei siirretä
        
    }

    function tunnukset()
    {
        $this->load->model('migraatio_model');
        $vars = array();
        $vars['msg'] = $this->migraatio_model->migrate_tunnukset();
                echo "Valmis";
        $vars['msg'] .= $this->migraatio_model->migrate_pikaviestit();
            echo "Valmis2";

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
        
        $this->migraatio_model->migrate_lisatiedot();
        
        $this->migraatio_model->migrate_hevosenomistajat();
        echo "omistajat done<br>";
        */
        $this->migraatio_model->migrate_hevosuvut();
        echo "suvut done";

        echo "lisatiedot done<br>";
    }
    
    function kasvattajanimet (){
    $this->load->model('migraatio_model');
    $this->migraatio_model->migrate_kasvattajanimet();
    echo "kasvit done";
    }
    
    

    
}
?>