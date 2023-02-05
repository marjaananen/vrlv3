<?php 

// declared here so we don't have to in each controller's variable file
$CI =& get_instance();

// generic global page variables used for all pages

$vars = array();
$vars['layout'] = 'main';
$vars['page_title'] = fuel_nav(array('render_type' => 'page_title', 'delimiter' => ' | ', 'order' => 'desc', 'home_link' => 'Virtuaalinen Ratsastajainliitto'));
$vars['meta_keywords'] = '';
$vars['meta_description'] = '';
$vars['js'] = array();
$vars['css'] = array();
$vars['body_class'] = uri_segment(1).' '.uri_segment(2);
$vars['sidemenu'] = fuel_nav(array('container_tag_id' => 'sidebar', 'container_tag_class' => 'nav nav-stacked', 'parent' => uri_segment(1)));
$vars['adminmainmenu'] = fuel_nav(array('container_tag_id' => 'sidebar', 'container_tag_class' => 'nav nav-stacked', 'parent' => 'yllapito', 'depth'=>'0'));
$vars['main_quickmenu'] = '<li><a href="'.site_url('liitto/yllapito').'">Yhteystiedot</a></li>
<li><a href="'.site_url('jasenyys/liity').'">Rekisteröidy jäseneksi</a></li>
<li><a href="'.site_url().'wiki">VirtuaaliWiki</a></li>    
';



$vars['red_bar'] = false;
$vars['red_bar_message'] = "Uusi VRL on valmis käyttöön! Vanhat salasanat on nollattu, mutta saat uuden käyttöösi palauttamalla salasanan yläpalkin \"unohditko salasanasi\" toiminnon avulla!";

//$vars['red_bar_message'] = "Uusi VRL on nyt otettu käyttöön! Osoite virtuaalihevoset.net osoittaa toistaiseksi vanhaan VRL:oon,
//joka ei ole enää käytössä, mutta sen vaihtaminen on työn alla.";
// page specific variables
$pages = array();

?>