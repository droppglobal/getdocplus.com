<?php 
/**
 * Version 1.0.0
 * Creation date: 03/Oct/2017
 * @Written By: S.M. Sarwar Hasan
 * Sarwar Hasan
 */
defined('BASEPATH') OR exit('No direct script access allowed');
 APP_Controller::LoadConfirmController();
 require_once APPPATH."/libraries/google_ai_studio/Google_AI_Studio.php";
class Ticket_confirm extends APP_ConfirmController {


	function __construct() {
		parent::__construct();
		$this->CheckPageAccess( 'close_ticket,ai_translate_text,ai_rewrite_text,ai_translate_reply' );
	}


	function ticket_delete( $param = "" ) {
		
		if ( empty( $param ) ) {
			$this->SetConfirmResponse( false, "Invalid Request" );

			return;
		}
		$mr = new Mticket();
		$mr->id( $param );
		if ( $mr->Select() ) {
			if ( Mticket::DeleteByID( $param ) ) {
				AddLog( "D", "id={$param}", "l003", "Ticket_confirm" );
				$this->SetConfirmResponse( true, "Successfully deleted" );
			} else {
				$this->SetConfirmResponse( false, "Delete failed try again" );
			}
		}
	}

	function ticket_reply( $ticket_id = '' ) {
		$this->output->unset_template();
		$adminData = GetAdminData();
		if ( ! empty( $ticket_id ) ) {
			$reply_user_id   = "";
			$reply_user_Type = "";
			$ticketObj       = Mticket::FindBy( "id", $ticket_id );
			$reply_user_id   = $adminData->id;
			$reply_user_Type = "A";
			if ( ! empty( $ticketObj ) && ! empty( $reply_user_id ) && ! empty( $reply_user_Type ) ) {
				$reply_text    = PostValue( "ticket_body", "", false, true );
				$ticket_status = PostValue( "status", "" );
				$is_private    = PostValue( "is_private", "Y" );
				if ( empty( $ticket_status ) ) {
					$ticket_status = $ticketObj->status;
				} else {
					
					Mticket::UpdateStatus( $ticket_id, $ticket_status, $reply_user_id, $reply_user_Type );
				}
				if ( $ticketObj->is_public != "Y" ) {
					$is_private = "Y";
				}
				$hasPayment        = false;
				$is_enable_payment = Mticket_payment::has_enable_payment();
				if ( $is_enable_payment ) {
					$hasPayment = PostValue( "has_payment", "N" ) == "Y";
					if ( $hasPayment ) {
						$amount = PostValue( "amount", 0 );
						$des    = PostValue( "payment_des", "" );
						if ( $amount < 1 && $amount > 999.99 ) {
							$this->SetConfirmResponse( false, "Invalid amount.", "", false, "Ticket Replay", "ticket" );

							return;
						}
						if ( empty( $des ) ) {
							$this->SetConfirmResponse( false, "Payment description is required.", "", false,
								"Ticket Replay", "ticket" );

							return;
						}

					}
				}

				$objReplay = Mticket_reply::add( $ticket_id, $reply_user_id, $reply_user_Type, $reply_text,
					$ticket_status, $is_private, $ticketObj->assigned_on );
				if ( $objReplay ) {
					if ( $hasPayment ) {
						$currency   = PostValue( "payment_currency", "USD" );
						$paymentObj = Mticket_payment::add( $ticket_id, $objReplay->reply_id, $reply_user_id, $amount,
							$des, $currency );
						if ( $paymentObj ) {
							if ( Mticket_reply::update_payment_id( $ticket_id, $objReplay->reply_id,
								$paymentObj->id ) ) {
								Mticket_log::AddTicketLog( $ticket_id, $reply_user_id, $reply_user_Type,
									"Payment added", $ticket_status );
							}
						}
					}
					$file_upload_list = [];
					$path             = Mticket::get_ticket_file_path( $ticketObj->ticket_user, $ticket_id, false,
						$objReplay->reply_id );
					if ( app_make_dir( $path, 755, true ) ) {
						app_uploaded_files_ok( $file_upload_list, $path, "" );
					}

					$logs           = Mticket_log::FindAllBy( "ticket_id", $ticket_id, [], "entry_time", "DESC", 1 );
					$replies        = new stdClass();
					$replies->reply = GetTicketReplyHTMLBy( $objReplay->ticket_id, $objReplay->reply_id );
					$log_single     = ! empty( $logs[0] ) ? $logs[0] : null;;
					$log_user = "";
					if ( $log_single ) {
						$log_user = Mticket_log::get_log_user_name( $log_single );
					}
					$replies->log                = GetTicketLog( $log_single, $log_user );
					$replies->current_status     = $ticket_status;
					$replies->current_status_str = getTextByKey( $ticket_status,
						$ticketObj->GetPropertyOptionsTag( "status" ) );
					Mticket_reply::SendAdminNotification( $ticketObj, $objReplay );
					Mticket::SetSeenStatus( $ticket_id, false );
					$this->SetConfirmResponse( true, "Successfully added", $replies, false, "Ticket Reply", "ticket" );

					return;
				} else {
					$msg = GetMsgForAPI();
					$this->SetConfirmResponse( false, "Ticket reply saved error", $msg, false, "Ticket Reply",
						"ticket" );

					return;
				}

			}

			$this->SetConfirmResponse( false, "Ticket reply save failed", null, false, "Ticket Reply", "ticket" );

			return;


		} else {
			$this->SetConfirmResponse( false, "Ticket information is wrong", null, false, "Ticket Reply", "ticket" );
		}
	}

	function assign_me( $ticket_id = '' ) {
		$this->output->unset_template();
		$adminData = GetAdminData();
		if ( ! empty( $ticket_id ) ) {
			if ( Mticket::AssignUser( $ticket_id, $adminData->id, $adminData->id, true ) ) {
				$this->SetConfirmResponse( true, "Successfully assign", null, false, "Assign Me", "paperclip " );

				return;
			} else {
				$msg = GetMsgForAPI();
				$this->SetConfirmResponse( false, $msg, null, false, "Assign Me", "paperclip " );

				return;
			}
		}
		$this->SetConfirmResponse( false, "Failed assign", null, false, "Assign Me", "paperclip " );

		return;
	}

	function close_ticket( $ticket_id = '' ) {
		$this->output->unset_template();
		$adminData = GetAdminData();
		if ( ! empty( $ticket_id ) ) {
			$ticketObj = Mticket::FindBy( "id", $ticket_id );
			if ( ! empty( $ticketObj ) && Mticket::hasTicketReplyPermission( $ticketObj ) ) {
				$uTicket = new Mticket();
				$uTicket->status( 'C' );
				$uTicket->SetWhereUpdate( "id", $ticket_id );
				if ( $uTicket->Update() ) {
					Mticket_log::AddTicketLog( $ticket_id, $adminData->id, 'A', "Closed", 'C' );
					$this->SetConfirmResponse( true, "Ticket successfully closed", null, false, "Ticket close",
						"paperclip " );

					return;
				}
			} else {
				$this->SetConfirmResponse( false, "You do not have permission to do this", null, false, "Ticket close",
					"paperclip " );

			}
		}
		$this->SetConfirmResponse( false, "Ticket close failed", null, false, "Ticket close", "paperclip " );

		return;
	}
	function get_params($ticket){
		$ticket_user=Msite_user::FindBy("id", $ticket->ticket_user);
		$params="Who opened support ticket =".$ticket_user->first_name." ". $ticket_user->last_name.",";
		$params.="support ticket title=".$ticket->title.",";



		$currentUser=GetAdminData();
		if(!empty($currentUser->title) && !empty($currentUser->role_title)){
			$params.="who replying this ticket=".$currentUser->title.",";
			$params.="who replying his or her position=".$currentUser->role_title.",";
			
			
		}
		return $params;
	}

	function ai_rewrite_text( $ticket_id = '' ) {
		$this->output->unset_template();
		
		$reply_text=$this->input->post("reply",false);
		if (empty( $ticket_id ) ) {
			$this->SetConfirmResponse( false, "Ticket id is required", null, false, "Ticket close", "paperclip " );
			return;
		}
		if (empty( $reply_text ) ) {
			$this->SetConfirmResponse( false, "Reply text is required", null, false, "Ticket close", "paperclip " );
			return;
		}
		try{
			$ticket=Mticket::FindBy("id",$ticket_id);
			if(empty($ticket)) {
				throw new Exception("Ticket is not found");
			}
			$ai=new AI_Handler();
			$ai->set_ai(Mapp_settings_api_advance::GetSettingsValue( 'ai_studio', 'ai_rewriter' ,'G'));

			$ai->add_instruction("Rewrite the following text politely as a support ticket reply:");
			$ai->add_instruction('- Do not add a subject and keep the initial greeting ("Dear" or "Hi") as is.');
			$ai->add_instruction('- Preserve all HTML structure, including <p> and <br> tags, with each paragraph separated individually.');
			$ai->add_instruction('- Maintain the regards name and any company info present.');
			$ai->add_instruction('- Do not add any assumptions or mock text.');
			$ai->add_instruction('- Rewrite each paragraph without changing the HTML structure.');
			
			
			
			$params=$this->get_params($ticket);
			$ai->add_instruction("- use these information if required".$params);
			$ai->set_content($reply_text);
			$response=$ai->get_ai_response();
			$this->SetConfirmResponse( true, "Response", $response, false, "AI Response", "paperclip " );
			return;

		}catch (Exception $e){
			$this->SetConfirmResponse( false, $e->getMessage(), null, false, "AI Response", "paperclip " );
			return;
		}

		$this->SetConfirmResponse( false, "Unknown", null, false, "Ticket close", "paperclip " );
	}

	function ai_translate_text( $ticket_id = '') {
		$this->output->unset_template();
		$adminData = GetAdminData();

		$reply_text=$this->input->post("reply",false);
		$to=$this->input->post("to_lang",false);
		if (empty( $ticket_id ) ) {
			$this->SetConfirmResponse( false, "Ticket id is required", null, false, "AI Response", "paperclip " );
			return;
		}
		if (empty( $reply_text ) ) {
			$this->SetConfirmResponse( false, "Reply text is required", null, false, "AI Response", "paperclip " );
			return;
		}
		if (empty( $to ) ) {
			$this->SetConfirmResponse( false, "To language is required", null, false, "AI Response", "paperclip " );
			return;
		}
		try{
			$ticket=Mticket::FindBy("id",$ticket_id);
			if(empty($ticket)) {
				throw new Exception("Ticket is not found");
			}
			$lang_details=app_get_language_details($to);


			$ai=new AI_Handler();


			$ai->set_ai(Mapp_settings_api_advance::GetSettingsValue( 'ai_studio', 'ai_translator' ,'G'));

			

			$ai->add_instruction("Detect language and translate to $to");
			if(!empty($lang_details->is_rtl)){
				$ai->add_instruction("RTL mode");
			}else{
				$ai->add_instruction("LTR mode");
			}

			
			
			
			$ai->add_instruction("Do not add any mockdown text, only html and keep all p and all br tag same as input");
			
			
			$params=$this->get_params($ticket);
			$ai->add_instruction("use these information if required".$params);
			$ai->set_content($reply_text);

			$response=$ai->get_ai_response();
			
			$this->SetConfirmResponse( true, "Response", $response, false, "Smart Reply", "paperclip " );
			return;

		}catch (Exception $e){
			$this->SetConfirmResponse( false, $e->getMessage(), null, false, "Smart Reply", "paperclip " );
			return;
		}

		$this->SetConfirmResponse( false, "Unknown", null, false, "AI Response", "paperclip " );
	}
	function ai_translate_reply( $ticket_id = '') {
		$this->output->unset_template();
		$adminData = GetAdminData();
		$reply_id=$this->input->post("reply_id",false);
		$to=$this->input->post("to_lang",false);


		if (empty( $ticket_id ) ) {
			$this->SetConfirmResponse( false, "Ticket id is required", null, false, "AI Response", "paperclip " );
			return;
		}
		$is_ticket=false;
		if (empty( $reply_id ) ) {
			$is_ticket=true;
		}
		if (empty( $to ) ) {
			$this->SetConfirmResponse( false, "To language is required", null, false, "AI Response", "paperclip " );
			return;
		}
		if(!$is_ticket) {
			$reply_obj = Mticket_reply::FindBy( "reply_id", $reply_id, [ "ticket_id" => $ticket_id ] );
			if ( empty( $reply_obj ) ) {
				$this->SetConfirmResponse( false, "No reply found with the request param", null, false, "AI Response",
					"paperclip " );

				return;
			}
		}
		try{

			$ticket=Mticket::FindBy("id",$ticket_id);
			if(empty($ticket)) {
				throw new Exception("Ticket is not found");
			}
			$ticket_obj=new Mticket_meta();
			$meta_key='tr_'.$to;
			if(!$is_ticket) {
				$old = Mticket_meta::get_reply_meta( $ticket_id, $reply_id, $meta_key );
				if ( ! empty( $old ) ) {
					$this->SetConfirmResponse( true, "already exits", $old->meta_val, false, "AI Response",
						"paperclip " );

					return;
				}
			}else{
				$old = Mticket_meta::get_ticket_meta( $ticket_id,  $meta_key );
				if ( ! empty( $old ) ) {
					$this->SetConfirmResponse( true, "already exits", $old->meta_val, false, "AI Response",
						"paperclip " );

					return;
				}
			}
			$lang_details=app_get_language_details($to);

			$ai=new AI_Handler();
			
			$ai->set_ai(Mapp_settings_api_advance::GetSettingsValue( 'ai_studio', 'ai_translator' ,'G'));

			

			$ai->add_instruction("Detect language and translate to $to");
			if(!empty($lang_details->is_rtl)){
				$ai->add_instruction("RTL mode");
			}else{
				$ai->add_instruction("LTR mode");
			}

			
			
			
			$ai->add_instruction("Do not add any mockdown text, only html and keep all p and all br tag same as input");
			$params=$this->get_params($ticket);
			$ai->add_instruction("use these information if required".$params);
			if($is_ticket){
				$ai->set_content($ticket->ticket_body);
			}else{
				$ai->set_content($reply_obj->reply_text);
			}
			$response=$ai->get_ai_response();
			if(!empty($response)){
				if($is_ticket){
					if(Mticket_meta::save_ticket_meta($ticket_id,$meta_key,$response)){
						$this->SetConfirmResponse( true, "Response", $response, false, "AI Response", "paperclip " );
						return;
					}
				}else{
					if(Mticket_meta::save_reply_meta($ticket_id,$reply_id,$meta_key,$response)){
						$this->SetConfirmResponse( true, "Response", $response, false, "AI Response", "paperclip " );
						return;
					}
				}
			}
			
			$this->SetConfirmResponse( false, "translation failed", null, false, "AI Response", "paperclip " );
			return;

		}catch (Exception $e){
			$this->SetConfirmResponse( false, $e->getMessage(), null, false, "AI Response", "paperclip " );
			return;
		}

		$this->SetConfirmResponse( false, "Unknown", null, false, "AI Response", "paperclip " );
	}
	function ai_smart_reply( $ticket_id = '' ) {
		$this->output->unset_template();
		$adminData = GetAdminData();
		$reply_text=$this->input->post("reply",false);
		if (empty( $ticket_id ) ) {
			$this->SetConfirmResponse( false, "Ticket id is required", "", false, "Ticket close", "paperclip " );
			return;
		}
		try{
			$ticket=Mticket::FindBy("id",$ticket_id);
			if(empty($ticket)) {
				throw new Exception("Ticket is not found");
			}

			$ai = new Google_AI_Studio();
			$ai->add_part($ticket->ticket_body);
			$ai->add_part("Please write an reply form me");
			$response=$ai->get_row_response();
			AddFileLog(print_r($response,true),true,"ai_log.log");
			$this->SetConfirmResponse( false, "Test", $response, false, "Smart Reply", "paperclip " );
			return;

		}catch (Exception $e){
			$this->SetConfirmResponse( false, $e->getMessage(), null, false, "Smart Reply", "paperclip " );
			return;
		}


		$this->SetConfirmResponse( true, "Unknown".$ticket_id, "This is test", false, "Smart Reply", "paperclip " );
	}
}
