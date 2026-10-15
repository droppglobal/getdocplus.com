<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$hook['pre_controller'][] = array(
    'class'    => 'AppSecurity',
    'function' => 'CleanRequestParam',
    'filename' => 'AppSecurity.php',
    'filepath' => 'hooks'
);
$hook['pre_controller'][]= array(
	'class'    => 'AddOnManager',
	'function' => 'PreController',
	'filename' => 'AddOnManager.php',
	'filepath' => 'hooks'
	
);
$hook['post_controller_constructor'][] = array(
    'class'    => 'AddOnManager',
    'function' => 'LoadAddons',
    'filename' => 'AddOnManager.php',
    'filepath' => 'hooks'
);
$hook['post_controller_constructor'][]= array(
    'class'    => 'ProviderSelector',
    'function' => 'setProvider',
    'filename' => 'ProviderSelector.php',
    'filepath' => 'hooks',
    
);
$hook['post_controller_constructor'][] = array(
		'class'    => 'AppConfigHook',
		'function' => 'Setup',
		'filename' => 'AppConfigHook.php',
		'filepath' => 'hooks',
		
);
$hook['post_controller_constructor'][]= array(
    'class'    => 'ThemeSelector',
    'function' => 'setThemeAndLayout',
    'filename' => 'ThemeSelector.php',
    'filepath' => 'hooks',
    
);
$hook['post_controller_constructor'][]= array(
	'class'    => 'AddOnManager',
	'function' => 'PostCallBack',
	'filename' => 'AddOnManager.php',
	'filepath' => 'hooks'
	
);


