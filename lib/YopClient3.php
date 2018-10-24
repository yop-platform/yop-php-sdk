<?php

require_once("YopRequest.php");
require_once("YopResponse.php");
require_once("Util/YopSignUtils.php");
require_once("Util/HttpRequest.php");
require_once("Util/StringUtils.php");
require_once("Util/HttpUtils.php");
require_once("Util/Base64Url.php");

class YopClient3
{

    public function __construct()
    {

    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @param $encode_data
     * @return array
     */
    public static function SignRsaParameter($methodOrUri, $YopRequest)
    {
        $appKey = $YopRequest->{$YopRequest->Config->APP_KEY};
        if (empty($appKey)) {
            $appKey = $YopRequest->Config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->Config->APP_KEY);
        }
        if (empty($appKey)) {
            error_log("appKey 与 customerNo 不能同时为空");
        }

        date_default_timezone_set('PRC');
        $dataTime = new DateTime();
        $timestamp = $dataTime->format(DateTime::ISO8601); // Works the same since const ISO8601 = "Y-m-d\TH:i:sO"

        $headers = array();

        $headers['x-yop-request-id'] = $YopRequest->requestId;
        $headers['x-yop-date'] = $timestamp;

        $protocolVersion = "yop-auth-v2";
        $EXPIRED_SECONDS = "1800";

        $authString = $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS;

        $headersToSignSet = array();
        array_push($headersToSignSet, "x-yop-request-id");
        array_push($headersToSignSet, "x-yop-date");

        $appKey = $YopRequest->{$YopRequest->Config->APP_KEY};

        if (StringUtils::isBlank($YopRequest->Config->CUSTOMER_NO)) {
            $headers['x-yop-appkey'] = $appKey;
            array_push($headersToSignSet, "x-yop-appkey");
        } else {
            $headers['x-yop-customerid'] = $appKey;
            array_push($headersToSignSet, "x-yop-customerid");
        }

        // Formatting the URL with signing protocol.
        $canonicalURI = HttpUtils::getCanonicalURIPath($methodOrUri);

        // Formatting the query string with signing protocol.
        $canonicalQueryString = YopClient3::getCanonicalQueryString($YopRequest, true);

        // Sorted the headers should be signed from the request.
        $headersToSign = YopClient3::getHeadersToSign($headers, $headersToSignSet);

        // Formatting the headers from the request based on signing protocol.
        $canonicalHeader = YopClient3::getCanonicalHeaders($headersToSign);

        $signedHeaders = "";
        if ($headersToSignSet != null) {
            foreach ($headersToSign as $key => $value) {
                $signedHeaders .= strlen($signedHeaders) == 0 ? "" : ";";
                $signedHeaders .= $key;
            }
            $signedHeaders = strtolower($signedHeaders);
        }

        $canonicalRequest = $authString . "\n" . $YopRequest->httpMethod . "\n" . $canonicalURI . "\n" . $canonicalQueryString . "\n" . $canonicalHeader;

        // Signing the canonical request using key with sha-256 algorithm.

        if (empty($YopRequest->secretKey)) {
            error_log("secretKey must be specified");
        }

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        $private_key = $YopRequest->secretKey;
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        $privateKey = openssl_pkey_get_private($private_key);// 提取私钥
        ($privateKey) or die('密钥不可用');

        $signToBase64 = "";

        openssl_sign($canonicalRequest, $encode_data, $privateKey, "SHA256");

        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);

        $signToBase64 .= '$SHA256';

        $headers['Authorization'] = "YOP-RSA2048-SHA256 " . $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS . "/" . $signedHeaders . "/" . $signToBase64;

        return $headers;
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name = $value;

    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name;
    }

    static public function get($methodOrUri, $YopRequest){
        $content = YopClient3::getForString($methodOrUri, $YopRequest);
        $response = YopClient3::handleRsaResult($YopRequest, $content);
        return $response;
    }

    static public function getForString($methodOrUri, $YopRequest){
        $YopRequest->httpMethod = "GET";
        $serverUrl = YopClient3::richRequest($methodOrUri, $YopRequest);
        YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $YopRequest->encoding();
        $serverUrl .= (strpos($serverUrl,'?') === false ?'?':'&') . $YopRequest->toQueryString();
        $response = YopClient3::getRestTemplate($serverUrl,$YopRequest,"GET");
        return $response;
    }

    public static function post($methodOrUri, $YopRequest)
    {
        $content = YopClient3::postString($methodOrUri, $YopRequest);
        $response = YopClient3::handleRsaResult($YopRequest, $content);
        return $response;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return type
     */
    public static function postString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = YopClient3::richRequest($methodOrUri, $YopRequest);
        $YopRequest->absoluteURL = $serverUrl;

        $headers = self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = YopClient3::getRestTemplate($serverUrl, $YopRequest, "POST", $headers);
        return $response;
    }

    /**
     * @param $YopRequest
     * @param $forSignature
     * @return string
     */
    public static function getCanonicalQueryString($YopRequest, $forSignature)
    {
        if (!empty($YopRequest->jsonParam)) {
            return "";
        }

        $ArrayList = array();
        $StrQuery = "";
        foreach ($YopRequest->paramMap as $k => $v) {
            if ($forSignature && strcasecmp($k, "Authorization") == 0) {
                continue;
            }
            array_push($ArrayList, $k . "=" . rawurlencode($v));
        }
        sort($ArrayList);

        foreach ($ArrayList as $kv) {
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "&";
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $headers
     * @param $headersToSign
     * @return arry
     */
    public static function getHeadersToSign($headers, $headersToSign)
    {
        $ret = array();
        if ($headersToSign != null) {
            $tempSet = array();
            foreach ($headersToSign as $header) {
                array_push($tempSet, strtolower(trim($header)));
            }

            $headersToSign = $tempSet;
        }

        foreach ($headers as $key => $value) {
            if ($value != null && !empty($value)) {
                if (($headersToSign == null && isDefaultHeaderToSign($key)) || ($headersToSign != null && in_array(strtolower($key), $headersToSign) && $key != "Authorization")) {
                    $ret[$key] = $value;
                }

            }
        }
        ksort($ret);
        return $ret;
    }

    /**
     * @param $header
     * @return bool
     */
    public static function isDefaultHeaderToSign($header)
    {
        $header = strtolower(trim($header));
        $defaultHeadersToSign = array();
        array_push($defaultHeadersToSign, "host");
        array_push($defaultHeadersToSign, "content-length");
        array_push($defaultHeadersToSign, "content-type");
        array_push($defaultHeadersToSign, "content-md5");

        return strpos($header, "x-yop-") == 0 || in_array($defaultHeadersToSign, $header);
    }

    /**
     * @param $headers
     * @return string
     */
    public static function getCanonicalHeaders($headers)
    {
        if (empty($headers)) {
            return "";
        }

        $headerStrings = array();

        foreach ($headers as $key => $value) {
            if ($key == null) {
                continue;
            }
            if ($value == null) {
                $value = "";
            }
            $key = HttpUtils::normalize(strtolower(trim($key)));
            $value = HttpUtils::normalize(trim($value));
            array_push($headerStrings, $key . ':' . $value);
        }

        sort($headerStrings);
        $StrQuery = "";

        foreach ($headerStrings as $kv) {
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "\n";
            $StrQuery .= $kv;
        }

        return $StrQuery;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return YopResponse
     */
    static public function upload($methodOrUri, $YopRequest)
    {
        $content = YopClient3::uploadForString($methodOrUri, $YopRequest);
        $response = YopClient3::handleRsaResult($YopRequest, $content);
        return $response;
    }

    static public function uploadForString($methodOrUri, $YopRequest)
    {
        $serverUrl = YopClient3::richRequest($methodOrUri, $YopRequest);

        $strTemp = $YopRequest->getParam("_file");
        $YopRequest->removeParam("_file");

        $headers = self::SignRsaParameter($methodOrUri, $YopRequest);

        $YopRequest->addParam("_file",$strTemp);

        $YopRequest->absoluteURL = $serverUrl;

        $response = YopClient3::getRestTemplate($serverUrl, $YopRequest, "PUT", $headers);
        return $response;
    }

    static public function getRestTemplate($serverUrl, $YopRequest, $method, $headers)
    {
        $YopRequest->encoding();

        if ($method == "GET") {
            return HttpRequest::curl_request($serverUrl, '', $YopRequest->Config->connectTimeout, true, $headers);
        } elseif ($method == "PUT") {
            //$YopRequest->addParam("_file", $YopRequest->ImagePath);
            return HttpRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, true, true, $headers);
        }

        return HttpRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, false, false, $headers, $YopRequest->jsonParam);
    }

    static public function signAndEncrypt($YopRequest)
    {
        if (empty($YopRequest->method)) {
            error_log("method must be specified");
        }
        if (empty($YopRequest->secretKey)) {
            error_log("secretKey must be specified");
        }
        $appKey = $YopRequest->{$YopRequest->Config->APP_KEY};
        if (empty($appKey)) {
            $appKey = $YopRequest->Config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->Config->APP_KEY);
        }
        if (empty($appKey)) {
            error_log("appKey 与 customerNo 不能同时为空");
        }
        $signValue = YopSignUtils::sign($YopRequest->paramMap, $YopRequest->ignoreSignParams, $YopRequest->secretKey, $YopRequest->signAlg);

        $YopRequest->addParam($YopRequest->Config->SIGN, $signValue);
        if ($YopRequest->isRest) {
            $YopRequest->removeParam($YopRequest->Config->METHOD);
            $YopRequest->removeParam($YopRequest->Config->VERSION);
        }
    }

    static public function richRequest($methodOrUri, $YopRequest)
    {
        if (strpos($methodOrUri, $YopRequest->Config->serverRoot)) {
            $methodOrUri = substr($methodOrUri, strlen($YopRequest->Config->serverRoot) + 1);
        }
        $isRest = (strpos($methodOrUri, "/rest/") == 0) ? true : false;
        $YopRequest->isRest = $isRest;
        $serverUrl = $YopRequest->serverRoot;
        if ($isRest) {
            $serverUrl .= $methodOrUri;
            preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
            if (!empty($version)) {
                $version = $version[1];
                if (!empty($version)) {
                    $YopRequest->setVersion($version);
                }
            }
        } else {
            $serverUrl .= "/command?" . $YopRequest->Config->METHOD . "=" . $methodOrUri;
        }
        $YopRequest->setMethod($methodOrUri);
        return $serverUrl;
    }

    public function handleRsaResult($YopRequest, $content)
    {
        $response = new YopResponse();

        $jsoncontent = json_decode($content);
        $response->state = $jsoncontent->state;
        $response->result = $jsoncontent->result;
        $response->ts = $jsoncontent->ts;
        $response->sign = $jsoncontent->sign;
        $response->requestId = $YopRequest->requestId;

        if(!empty($jsoncontent->error)){
            if(is_array($jsoncontent->error)){
                foreach ($jsoncontent->error as $k => $v) {
                    if(!is_array($v)){
                        $response->error .= (empty($response->error)?'':',') . '"'. $k .'" : "'.$v.'"';
                    } else {
                        $response->error .= (empty($response->error)?'':',') . '"'. $k .'" : "'.json_encode($v,JSON_UNESCAPED_UNICODE).'"';
                    }
                }
            } else {
                $response->error = $jsoncontent->error;
            }
        }

        return $response;
    }

}
