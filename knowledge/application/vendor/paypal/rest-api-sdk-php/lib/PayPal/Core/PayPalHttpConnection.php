<?php

namespace PayPal\Core;

use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;

/**
 * A wrapper class based on the curl extension.
 * Requires the PHP curl module to be enabled.
 * See for full requirements the PHP manual: http://php.net/curl
 */
class PayPalHttpConnection
{

    /**
     * @var PayPalHttpConfig
     */
    private $httpConfig;

    /**
     * LoggingManager
     *
     * @var PayPalLoggingManager
     */
    private $logger;

    /**
     * @var array
     */
    private $responseHeaders = array();

    /**
     * @var bool
     */
    private $skippedHttpStatusLine = false;

    /**
     * Default Constructor
     *
     * @param PayPalHttpConfig $httpConfig
     * @param array            $config
     * @throws PayPalConfigurationException
     */
    public function __construct(PayPalHttpConfig $httpConfig, array $config)
    {
        if (!function_exists("curl_init")) {
            throw new PayPalConfigurationException("Curl module is not available on this system");
        }
        $this->httpConfig = $httpConfig;
        $this->logger = PayPalLoggingManager::getInstance(__CLASS__);
    }

    /**
     * Gets all Http Headers
     *
     * @return array
     */
    private function getHttpHeaders()
    {
        $ret = array();
        foreach ($this->httpConfig->getHeaders() as $k => $v) {
            $ret[] = "$k: $v";
        }
        return $ret;
    }

    /**
     * Parses the response headers for debugging.
     *
     * @param resource $ch
     * @param string $data
     * @return int
     */
    protected function parseResponseHeaders($ch, $data) {
        if (!$this->skippedHttpStatusLine) {
            $this->skippedHttpStatusLine = true;
            return strlen($data);
        }

        $trimmedData =app_trim($data);
        if (strlen($trimmedData) == 0) {
            return strlen($data);
        }

        list($key, $value) = explode(":", $trimmedData, 2);

        $key =app_trim($key);
        $value =app_trim($value);

                        if (strlen($key) > 0 && strlen($value) > 0) {
                                                $this->responseHeaders[$key] = $value;
        }

        return strlen($data);
    }


    /**
     * Implodes a key/value array for printing.
     *
     * @param array $arr
     * @return string
     */
    protected function implodeArray($arr) {
        $retStr = '';
        foreach($arr as $key => $value) {
            $retStr .= $key . ': ' . $value . ', ';
        }
        rtrim($retStr, ', ');
        return $retStr;
    }

    /**
     * Executes an HTTP request
     *
     * @param string $data query string OR POST content as a string
     * @return mixed
     * @throws PayPalConnectionException
     */
    public function execute($data)
    {
                $this->logger->info($this->httpConfig->getMethod() . ' ' . $this->httpConfig->getUrl());

                $ch = curl_init($this->httpConfig->getUrl());
        $options = $this->httpConfig->getCurlOptions();
        if (empty($options[CURLOPT_HTTPHEADER])) {
            unset($options[CURLOPT_HTTPHEADER]);
        }
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_URL, $this->httpConfig->getUrl());
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHttpHeaders());

                switch ($this->httpConfig->getMethod()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
        }

                if ($this->httpConfig->getMethod() != null) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpConfig->getMethod());
        }

        $this->responseHeaders = array();
        $this->skippedHttpStatusLine = false;
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'parseResponseHeaders'));

                $result = curl_exec($ch);
                $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if (curl_errno($ch) == 60) {
            $this->logger->info("Invalid or no certificate authority found - Retrying using bundled CA certs file");
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            $result = curl_exec($ch);
                        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

                if (curl_errno($ch)) {
            $ex = new PayPalConnectionException(
                $this->httpConfig->getUrl(),
                curl_error($ch),
                curl_errno($ch)
            );
            curl_close($ch);
            throw $ex;
        }

                $requestHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $this->logger->debug("Request Headers \t: " . str_replace("\r\n", ", ", $requestHeaders));
        $this->logger->debug(($data && $data != '' ? "Request Data\t\t: " . $data : "No Request Payload") . "\n" . str_repeat('-', 128) . "\n");
        $this->logger->info("Response Status \t: " . $httpStatus);
        $this->logger->debug("Response Headers\t: " . $this->implodeArray($this->responseHeaders));

                curl_close($ch);

                if ($httpStatus < 200 || $httpStatus >= 300) {
            $ex = new PayPalConnectionException(
                $this->httpConfig->getUrl(),
                "Got Http response code $httpStatus when accessing {$this->httpConfig->getUrl()}.",
                $httpStatus
            );
            $ex->setData($result);
            $this->logger->error("Got Http response code $httpStatus when accessing {$this->httpConfig->getUrl()}. " . $result);
            $this->logger->debug("\n\n" . str_repeat('=', 128) . "\n");
            throw $ex;
        }

        $this->logger->debug(($result && $result != '' ? "Response Data \t: " . $result : "No Response Body") . "\n\n" . str_repeat('=', 128) . "\n");

                return $result;
    }
}
