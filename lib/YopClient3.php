<?php

/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/13
 * Time: 16:29
 */

require_once("YopRequest.php");
require_once("YopResponse.php");
require_once("Util/YopSignUtils.php");
require_once("Util/HttpRequest.php");
require_once("Util/BlowfishEncrypter.php");
require_once("Util/AESEncrypter.php");
require_once("Util/StringUtils.php");
require_once("Util/HttpUtils.php");
require_once("Util/Base64Url.php");


class YopClient3
{

    public function __construct()
    {

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

    static public function get($methodOrUri, $YopRequest)
    {
        $content = YopClient3::getForString($methodOrUri, $YopRequest);
        $response = YopClient3::unmarshal($content);
        YopClient3::handleRsaResult($YopRequest, $response, $content);
        return $response;

    }

    static public function getForString($methodOrUri, $YopRequest)
    {
        $serverUrl = YopClient3::richRequest($methodOrUri, $YopRequest);
        YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $YopRequest->encoding();
        $serverUrl .= (strpos($serverUrl, '?') === false ? '?' : '&') . $YopRequest->toQueryString();
        $response = YopClient3::getRestTemplate($serverUrl, $YopRequest, "GET");
        return $response;
    }

    public static function post($methodOrUri, $YopRequest)
    {

        $content = YopClient3::postString($methodOrUri, $YopRequest);

        $response = YopClient3::unmarshal($content);


        YopClient3::handleRsaResult($YopRequest, $response, $content);
        return $response;

    }

    /**
     * @param $methodOrUri
     * @param $YopRequest
     * @return type
     */
    public static function postString($methodOrUri, $YopRequest)
    {


        $serverUrl = YopClient3::richRequest($methodOrUri, $YopRequest);


        $YopRequest->absoluteURL = $serverUrl;

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
        //$timestamp=$dataTime->format('c'); // Returns ISO8601 in proper format
        $timestamp = $dataTime->format(DateTime::ISO8601); // Works the same since const ISO8601 = "Y-m-d\TH:i:sO"
        //$timestamp = "2016-02-25T08:57:48Z";
        $requestId = YopClient3::uuid();//Returns like ‘1225c695-cfb8-4ebb-aaaa-80da344e8352′
        //$requestId="123456";

        $headers = array();

        $headers['x-yop-request-id'] = $requestId;
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

        $canonicalRequest = $authString . "\n" . "POST" . "\n" . $canonicalURI . "\n" . $canonicalQueryString . "\n" . $canonicalHeader;

        // Signing the canonical request using key with sha-256 algorithm.

        if (empty($YopRequest->secretKey)) {
            error_log("secretKey must be specified");
        }

        extension_loaded('openssl') or die('php需要openssl扩展支持');

        $private_key = $YopRequest->secretKey;

        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        /* 提取私钥 */
        $privateKey = openssl_pkey_get_private($private_key);

        ($privateKey) or die('密钥不可用');
        $signToBase64 = "";


        openssl_sign($canonicalRequest, $encode_data, $privateKey, "SHA256");

        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);

        $signToBase64 .= '$SHA256';


        $headers['Authorization'] = "YOP-RSA2048-SHA256 " . $protocolVersion . "/" . $appKey . "/" . $timestamp . "/" . $EXPIRED_SECONDS . "/" . $signedHeaders . "/" . $signToBase64;

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
        $content = YopClient::uploadForString($methodOrUri, $YopRequest);
        $response = YopClient::unmarshal($content);
        YopClient3::handleResult($YopRequest, $response, $content);
        return $response;
    }


    public static function unmarshal($content)
    {

        $jsoncontent = json_decode($content, true);

        /*
         *
            {
              "state" : "FAILURE",
              "ts" : 1469523373843,
              "error" : {
                "code" : "U000001",
                "message" : "会员不存在"
             }
         * */

        $YopResponse = new YopResponse();

        if (!empty($jsoncontent['state'])) {
            $YopResponse->state = $jsoncontent['state'];
        }
        if (!empty($jsoncontent['error'])) {
            if (is_array($jsoncontent['error'])) {
                foreach ($jsoncontent['error'] as $k => $v) {
                    if (!is_array($v)) {
                        $YopResponse->error .= (empty($YopResponse->error) ? '' : ',') . '"' . $k . '" : "' . $v . '"';
                    } else {
                        $YopResponse->error .= (empty($YopResponse->error) ? '' : ',') . '"' . $k . '" : "' . json_encode($v, JSON_UNESCAPED_UNICODE) . '"';
                        foreach ($v as $vk => $vv) {

                        }
                    }

                }

            } else {
                $YopResponse->error = $jsoncontent['error'];
            }

        }
        if (!empty($jsoncontent['result'])) {
            $YopResponse->result = $jsoncontent['result'];
        }
        if (!empty($jsoncontent['ts'])) {
            $YopResponse->ts = $jsoncontent['ts'];
        }
        if (!empty($jsoncontent['sign'])) {
            $YopResponse->sign = $jsoncontent['sign'];
        }
        if (!empty($jsoncontent['stringResult'])) {
            $YopResponse->stringResult = $jsoncontent['stringResult'];
        }
        if (!empty($jsoncontent['format'])) {
            $YopResponse->format = $jsoncontent['format'];
        }
        if (!empty($jsoncontent['validSign'])) {
            $YopResponse->validSign = $jsoncontent['validSign'];
        }

        return $YopResponse;
    }

    static public function getRestTemplate($serverUrl, $YopRequest, $method, $headers)
    {
        $YopRequest->encoding();
        //$YopRequest->addParam("_file", $YopRequest->ImagePath);
        if ($method == "GET") {
            return HttpRequest::curl_request($serverUrl, '', $YopRequest->Config->connectTimeout, true);
        } elseif ($method == "PUT") {

            return HttpRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, true, true);
        }


        return HttpRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, false, true, $headers);
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
        if ($YopRequest->encrypt) {
            YopClient::encrypt($YopRequest);
        }


    }

    static public function encrypt($YopRequest)
    {
        $builder = $YopRequest->paramMap;
        foreach ($builder as $k => $v) {
            if ($YopRequest->Config->ispublicedKey($k)) {
                unset($builder[$k]);
            } else {
                $YopRequest->removeParam($k);
            }
        }
        if (!empty($builder)) {
            $encryptBody = "";
            foreach ($builder as $k => $v) {
                $encryptBody .= strlen($encryptBody) == 0 ? "" : "&";
                $encryptBody .= $k . "=" . urlencode($v);
            }
        }
        if (empty($encryptBody)) {
            $YopRequest->addParam($YopRequest->Config->ENCRYPT, true);

        } else {
            if (!empty($YopRequest->{$YopRequest->Config->APP_KEY})) {

                $encrypt = AESEncrypter::encode($encryptBody, $YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT, $encrypt);
            } else {
                $encrypt = BlowfishEncrypter::encode($encryptBody, $YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT, $encrypt);
            }

        }

    }

    static public function decrypt($YopRequest, $strResult)
    {
        if (!empty($strResult) && $YopRequest->{$YopRequest->Config->ENCRYPT}) {
            if (!empty($YopRequest->{$YopRequest->Config->APP_KEY})) {
                $strResult = AESEncrypter::decode($strResult, $YopRequest->secretKey);
            } else {
                $strResult = BlowfishEncrypter::decode($strResult, $YopRequest->secretKey);
            }
        }
        return $strResult;
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

    public function handleResult($YopRequest, $YopResponse, $content)
    {
        $YopResponse->format = $YopRequest->format;
        $ziped = '';
        if (strtoupper($YopResponse->state) == 'SUCCESS') {
            $strResult = self::getBizResult($content, $YopRequest->format);
            if (!empty($ziped) && $YopResponse->error == '') {
                if ($YopRequest->encrypt) {
                    $decryptResult = self::decrypt($YopRequest, trim($ziped));
                    $YopResponse->stringResult = $decryptResult;
                    $YopResponse->result = $decryptResult;

                } else {
                    $YopResponse->stringResult = $strResult;
                }
            }
        }
        if ($YopRequest->signRet && !empty($YopRequest->sign)) {
            $signStr = $YopResponse->state . $ziped . $YopResponse->ts;
            $YopResponse->validSign = YopSignUtils::isValidResult($signStr, $YopRequest->secretKey, $YopRequest->signAlg, $YopResponse->sign);
        } else {
            $YopResponse->validSign = true;
        }

    }

    public function handleRsaResult($YopRequest, $YopResponse, $content)
    {

        $YopResponse->format = $YopRequest->format;
        $ziped = '';

        if (strtoupper($YopResponse->state) == 'SUCCESS') {
            $strResult = YopClient3::getBizResult($content, $YopRequest->format);

            $ziped = $strResult;

            if (!empty($ziped) && empty($YopResponse->error)) {
                $YopResponse->stringResult = $strResult;
            }
        }


        $YopResponse->validSign = YopClient3::isValidRsaResult($ziped, $YopResponse->sign, $YopRequest->yopPublicKey);
    }


    /**
     * 对业务结果签名进行校验
     */
    public static function isValidRsaResult($result, $sign, $public_key)
    {

        $sb = "";
        if ($result == null || empty($result)) {
            $sb = "";
        } else {
            $sb .= trim($result);
        }

        //echo $public_key;

        $public_key = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($public_key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";


        $pu_key = openssl_pkey_get_public($public_key);


        $sb = preg_replace("/[\s]{2,}/", "", $sb);

        $sb = str_replace(PHP_EOL, "", $sb);

        $sb = str_replace(" ", "", $sb);

        //echo $sb;

        //echo substr($sign,0,-7);


        $res = openssl_verify($sb, Base64Url::decode(substr($sign, 0, -7)), $pu_key, "SHA256"); //验证
        //echo "verify sig result:".$res; //输出验证结果，1：验证成功，0：验证失败

        openssl_free_key($pu_key);

        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }


    private function getBizResult($content, $format)
    {
        if (empty($format)) {
            return $content;
        }
        switch ($format) {
            case 'json':
                //preg_match('@"result" :(.+),"ts"@i', $content, $jsonStr);

                $result = strstr($content, '"result"');

                $length = strlen('"result"');
                $result = substr($result, $length + 3);

                $result = substr($result, 0, strrpos($result, '"ts"'));
                $result = substr($result, 0, strlen($result) - 4);

                return $result;

            default:
                //preg_match('@</state>(.+)<ts>@i', $content, $xmlStr);
                $result = strstr($content, '"</state>"');

                $length = strlen('</state>');
                $result = substr($result, $length + 4);


                $result = substr($result, 0, strrpos($result, '"ts"'));

                $result = substr($result, 0, -2);

                return $result;
        }
    }

    private function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        return $prefix . $uuid;
    }

    function encrypt_rsa($data, $pu_key)
    {
        $encode_data = "";
        $split = str_split($data, 100);// 1024bit && OPENSSL_PKCS1_PADDING  不大于117即可
        foreach ($split as $part) {
            $isOkay = openssl_private_encrypt($part, $en_data, $pu_key);
            if (!$isOkay) {
                return false;
            }
            // echo strlen($en_data),'<br/>';
            $encode_data .= base64_encode($en_data);
        }
        return $encode_data;
    }


    function decrypt_rsa($data, $pi_key)
    {
        $decode_data = "";
        $split = str_split($data, 172);// 1024bit  固定172
        foreach ($split as $part) {
            $isOkay = openssl_public_decrypt(base64_decode($part), $de_data, $pi_key);// base64在这里使用，因为172字节是一组，是encode来的
            if (!$isOkay) {
                return false;
            }
            $decode_data .= $de_data;
        }
        return $decode_data;
    }


    /**
     * $str 原始中文字符串
     * $encoding 原始字符串的编码，默认GBK
     * $prefix 编码后的前缀，默认"&#"
     * $postfix 编码后的后缀，默认";"
     */
    function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';')
    {
        $str = iconv($encoding, 'UCS-2', $str);
        $arrstr = str_split($str, 2);
        $unistr = '';
        for ($i = 0, $len = count($arrstr); $i < $len; $i++) {
            $dec = hexdec(bin2hex($arrstr[$i]));
            $unistr .= $prefix . $dec . $postfix;
        }
        return $unistr;
    }

    /**
     * $str Unicode编码后的字符串
     * $decoding 原始字符串的编码，默认GBK
     * $prefix 编码字符串的前缀，默认"&#"
     * $postfix 编码字符串的后缀，默认";"
     */
    function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';')
    {
        $arruni = explode($prefix, $unistr);
        $unistr = '';
        for ($i = 1, $len = count($arruni); $i < $len; $i++) {
            if (strlen($postfix) > 0) {
                $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
            }
            $temp = intval($arruni[$i]);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
        }
        return iconv('UCS-2', $encoding, $unistr);
    }

}
