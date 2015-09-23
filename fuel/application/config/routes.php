<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/



//Profiilikontrollerin lisaa yhteystietoja ei ole menuissa

$route['yllapito/tunnukset/hyvaksy'] = "yllapito/hakemusjono";

$route['yllapito/tallirekisteri/tallijono/hyvaksy'] = "yllapito/tallijono";
$route['yllapito/tallirekisteri/tallijono/hyvaksy_kasittele/(:any)/(:num)'] = "yllapito/kasittele_talli/$1/$2";
$route['yllapito/tallirekisteri/tallijono/muokkaa/(:any)/(:any)'] = "tallit/muokkaa/$1/$2";

$route['yllapito/tallirekisteri/kategoriajono/hyvaksy'] = "yllapito/tallikategoriajono";
$route['yllapito/tallirekisteri/kategoriajono/hyvaksy_kasittele/(:any)/(:num)'] = "yllapito/kasittele_tallikategoria/$1/$2";


$route['profiili/omat-tallit'] = "tallit/index";
$route['profiili/omat-tallit/rekisteroi'] = "tallit/rekisteroi";
$route['profiili/omat-tallit/muokkaa/(:any)/(:any)'] = "tallit/muokkaa/$1/$2";


$route['tallirekisteri/talli/(:any)'] = "tallit/talliprofiili/$1";
$route['tallirekisteri/haku'] = "tallit/haku";


$route['tunnus/(:any)'] = "jasenyys/tunnus/$1";
$route['tunnus/(:any)/(:any)'] = "jasenyys/tunnus/$1/$2";


$route['jasenyys/jasenet'] = "jasenyys/haku";

//////////////////////////////////////

$route['default_controller'] = 'main';
$route['404_override'] = 'fuel/page_router';

/*	
| Uncomment this line if you want to use the automatically generated sitemap based on your navigation.
| To modify the sitemap.xml, go to the views/sitemap_xml.php file.
*/ 
//$route['sitemap.xml'] = 'sitemap_xml';

include(MODULES_PATH.'/fuel/config/fuel_routes.php');

/* End of file routes.php */
/* Location: ./application/config/routes.php */