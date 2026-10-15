<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class CORE_Router extends CI_Router{
	function get_route_unique_id($uri=""){		
		if(empty($uri)){
			$uri=$this->directory.$this->class."/".$this->method;
		}
		$uri=strtolower($uri);
		$uri=str_replace("_", "-", $uri);		
		
		
	
		return hash("crc32b",$uri);
		
	}
	function get_route_all_method_unique_id(){		
		return $this->get_route_unique_id($this->directory.$this->class."/*");		
	}
	
	
	
	

}
