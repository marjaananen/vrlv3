<?php 

// declared here so we don't have to in each controller's variable file
$CI =& get_instance();

// generic global page variables used for all pages

$vars = array();
$vars['layout'] = 'main';
$vars['page_title'] = fuel_nav(array('render_type' => 'page_title', 'delimiter' => ':', 'order' => 'desc', 'home_link' => 'Home'));
$vars['meta_keywords'] = '';
$vars['meta_description'] = '';
$vars['js'] = array();
$vars['css'] = array();
$vars['body_class'] = uri_segment(1).' '.uri_segment(2);
$vars['sidemenu'] = fuel_nav(array('container_tag_id' => 'sidebar', 'container_tag_class' => 'nav nav-stacked', 'parent' => uri_segment(1)));
$vars['adminmainmenu'] = fuel_nav(array('container_tag_id' => 'sidebar', 'container_tag_class' => 'nav nav-stacked', 'parent' => 'yllapito', 'depth'=>'0'));
$vars['main_quickmenu'] = '<li><a href="http://vf.marsupieni.net/viewtopic.php?f=3&t=1308">Yhteystiedot</a></li>
<li><a href=" ">Rekisteröidy jäseneksi</a></li>
<li><a href=" ">Hae työntekijäksi</a></li>
<li><a href="http://www.virtuaalihevoset.net/wiki/index.php/Etusivu">VirtuaaliWiki</a></li>    
';
$vars['yllapito_nimet'] = "<b>Marsupieni</b> (VRL-01026) ja <b>pipariina</b> (VRL-00050)";
// page specific variables
$pages = array();

?>