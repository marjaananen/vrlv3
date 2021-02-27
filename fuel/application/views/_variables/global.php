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
<li><a href="'.site_url('liitto/mainosta').'">Mainosta sivuillamme!</a></li>

<li><a href="http://www.virtuaalihevoset.net/wiki/index.php/Etusivu">VirtuaaliWiki</a></li>    
';
// page specific variables
$pages = array();

?>