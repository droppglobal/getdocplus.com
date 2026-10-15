<?php
/**
 * Google_AI_Studio
 *
 * @author: appsbd
 *
 */
class ChatGPT {
	protected $api_key;
	protected $model;

	protected $request_data;
	/**
	 * Its property is_enabled
	 *
	 * @var mixed|null
	 */
	protected $is_enabled;

	public function __construct() {
		$group                        = 'ai_studio';
		$this->is_enabled                = Mapp_settings_api_advance::GetSettingsValue( $group, 'gpt_is_active' ,'N')=='Y';
		$this->api_key                = Mapp_settings_api_advance::GetSettingsValue( $group, 'gpt_api_key' ,'');
		$this->request_data           = new stdClass();
		$this->request_data->model =Mapp_settings_api_advance::GetSettingsValue( $group, 'gpt_ai_model' ,'gpt-4o-mini');
		$this->request_data->messages = [];

	}

	public function add_part( $part_text, $role = 'user' ) {
		$part=new stdClass();
		$part->role=$role;
		$part->content=$part_text;
		file_put_contents(APPPATH."/logs/ailog2.txt",$part_text."\n",FILE_APPEND);
		$this->request_data->messages[] =$part;
	}

	protected function get_url() {
		if ( empty( $this->api_key )) {
			throw new Exception( "API key is invalid" );
		}

		return "https://api.openai.com/v1/chat/completions";

	}

	function get_row_response() {
		$response = $this->_request();

		return json_decode( $response );
	}

	function get_text_response() {
		$response     = $this->get_row_response();
		$respons_text = "";
		if ( ! empty( $response->error->message ) ) {
			throw new Exception( $response->error->message );
		}
		if ( ! empty( $response->choices ) && is_array( $response->choices ) ) {
			foreach ( $response->choices as $candidate ) {
				if ( ! empty( $candidate->message->content ) ) {
					$respons_text .= ' ' . $candidate->message->content;
				}
			}
		}

		return $respons_text;
	}

	protected function _request() {
		if(!$this->is_enabled){
			throw new Exception( "ChatGPT is not enabled" );
		}
		if ( empty( $this->api_key ) ) {
			throw new Exception( "ChatGPT API key is invalid" );
		}

		$curl = curl_init();
		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $this->get_url(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => ENVIRONMENT == 'production',
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => json_encode( $this->request_data ),
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'Authorization: Bearer '.$this->api_key,
			),
		) );

		$response = curl_exec( $curl );
		$error    = curl_error( $curl );
		if ( ! empty( $error ) ) {
			throw new Exception( $error );
		}
		curl_close( $curl );

		return $response;

	}
}