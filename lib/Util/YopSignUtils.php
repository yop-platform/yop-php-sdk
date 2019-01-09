<?php

require_once("Base64Url.php");
require_once("AESEncrypter.php");
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/7
 * Time: 17:33
 */
abstract class YopSignUtils{

    /**
     * 签名生成算法
     * @param array $params API调用的请求参数集合的关联数组，不包含sign参数
     * @param array $ignoreParamNames 忽略的参数数组
     * @param String $secret 密钥
     * @param String $algName 加密算法
     *
    md2
    md4
    md5
    sha1
    sha256
    sha384
    sha512
    ripemd128
    ripemd160
    ripemd256
    ripemd320
    whirlpool
     *
     * @return string 返回参数签名值
     */
    static function sign($params, $ignoreParamNames='', $secret, $algName='sha1',$debug=false){
        $str = '';  //待签名字符串
        //先将参数以其参数名的字典序升序进行排序
        $requestparams = $params;

        ksort($requestparams);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($requestparams as $k => $v) {
            //查看Key 是否为忽略参数
            if(!in_array($k,$ignoreParamNames)){
                //为key/value对生成一个keyvalue格式的字符串，并拼接到待签名字符串后面

                //value不为空,则进行加密
                if (!($v === NULL)) {
                    $str .= "$k$v";
                }

            }

        }
        //将签名密钥拼接到签名字符串两头
        $str = $secret.$str.$secret;
        //通过指定算法生成sing

        $signValue = hash($algName,$str);

        var_dump($YopConfig);
        if ($debug) {
                    var_dump("algName=".$algName);
                    var_dump("str=".$str);
                    var_dump("signValue=".$signValue);
        }

        return $signValue;
    }


    /**
     * 签名验证算法
     * @param array $result API调用的请求参数集合的关联数组，不包含sign参数
     * @param String $secret 密钥
     * @param String $algName 加密算法
     * @param String $sign 签名值
     * @return string 返回签名是否正确 0 - 如果两个字符串相等
     */
   static function isValidResult($result, $secret, $algName,$sign){
       $newString = $secret.$result.$secret;

       if(strcasecmp($sign,hash($algName,$newString))==0){
           return true;
       }else{
           return false;
       }

   }

    static function signRsa($source,$private_Key)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_Key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        extension_loaded('openssl') or die('php需要openssl扩展支持');


        /* 提取私钥 */
        $privateKey = openssl_get_privatekey($private_key);

        ($privateKey) or die('密钥不可用');

        openssl_sign($source, $encode_data, $privateKey, "SHA256");

        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);


        $signToBase64 .= '$SHA256';


        return $signToBase64;

    }

    static function getPrivateKey($filepath,$password)
    {
        $pkcs12 = file_get_contents($filepath);
        openssl_pkcs12_read($pkcs12, $certs, $password);
        $prikeyid = $certs['pkey']; //私钥

        $prikeyid = str_replace('-----BEGIN RSA PRIVATE KEY-----','',$prikeyid);
        $prikeyid = str_replace('-----END RSA PRIVATE KEY-----','',$prikeyid);

        $prikeyid = preg_replace("/(\r\n|\n|\r|\t)/i", '', $prikeyid);

        return $prikeyid;

    }

}

