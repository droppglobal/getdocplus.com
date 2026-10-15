<?php
	/**
	 * @since: 20/07/2020
	 * @author: Sarwar Hasan
	 * @version 1.0.0
	 */
	use ScssPhp\ScssPhp\Compiler;
	
	
	class ScssCompiler {
		
		static function ProcessClientColor( $is_comporess = true ) {
			try {
				$options_vars = array (
					'app_main_color' => Mapp_setting::GetSettingsValue ( "app_main_color", "#0b8ec2" ) ,
					'app_header_bg'=>Mapp_setting::GetSettingsValue("app_header_bg","#FFFFFF")
				);
				
				if(Mapp_setting::GetSettingsValue("app_c_auto","N")=="N"){
					$options_vars['app_navbar_bg']=Mapp_setting::GetSettingsValue("app_navbar_bg","");
					$options_vars['app_navbar_menu_acive_text_color']=Mapp_setting::GetSettingsValue("app_nav_acive_text","");
					$options_vars['footer_bg_color']=Mapp_setting::GetSettingsValue("footer_bg_color","");
					$options_vars['footer_text_color']=Mapp_setting::GetSettingsValue("footer_text_color","");
					$options_vars['app_text_color']=Mapp_setting::GetSettingsValue("app_text_color","");
					$options_vars['app_welcome_bg']=Mapp_setting::GetSettingsValue("app_welcome_bg","");
					$options_vars['app_welcome_text']=Mapp_setting::GetSettingsValue("app_welcome_text","");
					
				}
				$options_vars=array_filter($options_vars,function($val){
					if(empty($val)){
						return false;
					}
					return true;
				});
				if(!empty($options_vars['app_header_bg'])){
					if(strtolower($options_vars['app_header_bg'])!="#fff" && strtolower($options_vars['app_header_bg'])!="#ffffff"){
						$options_vars['app_header_text_color']="#ffffff";
						$options_vars['app_header_text_icon']=$options_vars['app_header_bg'];
					}else{
						$options_vars['app_header_text_color']="#6e6e6e";
						$options_vars['app_header_text_icon']="#ffffff";
					}
				}
				AddOnManager::CallHookRef('process-style-variable',$options_vars);
			} catch ( Exception $e ) {
				echo  $e->getMessage();
			}
		}

		static function ProcessChatColor( $is_comporess = true ) {
			try {
				$bgptrn=Mapp_setting_api::GetSettingsValue ( "webchat","chat_bg_pattern", "chat-bg5.png");
				$options_vars = array (
					'main_icon_bg' => Mapp_setting_api::GetSettingsValue ( "webchat","chat_main_color", "rgb(11, 193, 255)"),
					'chat_header_color' => Mapp_setting_api::GetSettingsValue ( "webchat","chat_header_color", "#000"),
					'bg_img'=>'"../img/'.$bgptrn.'"'
				);
				return self::process_scss('plugins/apsbd-chat/css/appsbd-chat.scss','plugins/apsbd-chat/css/appsbd-chat.css',$options_vars);

			} catch ( Exception $e ) {
				return false;
			}
		}

		public static function  process_scss($from,$to,$options_vars,$import_path=''){
			try {

				$scss         = new ScssPhp\ScssPhp\Compiler();
				if(!empty($import_path)) {
					$scss->addImportPath( FCPATH . $import_path );
				}
				$scss->addVariables($options_vars);
				$cssStr=file_get_contents( FCPATH . $from);
				$result = $scss->compileString( $cssStr);
				$css = $result->getCss();
				file_put_contents(  FCPATH.$to, $css );
				return true;
			} catch ( Exception $e ) {
				return false;
			}
		}
		
		
	}