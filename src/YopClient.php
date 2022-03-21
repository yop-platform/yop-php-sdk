<?php

namespace Yeepay\Yop\Sdk\V1;

use Yeepay\Yop\Sdk\V1\Util\HttpRequest;
use Yeepay\Yop\Sdk\V1\Util\YopSignUtils;

class YopClient
{

    public function __construct()
    {

    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    static public function get($methodOrUri, $YopRequest)
    {
        $content = self::getForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function getForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "GET";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $serverUrl .= (strpos($serverUrl, '?') === false ? '?' : '&') . $YopRequest->toQueryString();
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function post($methodOrUri, $YopRequest)
    {
        $content = self::postForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function postForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function upload($methodOrUri, $YopRequest)
    {
        $content = self::uploadForString($methodOrUri, $YopRequest);
        $response = self::handleResult($YopRequest, $content);
        return $response;
    }

    static public function uploadForString($methodOrUri, $YopRequest)
    {
        $YopRequest->httpMethod = "POST";
        $serverUrl = self::richRequest($methodOrUri, $YopRequest);

        self::signAndEncrypt($YopRequest);
        $response = HttpRequest::curl_request($serverUrl, $YopRequest);
        return $response;
    }

    static public function signAndEncrypt($YopRequest)
    {
        if (empty($YopRequest->method)) {
            error_log("method must be specified");
        }
        if (empty($YopRequest->secretKey)) {
            error_log("secretKey must be specified");
        }
        $appKey = $YopRequest->appKey;
        if (empty($appKey)) {
            $appKey = YopConfig::$appKey;
        }
        if (empty($appKey)) {
            error_log("appKey 不能为空");
        }

        $toSignParamMap = array_merge($YopRequest->paramMap, array("v" => $YopRequest->version, "method" => $YopRequest->method));
        $signValue = YopSignUtils::sign($toSignParamMap, $YopRequest->ignoreSignParams, $YopRequest->secretKey, $YopRequest->signAlg);

        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());

        $headers = array();
        $headers['x-yop-appkey'] = $appKey;
        $headers['x-yop-date'] = $timestamp;
        $headers['Authorization'] = "YOP-HMAC-AES128 " . $signValue;

        $YopRequest->headers = $headers;
    }

    static public function richRequest($methodOrUri, $YopRequest)
    {
        if (strpos($methodOrUri, YopConfig::$serverRoot)) {
            $methodOrUri = substr($methodOrUri, strlen(YopConfig::$serverRoot) + 1);
        }
        $serverUrl = $YopRequest->serverRoot;
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

    static public function handleResult($YopRequest, $content)
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

            if ($response->validSign == 1) {
                echo "<br><br>" . "返回结果签名验证成功!";
            }
        }

        return $response;
    }

}
