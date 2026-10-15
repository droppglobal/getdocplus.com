<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$config['hybridauth'] = array(
  "providers" => array(
    
   "Envato" => array(
      "enabled" => FALSE,
      "keys" => array("id" => "", "secret" => ""),
    ),
    "Google" => array(
      "enabled" => FALSE,
	   "scope"   =>'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
		, 
      "keys" => array("id" => "", "secret" => ""),
    ),
    "Facebook" => array(
      "enabled" => FALSE,
      "keys" => array("id" => "", "secret" => ""),
      "scope"   => array("email"), 

      "trustForwarded" => FALSE,
    ),
    "Twitter" => array(
      "enabled" => FALSE,
      "keys" => array("key" => "", "secret" => ""),
      "includeEmail" => TRUE,
    ),
    
    "LinkedIn" => array(
      "enabled" => FALSE,
      "keys" => array("id" => "", "secret" => ""),
      "scope"   => array("r_liteprofile r_emailaddress"), 
      "fields"  => array("id", "email-address", "first-name", "last-name","picture-url","public-profile-url"), 
      
    ),
    
      "GitHub" => array(
          "enabled" => FALSE,
          "keys" => array("id" => "", "secret" => ""),
          "scope"=>"user:email"
      ),
      "Yahoo" => array(
          "enabled" => FALSE,
          "keys" => array("id" => "", "secret" => ""),
      ),
      "Discord" => array ( 
          "enabled" => false,
          "keys" => array ( "key" => "", "secret" => "" )
      ),
  ),
  
  
  
  
  "debug_mode" => ENVIRONMENT === 'development',
  
  "debug_file" => APPPATH . 'logs/hybridauth.log',
);
