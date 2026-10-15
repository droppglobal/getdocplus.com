<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'site';
$route['404_override'] = 'App404';
$route['translate_uri_dashes'] = TRUE;


$route['a/(.+)'] = "site/addon/$1";
$route['admin/(:any)'] = "admin/$1";
$route['admin'] = "admin/dashboard";
$route['common-script.js'] = "common-script/js";
