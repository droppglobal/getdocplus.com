<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_setting extends APP_Controller {
	function __construct() {
		parent::__construct();
		$this->CheckPageAccess( 'enable_cwr,change_admin_color' );
		if ( Mapp_setting::GetSettingsValue( "is_first_run", "Y" ) == "Y" ) {
			Mapp_setting::SetInitialSettings();
			Mapp_setting::UpdateSettingsOrAdd( "is_first_run", "N" );
		}
	}

	function index() {

	}

	function general() {
		AddAppHTMLEditor();
		
		
		$this->SetTitle( "Application Settings" );
		$this->SetSubtitle( "Application All Settings" );
		$mainobj   = new Mapp_settings_advance();
		$apiobject = new Mapp_settings_api_advance();
		$apiobject->SetAPIName( "system" );
		$this->AddViewData( "apiobject", $apiobject );
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function security() {
		
		$this->SetTitle( "Security Settings" );
		$this->SetPOPUPIconClass( "ap ap-shield" );
		$this->SetSubtitle( "Application Security Settings" );
		$mainobj = new Mapp_settings_advance();
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function notification() {
		
		$this->SetTitle( "Admin Notification Settings" );
		$this->SetPOPUPIconClass( "fa fa-bell" );
		$this->SetSubtitle( "Ticket open or reply notification settings" );
		$mainobj = new Mapp_settings_advance();
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function imap() {

		$this->SetTitle( "Email To Ticket Settings" );
		$this->SetSubtitle( "Imap Settings" );
		$mainobj = new Mapp_settings_advance();
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function email_out_settings() {
		$this->SetTitle( "Email Outgoing Settings" );
		$this->SetSubtitle( "Sendmail or SMTP" );
		$mainobj = new Mapp_settings_advance();
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function theme() {
		
		$this->SetTitle( "Theme Settings" );
		$this->SetPOPUPIconClass( "ap ap-shield" );
		$this->SetSubtitle( "Application Theme Settings" );
		$mainobj = new Mapp_settings_advance();
		$this->AddViewData( "mainobj", $mainobj );
		$this->Display();
	}

	function fb_msg_settings() {
		
		$this->SetTitle( "Facebook Chat Settings" );
		$this->SetPOPUPIconClass( "ap ap-shield" );
		$this->SetSubtitle( "Messenger Settings" );
		$this->Display();
	}

	function webchat_settings() {
		
		$this->SetTitle( "Chat Settings" );
		$this->SetPOPUPIconClass( "ap ap-msg" );
		$this->SetSubtitle( "Web Chat Settings" );
		$this->Display();
	}

	function ganalytics() {
		
		$this->SetTitle( "Chat Settings" );
		$this->SetPOPUPIconClass( "ap ap-msg" );
		$this->SetSubtitle( "Web Chat Settings" );
		$this->Display();
	}

	function change_admin_color( $color = '' ) {
		$this->output->unset_template();
		if (ACL::HasPermission( "admin/admin-setting/general" ) ) {
			if ( ! empty( $color ) ) {
				$colors = [ "_none", "default", "purple", "blue", "cyan", "green", "orange", "pink", "red", "gray", "black" ];
				if ( in_array( $color, $colors ) ) {
					Mapp_setting::UpdateSettings( "admin_color", $color );
				}
			}
			$admin_color = Mapp_setting::GetSettingsValue( 'admin_color', 'default' );
			if ( $this->input->is_ajax_request() ) {
				echo css_url( 'theme/admin/css/color/' . $admin_color . '.css' );
				die;
			} else {
				$rd=GetValue('_rd',admin_url('admin/admin-setting/general'));
				redirect( $rd );
			}
		}
		header("HTTP/1.1 401 Unauthorized");
		echo "Permission Denied or logged out.";
		exit;
	}
}