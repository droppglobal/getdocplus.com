<?php

/**
 * Class ChatAdminResponse
 * @property ChatResponse[] $chatlist
 */
#[\AllowDynamicProperties]
class ChatAdminResponse{
	public $current_user_id;
    public $isAdminTyping=false;
    public $isUserTyping=false;
    public $isResetChatWindow=false;
    public $currentItem=null;
    public $chatlist=[];
	function __construct()
    {

    }
	function SetHeaderMessage($message){
		if(!is_string($message)){
			$message="Unknow message";
		}
		$this->header_msg=$message;
	}
    function SetCurrentItem($obj)
    {
        $this->currentItem = $obj;
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