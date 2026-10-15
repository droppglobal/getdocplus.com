<?php
class APPAPIResponse {
    public $status=false;
    public $code=200;
    public $msg="Unknown";
    
    public $data=null;
    public $rtime;
    function __construct(){
        $this->rtime=time();
    }
    function displayResponse(){
	    http_response_code($this->code);
	    header('Content-Type: application/json; charset=utf-8');
        echo(json_encode($this));
        exit;
    }
    function setResponse($status,$msg,$data=null,$response_code=200){
        $this->status=$status;
        $this->msg=$msg;      
        $this->data=$data;         
        $this->code=$response_code;
    }
	function displayErrorResponse($status,$msg,$response_code=422,$data=null){
    	$this->displayWithResponse($status,$msg,$data,$response_code);
	}
	function displayWithResponse($status,$msg,$data=null,$response_code=200){
		$this->status=$status;
		$this->msg=$msg;
		$this->data=$data;
		$this->code=$response_code;
		$this->displayResponse();
	}
}