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

$route['default_controller'] = "common";
$route['404_override'] = 'common/error404';

$route['magico'] = $route['default_controller'] . '/magico';
$route['magico_login'] = $route['default_controller'] . '/magico_login';
$route['magico_logout'] = $route['default_controller'] . '/magico_logout';

$route['abm/(:any)/(:any)'] = 'abm/$1/$2';

$route['puertas-de-entrada/(:any)/(:any)'] = 'puertasdeentrada/disciplina';
$route['puertas-de-entrada'] = 'puertasdeentrada';
$route['informacion/nuestra-casa'] = 'informacion/nuestraCasa';
$route['informacion/equipo'] = 'informacion/equipo/';
$route['informacion/equipo/(:any)'] = 'informacion/miembroEquipo/';
$route['informacion/(:any)'] = 'informacion/estatica/';
$route['novedades/ajaxMasNotas/(:any)'] = 'novedades/ajaxMasNotas/$1';
$route['novedades/(:any)'] = 'novedades/nota/';
$route['docentes/(:any)'] = 'docentes/docente/';
$route['agenda/regular'] = 'agenda/regular';
$route['agenda/mensual'] = 'agenda/especial';
$route['agenda/(:any)'] = 'agenda/actividadEspecial';


$route['inscripciones/inscripcionCancelada'] = 'inscripciones/inscripcionCancelada';
$route['inscripciones/aprobado'] = 'inscripciones/aprobado';
$route['inscripciones/pago_aprobado'] = 'inscripciones/pago_aprobado';
$route['inscripciones/pago_rechazado'] = 'inscripciones/pago_rechazado';
$route['inscripciones/pago_pendiente'] = 'inscripciones/pago_pendiente';
$route['inscripciones/revisado'] = 'inscripciones/revisado';
$route['inscripciones/inscribirse_regular'] = 'inscripciones/inscribirse_regular';
$route['inscripciones/inscribirse_especial/(:any)'] = 'inscripciones/inscribirse_especial/$1';
$route['inscripciones/(:any)/(:any)'] = 'inscripciones/inscripcion/$1/$2';
$route['inscripciones/(:any)'] = 'inscripciones/inscripcion/$1';


/*// example: '/en/about' -> use controller 'about'
$route['^es/(.+)$'] = "$1";
$route['^en/(.+)$'] = "$1";
 
// '/en' and '/fr' -> use default controller
$route['^es'] = $route['default_controller'];
$route['^en$'] = $route['default_controller'];*/

/* End of file routes.php */
/* Location: ./application/config/routes.php */
