<?php
defined('BASEPATH') OR exit('No direct script access allowed');



if(!empty($_SERVER['HTTP_HOST'])){
    $config['base_url'] =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
    $config['base_url'] .=  "://".$_SERVER['HTTP_HOST'];
    $config['base_url'] .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
}else{
   $config['base_url']="http://192.168.10.71/Projects/support-system/";
}




$config['index_page'] = '';


$config['uri_protocol']	= 'REQUEST_URI';



$config['url_suffix'] = '.html';


$config['language']	= 'english';


$config['charset'] = 'UTF-8';


$config['enable_hooks'] = TRUE;


$config['subclass_prefix'] = 'APP_';


$config['composer_autoload'] = TRUE;


$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';



$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';


$config['log_threshold'] = 0;


$config['log_path'] =  '';


$config['log_file_extension'] = '';


$config['log_file_permissions'] = 0644;


$config['log_date_format'] = 'Y-m-d H:i:s';


$config['error_views_path'] = '';


$config['cache_path'] = '';


$config['cache_query_string'] = FALSE;


$config['encryption_key'] = '';


$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'APBSSID';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;


$config['sess_prefix'] = 'abd_ses';


$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;


$config['standardize_newlines'] = FALSE;


$config['global_xss_filtering'] = FALSE;


$config['csrf_protection'] = true;
$config['csrf_token_name'] = 'app_form';
$config['csrf_cookie_name'] = 'app_token';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array('cheque/change-margin',"cheque/set-print","chat/*","admin/admin-chat/*","test/*","admin/addons/admin-page/*","admin/addons/admin-ajax/*","admin/addons/action/*","site/action/*");


$config['compress_output'] = FALSE;


$config['time_reference'] = 'local';


$config['rewrite_short_tags'] = FALSE;



$config['proxy_ips'] = '';


if(!empty($_SERVER['REQUEST_METHOD'])){
	define ( 'IsPostBack', strtoupper($_SERVER['REQUEST_METHOD']) == 'POST');
}else{
	define ( 'IsPostBack',false);
}
$config['IsMultipleDB']=false;
$config['app_base_code']="ABSS";
$config['is_sql_mode']=false;