<?php

require_once ("../lib/Util/YopSignUtils.php");

function T1()
{
    $data = "test"; //加密数据测试test

    $certs = array();
    $pkcs12 = file_get_contents("/Users/yp-tc-7176/Downloads/10015004197.p12");
// No password
    openssl_pkcs12_read($pkcs12, $certs, "123456");

    $prikeyid = $certs['pkey']; //私钥


    openssl_sign($data, $signMsg, $prikeyid,OPENSSL_ALGO_SHA1); //注册生成加密信息
    $signMsg = base64_encode($signMsg); //base64转码加密信息

    //print_r($signMsg);

    //公钥解密
    $unsignMsg=base64_decode($signMsg);//base64解码加密信息
    $pubkeyid = $certs['cert']; //公钥
    $res = openssl_verify($data, $unsignMsg, $pubkeyid); //验证
    echo $res; //输出验证结果，1：验证成功，0：验证失败

    print_r($prikeyid);
    print_r($pubkeyid);

}

$result= YopSignUtils::getPrivateKey("/Users/yp-tc-7176/Downloads/10015004197.p12","123456");

echo $result;