<?php 
/****************************************************************************************
// EXAMPLE:
$nav['about'] = 'About';
$nav['showcase'] = array('label' => 'Showcase', 'active' => 'showcase$|showcase/:any');
$nav['blog'] = array('label' => 'Blog', 'active' => 'blog$|blog/:any');
$nav['contact'] = 'Contact';

// about sub menu
$nav['about/services'] = array('label' => 'Services', 'parent_id' => 'about');
$nav['about/team'] = array('label' => 'Team', 'parent_id' => 'about');
$nav['about/what-they-say'] = array('label' => 'What They Say', 'parent_id' => 'about');
*****************************************************************************************/
$nav = array();
//Ylämenu
$nav['liitto'] = array('label' => 'Liitto', 'active' => 'liitto$|liitto/:any');
$nav['jasenyys'] = array('label' => 'Jäsenyys', 'active' => 'jasenyys$|jasenyys/:any');
$nav['tallit'] = array('label' => 'Virtuaalitallit', 'active' => 'tallit$|tallit/:any'); //Koodattavaa: Tallihaku
$nav['virtuaalihevoset'] = array('label' => 'Virtuaalihevoset', 'active' => 'virtuaalihevoset$|virtuaalihevoset/:any'); //Koodattavaa: Hevoshaku
$nav['kasvatus'] = array('label' => 'Kasvatus', 'active' => 'kasvatus$|kasvatus/:any'); //Koodattavaa: Hevoshaku
$nav['kilpailutoiminta'] = 'Kilpailutoiminta';
$nav['nayttelytoiminta'] = 'Näyttelytoiminta';
//Piilotetut
$nav['yllapito'] = array('label'=>'Ylläpito', 'hidden'=> TRUE, 'active' => 'yllapito$|yllapito/:any');
$nav['profiili'] = array('label'=>'Profiili', 'hidden'=> TRUE, 'active' => 'profiili');

//Liitto alamenu
$nav['liitto/tiedotukset'] = array('label' => 'Tiedotukset', 'parent_id' => 'liitto', 'active' => 'liitto/tiedotukset');
$nav['liitto/tiedotus'] = array('hidden'=>TRUE, 'label' => 'Tiedotus', 'parent_id' => 'liitto/tiedotukset', 'active' => 'liitto/tiedotus/:any'); 

$nav['liitto/yllapito'] = array('label' => 'Ylläpito ja yhteydenotto', 'parent_id' => 'liitto', 'active' => 'liitto/yllapito'); //Koodattavaa: työntekijöiden listaus
$nav['liitto/wiki'] = array('label' => 'Virtuaaliwiki', 'parent_id' => 'liitto', 'active' => 'liitto/wiki');
$nav['liitto/somessa'] = array('label' => 'VRL sosiaalisessa mediassa', 'parent_id' => 'liitto', 'active' => 'liitto/somessa');
$nav['liitto/mainosta'] = array('label' => 'Mainosta sivuillamme!', 'parent_id' => 'liitto', 'active' => 'liitto/mainosta');
$nav['liitto/copyright'] = array('label' => 'Tekijänoikeudet', 'parent_id' => 'liitto', 'active' => 'liitto/copyright');
//Kehitysblogi, porkkanat, Virma, tuki, kehitysblogi
 
// jäsenyys alamenu
$nav['jasenyys/liity'] = array('label' => 'Liity jäseneksi', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/liity');
$nav['jasenyys/rekisteriseloste'] = array('label' => 'Rekisteriseloste', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/rekisteriseloste');
// virtuaalitallit alamenu
$nav['tallit/haku'] = array('label' => 'Tallihaku', 'parent_id' => 'tallit', 'active' => 'tallit');
$nav['tallit/rekisterointi'] = array('label' => 'Tallin rekisteröinti', 'parent_id' => 'tallit', 'active' => 'tallit/rekisterointi');
$nav['tallit/omat'] = array('label' => 'Omat tallit', 'parent_id' => 'tallit', 'active' => 'tallit/omat');
$nav['tallit/uusimmat'] = array('label' => 'Uusimmat tallit', 'parent_id' => 'tallit', 'active' => 'tallit/uusimmat'); //Koodattavaa: Uusimpien rekattujen tallien lista
$nav['tallit/paivitetyt'] = array('label' => 'Viimeksi päivitetyt tallit', 'parent_id' => 'tallit', 'active' => 'tallit/paivitetyt'); //Koodattavaa: Viimeksi päivitetyt tallit
//virtuaalihevoset alamenu
$nav['virtuaalihevoset/haku'] = array('label' => 'Hevosrekisteri', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/haku'); //Koodattavaa: Rekisteröintilomake
$nav['virtuaalihevoset/rekisterointi'] = array('label' => 'Hevosten rekisteröinti', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/rekisterointi'); //Koodattavaa: Rekisteröintilomake
$nav['virtuaalihevoset/omat'] = array('label' => 'Omat hevoset', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/omat'); //Koodattavaa: Syntymämaat
$nav['virtuaalihevoset/statistiikka'] = array('label' => 'Statistiikka', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/statistiikka'); //Koodattavaa: Statistiikat
$nav['virtuaalihevoset/rodut'] = array('label' => 'Rotulista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/rotulista'); //Koodattavaa: Rotulistat
$nav['virtuaalihevoset/varit'] = array('label' => 'Värilista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/varilista'); //Koodattavaa: Värit
$nav['virtuaalihevoset/syntymamaat'] = array('label' => 'Syntymämaalista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/syntymamaat'); //Koodattavaa: Syntymämaat
//jalostus ja kasvatus alamenu
$nav['kasvatus/kasvattajanimet'] = array('label' => 'Kasvattajanimirekisteri', 'parent_id' => 'kasvatus', 'active' => 'kasvatus/kasvattajanimet'); 
$nav['kasvatus/kasvattajanimet/rekisteroi'] = array('label' => 'Rekisteröi kasvattajanimi', 'parent_id' => 'kasvatus/kasvattajanimet', 'active' => 'kasvatus/kasvattajanimet/omat');
$nav['kasvatus/kasvattajanimet/omat'] = array('label' => 'Omat kasvattajanimet', 'parent_id' => 'kasvatus/kasvattajanimet', 'active' => 'kasvatus/kasvattajanimet/omat');
$nav['kasvatus/kasvatit'] = array('label' => 'Omat kasvatit', 'parent_id' => 'kasvatus', 'active' => 'kasvatus/kasvatit');
$nav['kasvatus/unelmasuku'] = array('label' => 'Unelmasuku', 'parent_id' => 'kasvatus', 'active' => 'kasvatus/unelmasuku');
$nav['kasvatus/varijalostus'] = array('label' => 'Värien periytyminen', 'parent_id' => 'kasvatus', 'active' => 'kasvatus/varijalostus'); 
$nav['kasvatus/varilaskuri'] = array('label' => 'Periytymislaskuri', 'parent_id' => 'kasvatus/varijalostus', 'active' => 'kasvatus/varilaskuri'); 

//kantakirjat, laatikset, rekkaa kasvinimi
//kilpailutoiminta alamenu
$nav['kilpailutoiminta/kilpailukalenteri'] = array('label' => 'Kilpailukalenteri', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/kilpailukalenteri');
$nav['kilpailutoiminta/kilpailukalenteri/porrastetut'] = array('label' => 'Porrastetut', 'hidden'=>TRUE, 'parent_id' => 'kilpailutoiminta/kilpailukalenteri', 'active' => 'kilpailutoiminta/kilpailukalenteri/porrastetut');

$nav['kilpailutoiminta/kilpailukalenteri/k'] = array('label' => 'Kilpailukutsu', 'hidden'=>TRUE, 'parent_id' => 'kilpailutoiminta/kilpailukalenteri/porrastetut', 'active' => 'kilpailutoiminta/k|kilpailutoiminta/k/:any');

$nav['kilpailutoiminta/tulosarkisto'] = array('label' => 'Tulosarkisto', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/tulosarkisto');

$nav['kilpailutoiminta/kilpailujaokset'] = array('label' => 'Jaokset', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/jaokset');
$nav['kilpailutoiminta/kilpailusaannot'] = array('label' => 'Yleiset kilpailusäännöt', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/kilpailusaannot');
$nav['kilpailutoiminta/etuuspisteet'] = array('label' => 'Etuuspisteet', 'parent_id' => 'kilpailutoiminta/kilpailusaannot', 'active' => 'kilpailutoiminta/etuuspisteet');
$nav['kilpailutoiminta/porrastetut'] = array('label' => 'Porrastetut kilpailut', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/porrastetut');
$nav['kilpailutoiminta/porrastetut/luokat'] = array('label' => 'Luokat', 'parent_id' => 'kilpailutoiminta/porrastetut', 'active' => 'kilpailutoiminta/porrastetut/luokat');
$nav['kilpailutoiminta/porrastetut/kilpailulistat'] = array('label' => 'Kilpailulistat', 'parent_id' => 'kilpailutoiminta/porrastetut', 'active' => 'kilpailutoiminta/porrastetut/kilpailulistat');


$nav['kilpailutoiminta/omat'] = array('label' => 'Omat kilpailut', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/omat');
$nav['kilpailutoiminta/omat/porrastetut'] = array('hidden'=>true, 'label' => 'Avoimet (Porrastetut)', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/porrastetut');
$nav['kilpailutoiminta/omat/avoimet'] = array('hidden'=>true, 'label' => 'Avoimet (Perinteiset)', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/avoimet');
$nav['kilpailutoiminta/omat/jonossa'] = array('hidden'=>true, 'label' => 'Kutsujonossa', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/jonossa$|kilpailutoiminta/omat/jonossa/:any');
$nav['kilpailutoiminta/omat/tulosjonossa'] = array('hidden'=>true, 'label' => 'Tulosjonossa', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/tulosjonossa$|kilpailutoiminta/omat/tulosjonossa/:any');
$nav['kilpailutoiminta/omat/menneet'] = array('hidden'=>true, 'label' => 'Tulokselliset', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/menneet');

$nav['kilpailutoiminta/omat/etuuspisteet'] = array('label' => 'Omat etuuspisteet', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/etuuspisteet');

$nav['kilpailutoiminta/ilmoita_kilpailut/porrastetut'] = array('label' => 'Järjestä porrastetut kilpailut', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_kilpailut/porrastetut$|kilpailutoiminta/ilmoita_kilpailut/porrastetut/:any');
$nav['kilpailutoiminta/ilmoita_kilpailut/perinteiset'] = array('label' => 'Järjestä perinteiset kilpailut', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_kilpailut/perinteiset');
$nav['kilpailutoiminta/ilmoita_tulokset'] = array('label' => 'Ilmoita tulokset', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_tulokset');


//porrastettujen jutut, anomisen jutut jne jne
//nayttelytoiminta alamenu
$nav['kilpailutoiminta/saannot'] = array('label' => 'Näyttelysäännöt', 'parent_id' => 'nayttelytoiminta', 'active' => 'nayttelytoiminta/saannot');
$nav['kilpailutoiminta/nayttelykalenteri'] = array('label' => 'Näyttelykalenteri', 'parent_id' => 'nayttelytoiminta', 'active' => 'nayttelytoiminta/nayttelykalenteri');
$nav['kilpailutoiminta/bislista'] = array('label' => 'BIS-lista', 'parent_id' => 'nayttelytoiminta', 'active' => 'nayttelytoiminta/bislista');
$nav['kilpailutoiminta/nayttelyiden-kermaa'] = array('label' => 'Näyttelyiden kermaa', 'parent_id' => 'nayttelytoiminta', 'active' => 'nayttelytoiminta/nayttelyiden-kermaa');
$nav['kilpailutoiminta/jarjestaminen'] = array('label' => 'Näyttelyiden järjestäminen', 'parent_id' => 'nayttelytoiminta', 'active' => 'nayttelytoiminta/jarjestaminen');

// ylläpito alamenu
$nav['yllapito/tiedotukset'] = array('label' => 'Tiedotukset', 'parent_id' => 'yllapito', 'active' => 'yllapito/tiedotukset');
$nav['yllapito/tunnukset'] = array('label' => 'Tunnukset', 'parent_id' => 'yllapito', 'active' => 'yllapito/tunnukset');
$nav['yllapito/hevosrekisteri'] = array('label' => 'Hevosrekisteri', 'parent_id' => 'yllapito', 'active' => 'yllapito/hevosrekisteri');
$nav['yllapito/jaokset'] = array('label' => 'Jaokset', 'parent_id' => 'yllapito', 'active' => 'yllapito/jaokset');
$nav['yllapito/kalenterit'] = array('label' => 'Kisakalenterit', 'parent_id' => 'yllapito', 'active' => 'yllapito/kalenterit');



    
// ylläpito/tunnukset alamenu
$nav['yllapito/tunnukset/hyvaksy'] = array('label' => 'Hyväksy VRL-tunnuksia', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/hyvaksy');
$nav['yllapito/tunnukset/muokkaa'] = array('label' => 'Muokkaa tunnuksen tietoja', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/muokkaa');
$nav['yllapito/tunnukset/oikeudet'] = array('label' => 'Käyttöoikeudet', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/oikeudet');
$nav['yllapito/tunnukset/kirjautumiset/ip'] = array('label' => 'Kirjautumiset (ip)', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/kirjautumiset/ip');
$nav['yllapito/tunnukset/kirjautumiset/tunnus'] = array('label' => 'Kirjautumiset (tunnus)', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/kirjautumiset/tunnus');

// profiili alamenu
$nav['profiili/tunnus'] = array('label' => 'Omat tiedot', 'parent_id' => 'profiili', 'active' => 'profiili/tunnus');
$nav['profiili/tiedot'] = array('label' => 'Muokkaa tietoja', 'parent_id' => 'profiili', 'active' => 'profiili/tiedot');
$nav['profiili/vaihda_salasana'] = array('label' => 'Vaihda salasana', 'parent_id' => 'profiili', 'active' => 'profiili/vaihda_salasana');
$nav['profiili/pikaviestit'] = array('label' => 'Omat pikaviestit', 'parent_id' => 'profiili', 'active' => 'profiili/pikaviestit');

$nav['yllapito/hevosrekisteri/varit'] = array('label' => 'Hallitse värejä', 'parent_id' => 'yllapito/hevosrekisteri', 'active' => 'yllapito/hevosrekisteri/varit');
$nav['yllapito/hevosrekisteri/rodut'] = array('label' => 'Hallitse rotuja', 'parent_id' => 'yllapito/hevosrekisteri', 'active' => 'yllapito/hevosrekisteri/rodut');

$nav['yllapito/jaokset/lajit'] = array('label' => 'Hallitse lajeja', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/lajit');
$nav['yllapito/jaokset/ominaisuudet'] = array('label' => 'Hallitse ominaisuuksia', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/ominaisuudet');

$nav['yllapito/jaokset/jaokset'] = array('label' => 'Jaokset', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/jaokset');
$nav['yllapito/jaokset/lisaa_jaos'] = array('label' => 'Lisää jaos', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/lisaa_jaos');
$nav['yllapito/jaokset/tapahtumat'] = array('label' => 'Hallitse tapahtumia', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/tapahtumat');

$nav['yllapito/kalenterit/kisahyvaksynta'] = array('label' => 'Kisahyväksyntä', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/kisahyvaksynta|yllapito/kalenterit/kisahyvaksynta/:any');
$nav['yllapito/kalenterit/tuloshyvaksynta'] = array('label' => 'Tuloshyväksyntä', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/tuloshyvaksynta|yllapito/kalenterit/tuloshyvaksynta/:any');
$nav['yllapito/kalenterit/hyvaksytytkisat'] = array('label' => 'Selaa hyväksyttyjä kutsuja', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/hyvaksytytkisat');
$nav['yllapito/kalenterit/hyvaksytyttulokset'] = array('label' => 'Selaa hyväksyttyjä tuloksia', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/hyvaksytyttulokset');
$nav['yllapito/kalenterit/hyvaksytytkisat/edit'] = array('label' => 'Muokkaa kutsua', 'hidden'=>true, 'parent_id' => 'yllapito/kalenterit/hyvaksytytkisat', 'active' => 'yllapito/kalenterit/hyvaksytytkisat/edit/:any');








