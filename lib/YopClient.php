<?php

require_once("YopRequest.php");
require_once("YopResponse.php");
require_once("Util/YopSignUtils.php");
require_once("Util/HttpRequest.php");

class YopClient{

    public function __construct(){

    }

    public function __set($name, $value){
        // TODO: Implement __set() method.
        $this->$name = $value;
    }

    public function __get($name){
        // TODO: Implement __get() method.
        return $this->$name;
    }

    static public function get($methodOrUri, $YopRequest){
        $content = YopClient::getForString($methodOrUri, $YopRequest);
        $response = YopClient::handleResult($YopRequest, $content);
        return $response;
    }

    static public function getForString($methodOrUri, $YopRequest){
        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);
        $headers = YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $YopRequest->encoding();
        $serverUrl .= (strpos($serverUrl,'?') === false ?'?':'&') . $YopRequest->toQueryString();
        $response = YopClient::getRestTemplate($serverUrl, $YopRequest, "GET", $headers);
        return $response;
    }

    static public function post($methodOrUri, $YopRequest){
        $content = YopClient::postForString($methodOrUri, $YopRequest);
        $response = YopClient::handleResult($YopRequest, $content);
        return $response;
    }

    static public function postForString($methodOrUri, $YopRequest){
        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);
        $headers = YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $response = YopClient::getRestTemplate($serverUrl, $YopRequest, "POST", $headers);
        return $response;
    }

    static public function upload($methodOrUri, $YopRequest){
        $content = YopClient::uploadForString($methodOrUri, $YopRequest);
        $response = YopClient::handleResult($YopRequest, $content);
        return $response;
    }

    static public function uploadForString($methodOrUri, $YopRequest){
        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);

        //$alternate = file_get_contents($YopRequest->getParam("_file"));

        $headers = YopClient::signAndEncrypt($YopRequest);

        //$YopRequest->addParam("_file",str_replace('file:','@',$strTemp));PUT
        // Create a CURLFile object
        //$cfile = curl_file_create($file);

        //echo $YopRequest->getParam("_file");

        $YopRequest->absoluteURL = $serverUrl;

        $response = YopClient::getRestTemplate($serverUrl, $YopRequest, "PUT", $headers);
        return $response;
    }

    static public function getRestTemplate($serverUrl, $YopRequest, $method, $headers){
        $YopRequest->encoding();

        if($method == "GET"){
            return HTTPRequest::curl_request($serverUrl, '', $YopRequest->Config->connectTimeout, true, $headers);
        } elseif ($method == "PUT"){
            //$YopRequest->addParam("_file",$YopRequest->ImagePath,true );
            return HTTPRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, true, true, $headers);
        }
        return HTTPRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout, false, false, $headers);
    }

    static public function signAndEncrypt($YopRequest){
        if(empty($YopRequest->method)){
            error_log("method must be specified");
        }
        if(empty($YopRequest->secretKey)){
            error_log("secretKey must be specified");
        }
        $appKey =$YopRequest->{$YopRequest->Config->APP_KEY};
        if(empty($appKey)){
            $appKey = $YopRequest->Config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->Config->APP_KEY);
        }
        if(empty($appKey)){
            error_log("appKey 与 customerNo 不能同时为空");
        }

        $signValue="";
        $signValue=YopSignUtils::sign($YopRequest->paramMap,$YopRequest->ignoreSignParams,$YopRequest->secretKey,$YopRequest->signAlg);

        $YopRequest->addParam($YopRequest->Config->SIGN,$signValue);
        if($YopRequest->isRest){
            $YopRequest->removeParam($YopRequest->Config->METHOD);
            $YopRequest->removeParam($YopRequest->Config->VERSION);
        }

        $headers = array();
        $headers['x-yop-request-id'] = $YopRequest->requestId;
        return $headers;
    }

    static public function richRequest($methodOrUri, $YopRequest){
        if(strpos($methodOrUri, $YopRequest->Config->serverRoot)){
            $methodOrUri = substr($methodOrUri,strlen($YopRequest->Config->serverRoot)+1);
        }
        $isRest = (strpos($methodOrUri,"/rest/") == 0)?true:false;
        $YopRequest->isRest = $isRest;
        $serverUrl = $YopRequest->serverRoot;
        if($isRest){
            $serverUrl .= $methodOrUri;
            preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
            if(!empty($version)){
                $version = $version[1];
                if(!empty($version)){
                    $YopRequest->setVersion($version);
                }
            }
        }else{
            $serverUrl .=  "/command?" . $YopRequest->Config->METHOD . "=" . $methodOrUri;
        }
        $YopRequest->setMethod($methodOrUri);
        return $serverUrl;
    }

    public function handleResult($YopRequest, $content){
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
