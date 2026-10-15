<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class LDAP_auth{
            
            
            
	   public function auth($username, $password)
           {
			
			
                
                $CI =& get_instance();
                $CI->config->load('ldap_auth');

                $ds = $CI->config->item('ds');
                $server = $CI->config->item('ldap_server'); 
                $user_prefix = $CI->config->item('user_prefix'); 
                $user_suffix = $CI->config->item('user_suffix');
                $dc = $CI->config->item('dc');
                        
                $bind = @ldap_bind($ds,$user_prefix.$username.$user_suffix, $password);
                
                if ($bind){
                    return TRUE;
                    }
                else{
                    return FALSE;
                    }

	    }

		  
		  
	    public function info($username) {
                    
                $CI =& get_instance();
                $CI->config->load('ldap_auth');

	        $ds = $CI->config->item('ds');
	        $server = $CI->config->item('ldap_server'); 
	        $user_prefix = $CI->config->item('user_prefix'); 
	        $user_suffix = $CI->config->item('user_suffix');
	        $dc = $CI->config->item('dc');
			$sr = @ldap_search($ds, $dc,
				  "(&(objectCategory=user)(samAccountName=$username))");
			$info = null;
			if ($sr){
			  $info = @ldap_get_entries($ds, $sr);
			}
			return $info;
		}
   }