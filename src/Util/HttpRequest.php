<?php

namespace Yeepay\Yop\Sdk\V1\Util;

use Yeepay\Yop\Sdk\V1\YopConfig;

define("LANGS", "php");
define("VERSION", "3.1.14");
define("USERAGENT", LANGS . "/" . VERSION . "/" . PHP_OS . "/" . (array_key_exists('SERVER_SOFTWARE', $_SERVER) ? $_SERVER ['SERVER_SOFTWARE'] : "") . "/Zend Framework/" . zend_version() . "/" . PHP_VERSION . "/" . (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "") . "/");

abstract class HttpRequest
{

    /**
     * 加密
     * @param string $str 需加密的字符串
     * @param string $key 密钥
     * @param string $CIPHER 算法
     * @param string $MODE 模式
     * @return bool|string
     */
    static public function curl_request($url, $request)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, USERAGENT);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_TIMEOUT, $request->readTimeout);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $request->connectTimeout);

        $TLS = substr($url, 0, 8) == "https://";
        if ($TLS) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $request->encoding();

        $headerArray = array();
        if ($request->headers != null) {
            foreach ($request->headers as $key => $value) {
                array_push($headerArray, $key . ":" . $value);
            }
        }
        array_push($headerArray, "x-yop-sdk-langs:" . LANGS);
        array_push($headerArray, "x-yop-sdk-version:" . VERSION);
        array_push($headerArray, "x-yop-request-id:" . $request->requestId);
        if (isset($request->jsonParam)) {
            array_push($headerArray, 'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($request->jsonParam));
        }
        if (!$request->fileMap) {
            array_push($headerArray, 'Content-Type: application/x-www-form-urlencoded');
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);

        //var_dump($headerArray);
        //var_dump($request);
        //var_dump($request->httpMethod);

        if ("POST" == $request->httpMethod) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($request->jsonParam != null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->jsonParam);
            } else {
                $fields = $request->paramMap;
                if ($request->fileMap) {
                    // form-data
                    foreach ($request->fileMap as $fileParam => $fileName) {
                        //$file_name = str_replace("%2F", "/",$post["_file"]);
                        //var_dump($fileParam);
                        //var_dump($fileName);
                        //var_dump($file_name);

                        // 从php5.5开始,反对使用"@"前缀方式上传,可以使用CURLFile替代;
                        // 据说php5.6开始移除了"@"前缀上传的方式
                        if (class_exists('CURLFile')) {
                            // 禁用"@"上传方法,这样就可以安全的传输"@"开头的参数值
                            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
                            $file = new \CURLFile($fileName);
                        } else {
                            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
                            $file = "@{$fileName}";
                        }

                        $fields [$fileParam] = $file;
                    }
                    curl_setopt($curl, CURLOPT_INFILESIZE, YopConfig::$maxUploadLimit);
                    //curl_setopt($curl, CURLOPT_BUFFERSIZE, 16kB);
                } else {
                    // x-www-form-urlencoded
                    $fields = http_build_query($fields);
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            }
        } else {
            //var_dump(http_build_query($request->paramMap));
            curl_setopt($curl, CURLOPT_URL, $url);
        }

        $data = curl_exec($curl);
        //var_dump($data);

        if (curl_errno($curl) || (is_bool($data) && !$data)) {
            var_dump('curl fail errno:', curl_errno($curl), ", errinfo:", curl_error($curl));
            return curl_error($curl);
        }

        $responseHeaders = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        //print_r($responseHeaders);
        //print_r(substr_compare($responseHeaders, "application/octet-stream", 0, 16));
        if (!empty($responseHeaders) && substr_compare($responseHeaders, "application/octet-stream", 0, 16) == 0) {
            $request->downRequest = true;
        }

        curl_close($curl);
        return $data;
    }
}
