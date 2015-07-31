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
 
// about sub menu
$nav['jasenyys/liity'] = array('label' => 'Services', 'parent_id' => 'jasenyys');
$nav['jasenyys/team'] = array('label' => 'Team', 'parent_id' => 'jasenyys');
$nav['jasenyys/what-they-say'] = array('label' => 'What They Say', 'parent_id' => 'jasenyys');
