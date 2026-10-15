<?php
require_once "AI_Studio_Content.php";
require_once "Ai_Studio_Part.php";
/**
 * Google_AI_Studio
 *
 * @author: appsbd
 *
 */
class Google_AI_Studio {
	protected $api_key;
	protected $model;
	/**
	 * Its property Content
	 *
	 * @var AI_Studio_Content
	 */
	protected $request_data;
	/**
	 * Its property is_enabled
	 *
	 * @var mixed|null
	 */
	protected $is_enabled;
	public function __construct() {
		$group='ai_studio';
		$this->is_enabled=Mapp_settings_api_advance::GetSettingsValue($group,'ai_is_active','N')=='Y';
		$this->api_key=Mapp_settings_api_advance::GetSettingsValue($group,'ai_api_key');
		$this->model=Mapp_settings_api_advance::GetSettingsValue($group,'ai_model');
		$this->request_data=new stdClass();
		$this->request_data->contents=[];

	}
	 public function add_part( $part_text ,$role='user') {
		 $content=new AI_Studio_Content();
		 $content->add_part($part_text);
		 $this->request_data->contents[] =$content;
	 }
	protected function get_url() {
		if(empty($this->api_key) || empty($this->model)){
			throw new Exception("API key Or Model is invalid");
		}
		return "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->api_key}";

	}
	function get_row_response() {
		$response = $this->_request();

		return json_decode( $response );
	}
	function get_text_response() {
		$response     = $this->get_row_response();
		$respons_text = "";
		if ( ! empty( $response->candidates ) && is_array( $response->candidates ) ) {
			foreach ( $response->candidates as $candidate ) {
				if ( ! empty( $candidate->content->parts && is_array( $candidate->content->parts ) ) ) {
					foreach ( $candidate->content->parts as $part ) {
						if ( ! empty( $part->text ) ) {
							$respons_text .= '' . ( $part->text );
						}
					}

				}
			}
		}

		return $respons_text;
	}
	protected function _request() {
		if(!$this->is_enabled){
			throw new Exception( "Gimini is not enabled" );
		}
		if ( empty( $this->api_key ) ) {
			throw new Exception( "Gimini API key is invalid" );
		}
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $this->get_url(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => ENVIRONMENT=='production',
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => json_encode($this->request_data),
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json'
			),
		) );

		$response = curl_exec( $curl );
		$error = curl_error($curl );
		if(!empty($error)){
			throw new Exception($error);
		}
		curl_close( $curl );
		return $response;

	}
}