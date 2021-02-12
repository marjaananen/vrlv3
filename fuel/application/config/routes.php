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
$route['yllapito'] = "main/yllapito";
$route['yllapito/tunnukset'] = "yllapito_tunnukset/hakemusjono_etusivu";
$route['yllapito/tunnukset/hyvaksy'] = "yllapito_tunnukset/hakemusjono";
$route['yllapito/tunnukset/muokkaa'] = "yllapito_tunnukset/muokkaa";
$route['yllapito/tunnukset/muokkaa/(:any)'] = "yllapito_tunnukset/muokkaa/$1";
$route['yllapito/tunnukset/oikeudet'] = "yllapito_tunnukset/oikeudet";
$route['yllapito/tunnukset/oikeudet/(:any)'] = "yllapito_tunnukset/oikeudet/$1";
$route['yllapito/tunnukset/kirjautumiset'] = "yllapito_tunnukset/kirjautumiset";
$route['yllapito/tunnukset/kirjautumiset/(:any)'] = "yllapito_tunnukset/kirjautumiset/$1";
$route['yllapito/tunnukset/kasittele/(:any)/(:any)'] = "yllapito_tunnukset/kasittele_hakemus/$1/$2";


$route['yllapito/tiedotukset'] = "yllapito_tiedotukset";
$route['yllapito/tiedotukset/(:any)'] = "yllapito_tiedotukset/$1";
$route['yllapito/tiedotukset/(:any)/(:any)'] = "yllapito_tiedotukset/$1/$2";

$route['yllapito/hevosrekisteri'] = "yllapito_hevosrekisteri";
$route['yllapito/hevosrekisteri/poista/(:any)'] = "virtuaalihevoset/poista/$1";
$route['yllapito/hevosrekisteri/muokkaa/(:any)'] = "virtuaalihevoset/muokkaa/$1";
$route['yllapito/hevosrekisteri/muokkaa/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2";
$route['yllapito/hevosrekisteri/muokkaa/(:any)/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2/$3";
$route['yllapito/hevosrekisteri/muokkaa/(:any)/(:any)/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2/$3/$4";
$route['yllapito/hevosrekisteri/(:any)'] = "yllapito_hevosrekisteri/$1";
$route['yllapito/hevosrekisteri/(:any)/(:any)'] = "yllapito_hevosrekisteri/$1/$2";
$route['yllapito/hevosrekisteri/(:any)/(:any)/(:any)'] = "yllapito_hevosrekisteri/$1/$2/$3";


$route['yllapito/jaokset'] = "yllapito_jaokset";
$route['yllapito/jaokset/tapahtumat'] = "yllapito_jaokset/tapahtumat/";
$route['yllapito/jaokset/tapahtumat/(:any)'] = "yllapito_jaokset/tapahtumat/$1";
$route['yllapito/jaokset/(:any)'] = "yllapito_jaokset/$1";
$route['yllapito/jaokset/(:any)/(:any)'] = "yllapito_jaokset/$1/$2";
$route['yllapito/jaokset/(:any)/(:any)/(:any)'] = "yllapito_jaokset/$1/$2/$3";
$route['yllapito/jaokset/(:any)/(:any)/(:any)/(:any)'] = "yllapito_jaokset/$1/$2/$3/$4";
$route['yllapito/jaokset/(:any)/(:any)/(:any)/(:any)/(:any)'] = "yllapito_jaokset/$1/$2/$3/$4/$5";
$route['yllapito/jaokset/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = "yllapito_jaokset/$1/$2/$3/$4/$5/$6";


$route['yllapito/alayhdistykset'] = "yllapito_puljut";
$route['yllapito/alayhdistykset/tapahtumat'] = "yllapito_puljut/tapahtumat/";
$route['yllapito/alayhdistykset/tapahtumat/(:any)'] = "yllapito_puljut/tapahtumat/$1";
$route['yllapito/alayhdistykset/(:any)'] = "yllapito_puljut/$1";
$route['yllapito/alayhdistykset/(:any)/(:any)'] = "yllapito_puljut/$1/$2";
$route['yllapito/alayhdistykset/(:any)/(:any)/(:any)'] = "yllapito_puljut/$1/$2/$3";
$route['yllapito/alayhdistykset/(:any)/(:any)/(:any)/(:any)'] = "yllapito_puljut/$1/$2/$3/$4";
$route['yllapito/alayhdistykset/(:any)/(:any)/(:any)/(:any)/(:any)'] = "yllapito_puljut/$1/$2/$3/$4/$5";
$route['yllapito/alayhdistykset/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)'] = "yllapito_puljut/$1/$2/$3/$4/$5/$6";

$route['yllapito/kalenterit'] = "yllapito_kalenterit";
$route['yllapito/kalenterit/kisahyvaksynta'] = "yllapito_kalenterit/kisahyvaksynta";
$route['yllapito/kalenterit/kisahyvaksynta/(:any)'] = "yllapito_kalenterit/kisahyvaksynta/$1";
$route['yllapito/kalenterit/kisahyvaksynta/(:any)/(:any)'] = "yllapito_kalenterit/kisahyvaksynta/$1/$2";
$route['yllapito/kalenterit/kisahyvaksynta/(:any)/(:any)/(:any)'] = "yllapito_kalenterit/kisahyvaksynta/$1/$2/$3";
$route['yllapito/kalenterit/tuloshyvaksynta'] = "yllapito_kalenterit/tuloshyvaksynta";
$route['yllapito/kalenterit/tuloshyvaksynta/(:any)'] = "yllapito_kalenterit/tuloshyvaksynta/$1";
$route['yllapito/kalenterit/tuloshyvaksynta/(:any)/(:any)'] = "yllapito_kalenterit/tuloshyvaksynta/$1/$2";
$route['yllapito/kalenterit/tuloshyvaksynta/(:any)/(:any)/(:any)'] = "yllapito_kalenterit/tuloshyvaksynta/$1/$2/$3";
$route['yllapito/kalenterit/hyvaksytyttulokset'] = "yllapito_kalenterit/hyvaksytyttulokset";
$route['yllapito/kalenterit/hyvaksytytkisat'] = "yllapito_kalenterit/hyvaksytytkisat";
$route['yllapito/kalenterit/hyvaksytytkisat/delete/(:any)/(:any)'] = "yllapito_kalenterit/hyvaksytytkisat/delete/$1/$2";
$route['yllapito/kalenterit/hyvaksytytkisat/edit/(:any)/(:any)'] = "yllapito_kalenterit/hyvaksytytkisat/edit/$1/$2";
$route['yllapito/kalenterit/hyvaksytyttulokset/delete/(:any)/(:any)'] = "yllapito_kalenterit/hyvaksytyttulokset/delete/$1/$2";
$route['yllapito/kalenterit/porrastetut_run'] = "yllapito_kalenterit/porrastetut_run";
$route['yllapito/kalenterit/etuuspisteet'] = "yllapito_kalenterit/etuuspisteet";
$route['yllapito/kalenterit/etuuspisteet/(:any)'] = "yllapito_kalenterit/etuuspisteet/$1";
$route['yllapito/kalenterit/etuuspisteet/(:any)/(:any)'] = "yllapito_kalenterit/etuuspisteet/$1/$2";





$route['kilpailutoiminta/ilmoita_tulokset'] = "kilpailutoiminta/omat/kisat/avoimet";
$route['kilpailutoiminta/ilmoita_tulokset/kisat'] = "kilpailutoiminta/omat/kisat/avoimet";
$route['kilpailutoiminta/ilmoita_tulokset/nayttelyt'] = "kilpailutoiminta/omat/nayttelyt/avoimet";

$route['kilpailutoiminta/ilmoita_tulokset/kisat/(:any)'] = "kilpailutoiminta/ilmoita_tulokset/kisat/$1";
$route['kilpailutoiminta/ilmoita_tulokset/nayttelyt/(:any)'] = "kilpailutoiminta/ilmoita_tulokset/nayttelyt/$1";

$route['kilpailutoiminta/omat/kisat/tulosjonossa/delete/(:any)'] = "kilpailutoiminta/omat_delete/kisat/tulos/$1";
$route['kilpailutoiminta/omat/kisat/jonossa/delete/(:any)'] = "kilpailutoiminta/omat_delete/kisat/kisat/$1";

$route['kilpailutoiminta/omat/nayttelyt/tulosjonossa/delete/(:any)'] = "kilpailutoiminta/omat_delete/nayttelyt/tulos/$1";
$route['kilpailutoiminta/omat/nayttelyt/jonossa/delete/(:any)'] = "kilpailutoiminta/omat_delete/nayttelyt/kisa/$1";








//stables
$route['tallit/talli/(:any)'] = "tallit/talliprofiili/$1";
$route['tallit/talli/(:any)/(:any)'] = "tallit/talliprofiili/$1/$2";
$route['tallit/muokkaa/(:any)'] = "tallit/muokkaa/$1";
$route['tallit/rekisterointi'] = "tallit/rekisterointi";

$route['virtuaalihevoset/hevonen/(:any)'] = "virtuaalihevoset/hevosprofiili/$1";
$route['virtuaalihevoset/hevonen/(:any)/(:any)'] = "virtuaalihevoset/hevosprofiili/$1/$2";

$route['virtuaalihevoset/hevonen/muokkaa/(:any)'] = "virtuaalihevoset/muokkaa/$1";
$route['virtuaalihevoset/hevonen/muokkaa/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2";
$route['virtuaalihevoset/hevonen/muokkaa/(:any)/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2/$3";
$route['virtuaalihevoset/hevonen/muokkaa/(:any)/(:any)/(:any)/(:any)'] = "virtuaalihevoset/muokkaa/$1/$2/$3/$4";

$route['tunnus'] = "jasenyys/tunnus/";
$route['tunnus/(:any)'] = "jasenyys/tunnus/$1";
$route['tunnus/(:any)/(:any)'] = "jasenyys/tunnus/$1/$2";
$route['jasenyys'] = "jasenyys";
$route['kisakeskus'] = "kisakeskus";
$route['liitto'] = "liitto";
$route['liitto/rajapinta'] = "rajapinta";


$route['profiili/vaihda_salasana'] = 'auth/change_password';
$route['profiili/tunnus'] = "jasenyys/tunnus/";
$route['profiili/tunnus/(:any)/(:any)'] = "jasenyys/tunnus/$1/$2";




$route['default_controller'] = 'Main';


$route['404_override'] = 'fuel/page_router';
$route['translate_uri_dashes'] = FALSE;

/*	
| Uncomment this line if you want to use the automatically generated sitemap based on your navigation.
| To modify the sitemap.xml, go to the views/sitemap_xml.php file.
*/ 
//$route['sitemap.xml'] = 'sitemap_xml';

include(MODULES_PATH.'/fuel/config/fuel_routes.php');