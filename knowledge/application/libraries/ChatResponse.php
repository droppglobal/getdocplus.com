<?php
#[\AllowDynamicProperties]
class ChatResponse{
	public $id="";
	public $msg="unknown";
    public $header_msg="";
	public $currentItem=null;
	public $open_user_id="";
	public $lastData=[];
	public $temp_id_prefix;
    public $start_time;
    public $isStarted=false;
    public $status;
    public $clientImg="";
    public $isAdminTyping=false;
    public $isUserTyping=false;
    public $isResetChatWindow=false;
	function __construct()
    {
        if(function_exists("base_url")){
            $this->temp_id_prefix=hash("crc32b",base_url())."_";
        }else{
            $this->temp_id_prefix="APBDWCHT_";
        }
    }
    function StartChat($id,$msg=""){
	    $this->id=$id;
	    $this->msg=$msg;
	    $this->status=true;
	    $this->Display();
    }
    function SetCurrentItem($obj)
    {
        $this->currentItem = $obj;
    }
    function SetHeaderMessage($message){
        if(!is_string($message)){
            $message="Unknow message";
        }
        $this->header_msg=$message;
    }
    function AddItem($obj){
        $this->lastData[]=$obj;
    }
	function Display(){
		echo json_encode ( $this );die;
	}
}
if(!function_exists("__")){
	function __($msgid)
	{
		return $msgid;  
	}
}