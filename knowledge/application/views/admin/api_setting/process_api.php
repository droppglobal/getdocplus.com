<?php
$mainObj=APP_API::get_api_object($api_name);
$this->SetTitle("Test");
if($mainObj){
	echo $mainObj->do_porcess();
}