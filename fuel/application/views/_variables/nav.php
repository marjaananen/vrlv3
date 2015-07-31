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


$nav['liitto'] = 'Liitto';
$nav['jasenyys'] = array('label' => 'Jäsenyys', 'active' => 'jasenyys$|jasenyys/:any');
$nav['virtuaalitallit'] = array('label' => 'Virtuaalitallit', 'active' => 'blog$|blog/:any');
$nav['virtuaalihevoset'] = 'Virtuaalihevoset';
$nav['jalostus-ja-kasvatus'] = 'Jalostus ja kasvatus';
$nav['kilpailutoiminta'] = 'Kilpailutoiminta';
$nav['seurat-ja-yhdistykset'] = 'Seurat ja yhdistykset';
$nav['yllapito'] = array('label'=>'Ylläpito', 'hidden'=> TRUE, 'active' => 'yllapito');
 
// jäsenyys alamenu
$nav['jasenyys/liity'] = array('label' => 'Liity jäseneksi', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/liity');
$nav['jasenyys/jasenet'] = array('label' => 'Jäsenet', 'parent_id' => 'jasenyys', 'active' => 'jasenyys/jasenet');
//$nav['jasenyys/what-they-say'] = array('label' => 'Linkki 3', 'parent_id' => 'jasenyys');

// ylläpito alamenu
$nav['yllapito/tunnukset'] = array('label' => 'Tunnukset', 'parent_id' => 'yllapito', 'active' => 'yllapito/tunnukset');
//$nav['yllapito/hevosrekisteri'] = array('label' => 'Hevosrekisteri', 'parent_id' => 'yllapito', 'active' => 'yllapito/hevosrekisteri');

// ylläpito/tunnukset alamenu
$nav['yllapito/tunnukset/hyvaksy'] = array('label' => 'Hyväksy VRL-tunnuksia', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/hyvaksy');
$nav['yllapito/tunnukset/muokkaa'] = array('label' => 'Muokkaa tunnuksen tietoja', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/muokkaa');
$nav['yllapito/tunnukset/haku'] = array('label' => 'Tee hakuja tunnuksista', 'parent_id' => 'yllapito/tunnukset', 'active' => 'yllapito/tunnukset/haku');



