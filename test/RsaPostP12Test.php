<?php
require_once ("../lib/YopRsaClient.php");

//Post请求 非对称秘钥P12(文件秘钥)
function rsa_post_p12(){
    /*商户秘钥文件存放的绝对路径*/
    $filepath="/Users/yp-tc-7176/Downloads/10015004197.p12";
    /*商户秘钥文件的打开密码*/
    $password="123456";

    /*商户私钥*/
    $private_key =YopSignUtils::getPrivateKey($filepath,$password);

    $request = new YopRequest("regression-test", $private_key);
    //加入请求参数
    $request->addParam("request_flow_id", "12345678");//请求流水标识
    $request->addParam("name", "123");//请求流水标识
    $request->addParam("id_card_number", "370982199101186691");//请求流水标识
    //提交Post请求
    $response = YopRsaClient::post("/rest/v1.0/test/auth2/auth-id-card", $request);
}

rsa_post_p12();
