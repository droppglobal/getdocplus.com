<?php



class EnvatoOAuth2Client extends OAuth2Client
{
    /**
     * Format and sign an oauth for provider api
     */
    public function api( $url, $method = "GET", $parameters = array(), $decode_json = true )
    {
        if ( strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0 ) {
            $url = $this->api_base_url . $url;
        }
        $parameters[$this->sign_token_name] = $this->access_token;
        $this->curl_header = array( 'Authorization: Bearer ' . $this->access_token );
        $response = null;

        switch( $method ){
            case 'GET'  : $response = $this->request( $url, $parameters, "GET"  ); break;
            case 'POST' : $response = $this->request( $url, $parameters, "POST" ); break;
        }
        if( $response){
            $response = json_decode( $response );
        }

        return $response;
    }
    private function request( $url, $params=false, $type="GET" )
    {
        Hybrid_Logger::info( "Enter OAuth2Client::request( $url )" );
        Hybrid_Logger::debug( "OAuth2Client::request(). dump request params: ", serialize( $params ) );

        $urlEncodedParams = http_build_query($params, '', '&');

        if( $type == "GET" ){
            $url = $url . ( strpos( $url, '?' ) ? '&' : '?' ) . $urlEncodedParams;
        }

        $this->http_info = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL            , $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt($ch, CURLOPT_TIMEOUT        , $this->curl_time_out );
        curl_setopt($ch, CURLOPT_USERAGENT      , $this->curl_useragent );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , $this->curl_connect_time_out );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , $this->curl_ssl_verifypeer );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , $this->curl_ssl_verifyhost );
        curl_setopt($ch, CURLOPT_HTTPHEADER     , $this->curl_header );

        if ($this->curl_compressed){
            curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        }

        if($this->curl_proxy){
            curl_setopt( $ch, CURLOPT_PROXY        , $this->curl_proxy);
        }

        if ($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);

                        if (isset($params['body'])) {
                $urlEncodedParams = json_encode($params['body']);
            }

                                                if ($params) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $urlEncodedParams);
            }
        }

        if( $type == "DELETE" ){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        if( $type == "PATCH" ){
            curl_setopt($ch, CURLOPT_POST, 1);
            if($params) curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        }
        $response = curl_exec($ch);
        if( $response === false ) {
            Hybrid_Logger::error( "OAuth2Client::request(). curl_exec error: ", curl_error($ch) );
        }
        Hybrid_Logger::debug( "OAuth2Client::request(). dump request info: ", serialize( curl_getinfo($ch) ) );
        Hybrid_Logger::debug( "OAuth2Client::request(). dump request result: ", serialize( $response ) );

        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ch));

        curl_close ($ch);

        return $response;
    }
}
