<?php

require_once("HttpUtils.php");

define("LANGS", "php");
define("VERSION", "2.1.0");
define("USERAGENT", LANGS."/".VERSION."/".PHP_OS."/".$_SERVER ['SERVER_SOFTWARE']."/Zend Framework/".zend_version()."/".PHP_VERSION."/".$_SERVER['HTTP_ACCEPT_LANGUAGE']."/");

abstract class HTTPRequest{

    /**
     * 加密
     * @param string $str	    需加密的字符串
     * @param string $key	    密钥
     * @param string $CIPHER	算法
     * @param string $MODE	    模式
     * @return type
     */
    static public function curl_request($url, $post, $timeout=120, $json=false, $Multipart =false, $headers=null, $jsonParam=null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, USERAGENT);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $TLS = substr($url, 0, 8) == "https://" ? true : false;
        if($TLS) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $headerArray=array();
        if($headers!=null) {
            foreach ($headers as  $key => $value) {
                array_push($headerArray, $key.":".$value);
            }
        }
        array_push($headerArray, "x-yop-sdk-langs:".LANGS);
        array_push($headerArray, "x-yop-sdk-version:".VERSION);
        if($jsonParam!=null) {
            array_push($headerArray,'Content-Type: application/json; charset=utf-8',
                                    'Content-Length: ' . strlen($jsonParam));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER,  $headerArray);

        //var_dump($headerArray);

        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            if($jsonParam!=null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonParam);
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($post)?http_build_query($post):$post);
            }
        }

        if($Multipart){
            curl_setopt($curl, CURLOPT_POST, 1);

            $file_name = str_replace("%2F", "/",$post["_file"]);

            // 从php5.5开始,反对使用"@"前缀方式上传,可以使用CURLFile替代;
            // 据说php5.6开始移除了"@"前缀上传的方式
            if (class_exists('CURLFile')) {
                // 禁用"@"上传方法,这样就可以安全的传输"@"开头的参数值
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
                $file = new CURLFile($file_name);
            } else {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
                $file = "@{$file_name}";
            }

            $fields = $post;
            $fields ['_file'] = $file;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        }

        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        return $data;
    }
}
