<?php

namespace Yeepay\Yop\Sdk\V1;

use Yeepay\Yop\Sdk\V1\Util\Base64Url;
use Yeepay\Yop\Sdk\V1\Util\HttpRequest;
use Yeepay\Yop\Sdk\V1\Util\HttpUtils;

class YopRsaClient
{

    public function __construct()
    {

    }

    /**
     * @param $methodOrUri
     * @param $YopRequest YopRequest
     */
    public static function SignRsaParameter($methodOrUri, $YopRequest)
    {
        $appKey = $YopRequest->appKey;
        if (empty($appKey)) {
            $appKey = YopConfig::$appKey;
        }
        if (empty($appKey)) {
            error_log("appKey 不能为空");
        }

        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());;
        $headers = array();

        $headers['x-yop-appkey'] = $appKey;
        $headers['x-yop-request-id'] = $YopRequest->requestId;

        $protocolVersion = "yop-auth-v2";
        $EXPIRED_SECONDS = "1800";

        $authString = $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS;

        $headersToSignSet = array();
        array_push($headersToSignSet, "x-yop-request-id");

        // Formatting the URL with signing protocol.
        $canonicalURI = HttpUtils::getCanonicalURIPath($methodOrUri);

        // Formatting the query string with signing protocol.
        $canonicalQueryString = YopRsaClient::getCanonicalQueryString($YopRequest, true);

        // Sorted the headers should be signed from the request.
        $headersToSign = YopRsaClient::getHeadersToSign($headers, $headersToSignSet);

        // Formatting the headers from the request based on signing protocol.
        $canonicalHeader = YopRsaClient::getCanonicalHeaders($headersToSign);

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

        openssl_sign($canonicalRequest, $encode_data, $privateKey, "SHA256");
        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);
        $signToBase64 .= '$SHA256';

        $headers['Authorization'] = "YOP-RSA2048-SHA256 " . $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS . "/" . $signedHeaders . "/" . $signToBase64;

        if (YopConfig::$debug) {
            var_dump("authString=" . $authString);
            var_dump("canonicalURI=" . $canonicalURI);
            var_dump("canonicalQueryString=" . $canonicalQueryString);
            var_dump("canonicalHeader=" . $canonicalHeader);
            var_dump("canonicalRequest=" . $canonicalRequest);
            var_dump("signToBase64=" . $signToBase64);
        }
        $YopRequest->headers = $headers;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function get($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::getForString($methodOrUri, $YopRequest);
        $response = YopRsaClient::handleRsaResult($YopRequest, $content);
        return $response;
    }

    public static function getForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "GET";
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);
        $serverUrl .= (strpos($serverUrl, '?') === false ? '?' : '&') . $YopRequest->toQueryString();

        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    public static function post($methodOrUri, $YopRequest)
    {
        $content = YopRsaClient::postString($methodOrUri, $YopRequest);
        $response = (new YopRsaClient)->handleRsaResult($YopRequest, $content);
        return $response;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return bool|string
     */
    public static function postString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = YopRsaClient::richRequest($methodOrUri, $YopRequest);

        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
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
     * @return array
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
                if (($headersToSign == null && YopRsaClient::isDefaultHeaderToSign($key)) || ($headersToSign != null && in_array(strtolower($key), $headersToSign) && $key != "Authorization")) {
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
    public static function isDefaultHeaderToSign($header): bool
    {
        $header = strtolower(trim($header));
        $defaultHeadersToSign = array();
        array_push($defaultHeadersToSign, "host");
        array_push($defaultHeadersToSign, "content-type");

        return strpos($header, "x-yop-") == 0 || in_array($header, $defaultHeadersToSign);
    }

    /**
     * @param $headers
     * @return string
     */
    public static function getCanonicalHeaders($headers): string
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
        $strQuery = "";

        foreach ($headerStrings as $kv) {
            $strQuery .= strlen($strQuery) == 0 ? "" : "\n";
            $strQuery .= $kv;
        }

        return $strQuery;
    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return YopResponse
     */
    public static function upload($methodOrUri, $YopRequest): YopResponse
    {
        $content = self::uploadForString($methodOrUri, $YopRequest);
        $response = self::handleRsaResult($YopRequest, $content);
        return $response;
    }

    public static function uploadForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);
        self::SignRsaParameter($methodOrUri, $YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    public static function richRequest($methodOrUri, $YopRequest): string
    {
        if (strpos($methodOrUri, YopConfig::$serverRoot)) {
            $methodOrUri = substr($methodOrUri, strlen(YopConfig::$serverRoot) + 1);
        }
        $serverUrl = $YopRequest->serverRoot;
        //判定是否是yos请求，当前只判断是否是文件上传，后续需要补充判断文件下载
        $yosRequest = !empty($YopRequest->fileMap);
        if ($yosRequest && strcmp($serverUrl, YopConfig::$serverRoot) === 0) {
            $serverUrl = YopConfig::$yosServerRoot;
        }
        $serverUrl .= $methodOrUri;
        preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
        if (!empty($version)) {
            $version = $version[1];
            if (!empty($version)) {
                $YopRequest->setVersion($version);
            }
        }
        $YopRequest->setMethod($methodOrUri);
        return $serverUrl;
    }

    public static function handleRsaResult($YopRequest, $content)
    {
        if (isset($YopRequest->downRequest) && $YopRequest->downRequest) {
            return $content;
        }

        $response = new YopResponse();
        $jsoncontent = json_decode($content);
        $response->requestId = $YopRequest->requestId;
        if (!empty($jsoncontent->result)) {
            $response->state = "SUCCESS";
            $response->result = $jsoncontent->result;
        } else {
            $response->state = "FAILURE";
            $response->error = new YopError();
            $response->error->code = $jsoncontent->code;
            $response->error->message = $jsoncontent->message;
            $response->error->subCode = $jsoncontent->subCode;
            $response->error->subMessage = $jsoncontent->subMessage;
        }

        if (YopConfig::$debug) {
            print_r($response);
        }

//        if (!empty($response->sign)) {
//            $response->validSign = YopRsaClient::isValidRsaResult($jsoncontent->result, $jsoncontent->sign, $YopRequest->yopPublicKey);
//        } else {
        //3.2.7之前返回结果没有签名，3.2.7之后有签名，具体签名策略请参照网关
        $response->validSign = "1";
//        }
        return $response;
    }


//    public static function isValidRsaResult($result, $sign, $public_key)
//    {
//        $result = json_encode($result, 320);
//        $sb = "";
//        if ($result == null || empty($result)) {
//            $sb = "";
//        } else {
//            $sb .= trim($result);
//        }
//
//        $public_key = "-----BEGIN PUBLIC KEY-----\n" .
//            wordwrap($public_key, 64, "\n", true) .
//            "\n-----END PUBLIC KEY-----";
//        $pub_key = openssl_pkey_get_public($public_key);
//        $sb = preg_replace("/[\s]{2,}/", "", $sb);
//        $sb = str_replace(PHP_EOL, "", $sb);
//        $sb = str_replace(" ", "", $sb);
//        $res = openssl_verify($sb, Base64Url::decode(substr($sign, 0, -7)), $pub_key, "SHA256"); //验证
//        openssl_free_key($pub_key);
//        if ($res == 1) {
//            return true;
//        } else {
//            return false;
//        }
//}
}
