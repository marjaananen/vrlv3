<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method

*/

//Admin urls, 
$route['yllapito/tunnukset'] = "yllapito_tunnukset/hakemusjono_etusivu";
$route['yllapito/tunnukset/hyvaksy'] = "yllapito_tunnukset/hakemusjono";
$route['yllapito/tunnukset/kasittele/(:any)/(:any)'] = "yllapito_tunnukset/kasittele_hakemus/$1/$2";

$route['yllapito/tallirekisteri'] = "yllapito_tallirekisteri/tallirekisteri_etusivu";
$route['yllapito/tallirekisteri/hyvaksy'] = "yllapito_tallirekisteri/tallijono";
$route['yllapito/tallirekisteri/hyvaksy_kasittele/(:any)/(:num)'] = "yllapito_tallirekisteri/kasittele_talli/$1/$2";
$route['yllapito/tallirekisteri/muokkaa'] = "yllapito_tallirekisteri/muokkaa_talli";
$route['yllapito/tallirekisteri/haku'] = "yllapito_tallirekisteri/talli_haku";

//profile urls
$route['profiili/omat-tallit'] = "profiili_tallit/index";
$route['profiili/omat-tallit/rekisteroi'] = "profiili_tallit/rekisteroi_talli";
$route['profiili/omat-tallit/muokkaa/(:any)/(:any)'] = "profiili_tallit/muokkaa_talli/$1/$2";


//public urls
$route['tallirekisteri/talli/(:any)'] = "tallit/talliprofiili/$1";
$route['tunnus/(:any)'] = "jasenyys/tunnus/$1";
$route['tunnus/(:any)/(:any)'] = "jasenyys/tunnus/$1/$2";
$route['jasenyys'] = "jasenyys";
$route['kisakeskus'] = "kisakeskus";
$route['liitto'] = "liitto";


$route['default_controller'] = 'Main';

$route['404_override'] = 'fuel/page_router';
$route['translate_uri_dashes'] = FALSE;

/*	
| Uncomment this line if you want to use the automatically generated sitemap based on your navigation.
| To modify the sitemap.xml, go to the views/sitemap_xml.php file.
*/ 
//$route['sitemap.xml'] = 'sitemap_xml';

include(MODULES_PATH.'/fuel/config/fuel_routes.php');