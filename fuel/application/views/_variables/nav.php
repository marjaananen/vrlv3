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
$nav['alayhdistykset'] = 'Alayhdistykset';
//Piilotetut
$nav['yllapito'] = array('label'=>'Ylläpito', 'hidden'=> TRUE, 'active' => 'yllapito$|yllapito/:any');
$nav['profiili'] = array('label'=>'Profiili', 'hidden'=> TRUE, 'active' => 'profiili');

//Liitto alamenu
$nav['liitto/tiedotukset'] = array('label' => 'Tiedotukset', 'parent_id' => 'liitto', 'active' => 'liitto/tiedotukset');
$nav['liitto/tiedotus'] = array('hidden'=>TRUE, 'label' => 'Tiedotus', 'parent_id' => 'liitto/tiedotukset', 'active' => 'liitto/tiedotus/:any'); 
$nav['liitto/yllapito'] = array('label' => 'Ylläpito ja yhteydenotto', 'parent_id' => 'liitto', 'active' => 'liitto/yllapito'); //Koodattavaa: työntekijöiden listaus
$nav['liitto/rajapinta'] = array('label' => 'Rajapinta', 'parent_id' => 'liitto', 'active' => 'liitto/rajapinta');

$nav['liitto/wiki'] = array('label' => 'Virtuaaliwiki', 'parent_id' => 'liitto', 'active' => 'liitto/wiki');
$nav['liitto/somessa'] = array('label' => 'VRL sosiaalisessa mediassa', 'parent_id' => 'liitto', 'active' => 'liitto/somessa');
$nav['liitto/mainosta'] = array('label' => 'Mainosta sivuillamme!', 'parent_id' => 'liitto', 'active' => 'liitto/mainosta');
$nav['liitto/copyright'] = array('label' => 'Tekijänoikeudet', 'parent_id' => 'liitto', 'active' => 'liitto/copyright');
//Kehitysblogi, porkkanat, Virma, tuki, kehitysblogi
 
// jäsenyys alamenu
$nav['jasenyys/liity'] = array('label' => 'Liity jäseneksi', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/liity');
$nav['jasenyys/rekisteriseloste'] = array('label' => 'Rekisteriseloste', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/rekisteriseloste');
$nav['tunnus'] = array('hidden'=>true, 'label' => 'Tunnus', 'parent_id' => 'jasenyys', 'active' => 'tunnus$|tunnus/:any');
$nav['jasenyys/tunnus'] = array('hidden'=>true, 'label' => 'Tunnus', 'parent_id' => 'jasenyys', 'active' => 'tunnus$|tunnus/:any');




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
$nav['virtuaalihevoset/statistiikka'] = array('label' => 'Statistiikka', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/statistiikka$|virtuaalihevoset/statistiikka/:any'); //Koodattavaa: Statistiikat
$nav['virtuaalihevoset/rodut'] = array('label' => 'Rotulista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/rodut'); //Koodattavaa: Rotulistat
$nav['virtuaalihevoset/rotu'] = array('hidden'=>true, 'label' => 'Rotu', 'parent_id' => 'virtuaalihevoset/rodut', 'active' => 'virtuaalihevoset/rotu$|virtuaalihevoset/rotu/:any'); //Koodattavaa: Rotulistat

$nav['virtuaalihevoset/varit'] = array('label' => 'Värilista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/varit'); //Koodattavaa: Värit
$nav['virtuaalihevoset/vari'] = array('hidden'=>true, 'label' => 'Väri', 'parent_id' => 'virtuaalihevoset/varit', 'active' => 'virtuaalihevoset/vari$|virtuaalihevoset/vari/:any'); //Koodattavaa: Rotulistat


$nav['virtuaalihevoset/syntymamaat'] = array('label' => 'Syntymämaalista', 'parent_id' => 'virtuaalihevoset', 'active' => 'virtuaalihevoset/syntymamaat'); //Koodattavaa: Syntymämaat
$nav['virtuaalihevoset/maa'] = array('hidden'=>true, 'label' => 'Maa', 'parent_id' => 'virtuaalihevoset/syntymamaat', 'active' => 'virtuaalihevoset/maa$|virtuaalihevoset/maa/:any'); //Koodattavaa: Rotulistat

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
$nav['kilpailutoiminta/nayttelykalenteri'] = array('label' => 'Näyttelykalenteri', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/nayttelykalenteri');
$nav['kilpailutoiminta/kilpailukalenteri'] = array('label' => 'Kilpailukalenteri', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/kilpailukalenteri');
$nav['kilpailutoiminta/kilpailukalenteri/porrastetut'] = array('label' => 'Porrastetut', 'hidden'=>TRUE, 'parent_id' => 'kilpailutoiminta/kilpailukalenteri', 'active' => 'kilpailutoiminta/kilpailukalenteri/porrastetut');
$nav['kilpailutoiminta/kilpailukalenteri/k'] = array('label' => 'Kilpailukutsu', 'hidden'=>TRUE, 'parent_id' => 'kilpailutoiminta/kilpailukalenteri/porrastetut', 'active' => 'kilpailutoiminta/k|kilpailutoiminta/k/:any');


$nav['kilpailutoiminta/tulosarkisto'] = array('label' => 'Tulosarkisto', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/tulosarkisto');
$nav['kilpailutoiminta/tulosarkisto/porrastetut'] = array('hidden'=>true, 'label' => 'Porrastetut', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/porrastetut');
$nav['kilpailutoiminta/tulosarkisto/nayttelyt'] = array('hidden'=>true, 'label' => 'Näyttelyt', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/nayttelyt');
$nav['kilpailutoiminta/tulosarkisto/perinteiset'] = array('hidden'=>true, 'label' => 'Perinteiset', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/perinteiset');
$nav['kilpailutoiminta/tulosarkisto/tarinalliset'] = array('hidden'=>true, 'label' => 'Tarinalliset', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/tarinalliset');
$nav['kilpailutoiminta/tulosarkisto/porrastetut'] = array('hidden'=>true, 'label' => 'Porrastetut', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/porrastetut');
$nav['kilpailutoiminta/tulosarkisto/tulos'] = array('hidden'=>true, 'label' => 'Tulos', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/tulos|kilpailutoiminta/tulosarkisto/tulos/:any');
$nav['kilpailutoiminta/tulosarkisto/bis'] = array('hidden'=>true, 'label' => 'Tulos', 'parent_id' => 'kilpailutoiminta/tulosarkisto', 'active' => 'kilpailutoiminta/tulosarkisto/bis$|kilpailutoiminta/tulosarkisto/bis/:any');



$nav['kilpailutoiminta/kilpailujaokset'] = array('label' => 'Jaokset', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/jaokset');

$nav['kilpailutoiminta/kilpailusaannot'] = array('label' => 'Yleiset kilpailusäännöt', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/kilpailusaannot');
$nav['kilpailutoiminta/porrastetut'] = array('label' => 'Porrastetut kilpailut', 'parent_id' => 'kilpailutoiminta/kilpailusaannot', 'active' => 'kilpailutoiminta/kilpailusaannot/porrastetut');
$nav['kilpailutoiminta/porrastetut/luokat'] = array('label' => 'Luokat', 'parent_id' => 'kilpailutoiminta/porrastetut', 'active' => 'kilpailutoiminta/kilpailusaannot/porrastetut/luokat');
$nav['kilpailutoiminta/porrastetut/kilpailulistat'] = array('label' => 'Kilpailulistat', 'parent_id' => 'kilpailutoiminta/porrastetut', 'active' => 'kilpailutoiminta/kilpailusaannot/porrastetut/kilpailulistat');


$nav['kilpailutoiminta/nayttelysaannot'] = array('label' => 'Näyttelyiden järjestäminen', 'parent_id' => 'kilpailutoiminta/kilpailusaannot', 'active' => 'kilpailutoiminta/nayttelysaannot');
$nav['kilpailutoiminta/kilpailujarjestaminen'] = array('label' => 'Kilpailujen järjestäminen', 'parent_id' => 'kilpailutoiminta/kilpailusaannot', 'active' => 'kilpailutoiminta/kilpailujarjestaminen');
$nav['kilpailutoiminta/etuuspisteet'] = array('label' => 'Etuuspisteet', 'parent_id' => 'kilpailutoiminta/kilpailusaannot', 'active' => 'kilpailutoiminta/etuuspisteet');



$nav['kilpailutoiminta/omat'] = array('label' => 'Omat kilpailut', 'parent_id' => 'kilpailutoiminta', 'active' => 'kilpailutoiminta/omat');
$nav['kilpailutoiminta/omat/kisat'] = array('label' => 'Kilpailut', 'hidden'=>'true', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/kisat');
$nav['kilpailutoiminta/omat/nayttelyt'] = array('label' => 'Näyttelyt', 'hidden'=>'true', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/nayttelyt');

$nav['kilpailutoiminta/omat/kisat/porrastetut'] = array('hidden'=>true, 'label' => 'Avoimet (Porrastetut)', 'parent_id' => 'kilpailutoiminta/omat/kisat', 'active' => 'kilpailutoiminta/omat/kisat/porrastetut');
$nav['kilpailutoiminta/omat/kisat/avoimet'] = array('hidden'=>true, 'label' => 'Avoimet (Perinteiset)', 'parent_id' => 'kilpailutoiminta/omat/kisat', 'active' => 'kilpailutoiminta/omat/kisat/avoimet');
$nav['kilpailutoiminta/omat/kisat/jonossa'] = array('hidden'=>true, 'label' => 'Kutsujonossa', 'parent_id' => 'kilpailutoiminta/omat/kisat', 'active' => 'kilpailutoiminta/omat/kisat/jonossa$|kilpailutoiminta/omat/kisat/jonossa/:any');
//$nav['kilpailutoiminta/omat/kisat/tulosjonossa'] = array('hidden'=>true, 'label' => 'Tulosjonossa', 'parent_id' => 'kilpailutoiminta/omat/kisat', 'active' => 'kilpailutoiminta/omat/kisat/tulosjonossa$|kilpailutoiminta/omat/kisat/tulosjonossa/:any$|kilpailutoiminta/omat/kisat/tulosjonossa/:any/:any|');
$nav['kilpailutoiminta/omat/kisat/menneet'] = array('hidden'=>true, 'label' => 'Tulokselliset', 'parent_id' => 'kilpailutoiminta/omat/kisat', 'active' => 'kilpailutoiminta/omat/kisat/menneet');

$nav['kilpailutoiminta/omat/nayttelyt/avoimet'] = array('hidden'=>true, 'label' => 'Avoimet (Perinteiset)', 'parent_id' => 'kilpailutoiminta/omat/nayttelyt', 'active' => 'kilpailutoiminta/omat/nayttelyt/avoimet');
$nav['kilpailutoiminta/omat/nayttelyt/jonossa'] = array('hidden'=>true, 'label' => 'Kutsujonossa', 'parent_id' => 'kilpailutoiminta/omat/nayttelyt', 'active' => 'kilpailutoiminta/omat/nayttelyt/jonossa$|kilpailutoiminta/omat/nayttelyt/jonossa/:any');
$nav['kilpailutoiminta/omat/nayttelyt/tulosjonossa'] = array('hidden'=>true, 'label' => 'Tulosjonossa', 'parent_id' => 'kilpailutoiminta/omat/nayttelyt', 'active' => 'kilpailutoiminta/omat/nayttelyt/tulosjonossa$|kilpailutoiminta/omat/tulosjonossa/:any');
$nav['kilpailutoiminta/omat/nayttelyt/menneet'] = array('hidden'=>true, 'label' => 'Tulokselliset', 'parent_id' => 'kilpailutoiminta/omat/nayttelyt', 'active' => 'kilpailutoiminta/omat/nayttelyt/menneet');




$nav['kilpailutoiminta/omat/etuuspisteet'] = array('label' => 'Omat etuuspisteet', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/omat/etuuspisteet');

$nav['kilpailutoiminta/ilmoita_kilpailut/porrastetut'] = array('label' => 'Järjestä porrastetut kilpailut', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_kilpailut/porrastetut$|kilpailutoiminta/ilmoita_kilpailut/porrastetut/:any');
$nav['kilpailutoiminta/ilmoita_kilpailut/perinteiset'] = array('label' => 'Järjestä perinteiset kilpailut', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_kilpailut/perinteiset');
$nav['kilpailutoiminta/ilmoita_kilpailut/nayttelyt'] = array('label' => 'Järjestä näyttelyt', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_kilpailut/nayttelyt');

$nav['kilpailutoiminta/ilmoita_tulokset/kisat'] = array('label' => 'Ilmoita kisatulokset', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_tulokset/kisat');
$nav['kilpailutoiminta/ilmoita_tulokset/nayttelyt'] = array('label' => 'Ilmoita näyttelytulokset', 'parent_id' => 'kilpailutoiminta/omat', 'active' => 'kilpailutoiminta/ilmoita_tulokset/nayttelyt');





// ylläpito alamenu
$nav['yllapito/tiedotukset'] = array('label' => 'Tiedotukset', 'parent_id' => 'yllapito', 'active' => 'yllapito/tiedotukset');
$nav['yllapito/tunnukset'] = array('label' => 'Tunnukset', 'parent_id' => 'yllapito', 'active' => 'yllapito/tunnukset');
$nav['yllapito/hevosrekisteri'] = array('label' => 'Hevosrekisteri', 'parent_id' => 'yllapito', 'active' => 'yllapito/hevosrekisteri');
$nav['yllapito/alayhdistykset'] = array('label' => 'Alayhdistykset', 'parent_id' => 'yllapito', 'active' => 'yllapito/alayhdistykset');
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

$nav['yllapito/hevosrekisteri/polveutumistarkastus'] = array('label' => 'Polveutumistarkastus', 'parent_id' => 'yllapito/hevosrekisteri', 'active' => 'yllapito/hevosrekisteri/polveutumistarkastus$|yllapito/hevosrekisteri/polveutumistarkastus/:any');
$nav['yllapito/hevosrekisteri/varit'] = array('label' => 'Hallitse värejä', 'parent_id' => 'yllapito/hevosrekisteri', 'active' => 'yllapito/hevosrekisteri/varit');
$nav['yllapito/hevosrekisteri/rodut'] = array('label' => 'Hallitse rotuja', 'parent_id' => 'yllapito/hevosrekisteri', 'active' => 'yllapito/hevosrekisteri/rodut');

$nav['yllapito/jaokset/lajit'] = array('label' => 'Hallitse lajeja', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/lajit');
$nav['yllapito/jaokset/ominaisuudet'] = array('label' => 'Hallitse ominaisuuksia', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/ominaisuudet');
$nav['yllapito/jaokset/jaokset'] = array('hidden'=>true, 'label' => 'Jaokset', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/jaokset');
$nav['yllapito/jaokset/lisaa_jaos'] = array('label' => 'Lisää jaos', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/lisaa_jaos');
$nav['yllapito/jaokset/tapahtumat'] = array('label' => 'Hallitse tapahtumia', 'parent_id' => 'yllapito/jaokset', 'active' => 'yllapito/jaokset/tapahtumat');

$nav['yllapito/kalenterit/kisahyvaksynta'] = array('hidden'=>true, 'label' => 'Kisahyväksyntä', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/kisahyvaksynta|yllapito/kalenterit/kisahyvaksynta/:any');
$nav['yllapito/kalenterit/tuloshyvaksynta'] = array('hidden'=>true, 'label' => 'Tuloshyväksyntä', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/tuloshyvaksynta|yllapito/kalenterit/tuloshyvaksynta/:any');
$nav['yllapito/kalenterit/hyvaksytytkisat'] = array('label' => 'Selaa hyväksyttyjä kutsuja', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/hyvaksytytkisat');
$nav['yllapito/kalenterit/hyvaksytyttulokset'] = array('label' => 'Selaa hyväksyttyjä tuloksia', 'parent_id' => 'yllapito/kalenterit', 'active' => 'yllapito/kalenterit/hyvaksytyttulokset');
$nav['yllapito/kalenterit/hyvaksytytkisat/edit'] = array('label' => 'Muokkaa kutsua', 'hidden'=>true, 'parent_id' => 'yllapito/kalenterit/hyvaksytytkisat', 'active' => 'yllapito/kalenterit/hyvaksytytkisat/edit/:any');

$nav['yllapito/alayhdistykset/yhdistykset'] = array('hidden'=> true, 'label' => 'Alayhdistykset', 'parent_id' => 'yllapito/alayhdistykset', 'active' => 'yllapito/alayhdistykset/yhdistykset');
$nav['yllapito/alayhdistykset/lisaa_yhdistys'] = array('label' => 'Lisää yhdistys', 'parent_id' => 'yllapito/alayhdistykset', 'active' => 'yllapito/alayhdistykset/lisaa_yhdistys');
$nav['yllapito/alayhdistykset/tapahtumat'] = array('label' => 'Hallitse tapahtumia', 'parent_id' => 'yllapito/alayhdistykset', 'active' => 'yllapito/alayhdistykset/tapahtumat');


$nav['alayhdistykset/kilpailujaokset'] = array('label' => 'Kilpailujaokset', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/kilpailujaokset');
$nav['alayhdistykset/kantakirjat'] = array('label' => 'Kantakirjat', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/kantakirjat');
$nav['alayhdistykset/laatuarvostelut'] = array('label' => 'Muut laatuarvostelut', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/laatuarvostelut');
$nav['alayhdistykset/rotuyhdistykset'] = array('label' => 'Rotuyhdistykset', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/rotuyhdistykset');

$nav['alayhdistykset/tapahtumat'] = array('label' => 'Tapahtuma-arkisto', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/tapahtumat$|alayhdistykset/tapahtumat/:any');
$nav['alayhdistykset/tapahtuma'] = array('hidden'=>TRUE, 'label' => 'Tapahtuma', 'parent_id' => 'alayhdistykset', 'active' => 'alayhdistykset/tapahtuma$|alayhdistykset/tapahtuma/:any');




