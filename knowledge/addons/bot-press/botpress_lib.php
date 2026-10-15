<?php

class botpress_lib {
	public function __construct() {

		add_action( 'action-bp-create-ticket', [ $this, "create_ticket" ], 10, 0 );

	}
	function get_request_content_type() {
		if ( ! empty( $_SERVER['HTTP_CONTENT_TYPE'] ) ) {
			return  $_SERVER['HTTP_CONTENT_TYPE'];
		}
		if ( ! empty( $_SERVER['CONTENT_TYPE'] ) ) {
			return $_SERVER['CONTENT_TYPE'] ;
		}
	}
	public function create_ticket() {
		$req_type = $this->get_request_content_type();
		if ( ! empty( $req_type ) ) {
			$req_type = strtolower( $this->get_request_content_type() );
		}
		$_posted=[];
		if ( 'application/x-www-form-urlencoded' == $req_type || 'application/json' == $req_type ) {
			$_posted = file_get_contents( 'php://input' );
			if(!empty($_posted)){
				$_posted=json_decode( $_posted, true );
			}
		}
		AddFileLog($req_type,true,"bot_create.log");
		AddFileLog($_SERVER,true,"bot_create.log");
		AddFileLog($_posted,true,"bot_create.log");
		echo "Successfully Ticket created";
		exit;
	}
}