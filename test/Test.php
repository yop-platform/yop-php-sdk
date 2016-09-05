<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/6/17
 * Time: 18:44
 */


require_once ("../lib/YopClient.php");





function test1(){
    $request=new YopRequest("openSmsApi",
        "1234554321",
        "http://open.yeepay.com/yop-center");
    //"http://172.17.102.173:8064/yop-center");
    $request->setSignAlg("MD5");
    $request->addParam("notifyRule","商户结算短信通知");
    $request->addParam("recipients","18253166342");
    $request->addParam("content","{code:1235}");
    $request->addParam("extNum","3");
    $request->addParam("feeSubject","0.01");

    $response = YopClient::get("/rest/v1.0/notifier/send",$request);


    print_r($response);
}

function test2(){

    $request=new YopRequest("",
        "8intulgnqibv77f1t8q9j0hhlkiy6ei6c82sknv63vib3zhgyzl8uif9ky7a",
        "http://172.17.102.173:8064/yop-center");
    $request->setFormat(true);
    $request->setSignRet(true);
    $request->addParam("customerNo", "10040011444");
    $request->addParam("requestId", "123");
    $request->addParam("platformUserNo", "8880222");

   // print_r($request);
    //print_r("\n\n\n\n\n\n");

    $response = YopClient::get("/rest/v1.0/member/queryAccount",$request);


    print_r($response);

}
function test3(){
    $request=new YopRequest("neices",
        "LQ2YEfJq8PPRJXR/03nQ0Q==",
        "http://open.yeepay.com/yop-center");

//    md5
//    sha1
//    sha256
//    sha384
//    sha512
//    $request->setSignAlg("sha256");
    $request->setSignAlg("sha1");
    $request->setEncrypt(true);
    $request->addParam("customerNo","211287279703");
    $request->addParam("platformUserNo","211287279703");
    $request->addParam("requestId","123456789");
    //print_r($request);
    //print_r("\n\n\n\n\n\n");
    $response = YopClient::post("/rest/v1.0/member/queryAccount",$request);

    print_r($response);

}
function test4(){
    $request = new YopRequest("",
        "J58961W1061051gx1R7n80s76UHA2499oe51881iWx1Rgu8TV20bY2M7l52d", "http://open.yeepay.com/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->addParam("customerNo", "10040020578");
    $request->addParam("merchantno", "10040020578");
    $request->addParam("yborderid", "124");
    $request->addParam("requestno", "8880222");
    $response = YopClient::get("/rest/v1.0/paperorder/api/refund/query", $request);
    print_r($response);
}
function test5(){
    $request = new YopRequest("",
        "s5KI8r0920SQ339oVlFE6eWJ0yk019SD7015nw39iaXJp10856z0C1d7JV5l", "http://open.yeepay.com/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->addParam("customerNo", "10011830665");
    $request->addParam("customernumber", "10012544672");
    $request->addParam("requestid", time());
    $request->addParam("amount", "0.1");
    $request->addParam("callbackurl", "http://www.baidu.com");
    $request->addParam("webcallbackurl", "http://www.baidu.com");
    $request->addParam("bankid", "ICBC");
    $request->addParam("payproducttype", "SALES");
    $response = YopClient::post("/rest/v1.0/merchant/pay", $request);
    print_r($response);
}
function test6(){
    $request = new YopRequest("",
        "0owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846T", "http://10.151.30.88:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->setSignAlg("sha256");
    $request->addParam("customerNo", "10040020578");
    $request->addParam("merchantno", "10040020578");
    //$request->addParam("yborderid", "124");
    $request->addParam("requestno", "YOP-SDK-1471601751370");
    $response = YopClient::post("/rest/v1.0/paperorder/api/pay/query", $request);
    print_r($response);
}

function T1(){
    $request = new YopRequest("B112345678901237",
        "nUXQx0Mt0aSKvR0uNOp6kg==",
        "http://10.151.30.87:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->setSignAlg("sha1");
    $request->addParam("appKey", "B112345678901237");
    $request->addParam("fileType", "IMAGE");
    $request->addParam("fileURI", "file:/usr/local/b1.jpeg");

    $response = YopClient::upload("/rest/v1.0/file/upload", $request);
    print_r($response);
}

function T2(){
    $request = new YopRequest("",
        "8intulgnqibv77f1t8q9j0hhlkiy6ei6c82sknv63vib3zhgyzl8uif9ky7a",
        "http://10.151.30.87:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->setSignAlg("sha1");
    $request->addParam("customerNo", "10040011444");
    $request->addParam("fileType", "IMAGE");
    $request->addParam("fileURI", "file:/usr/local/b1.jpeg");

    $response = YopClient::upload("/rest/v1.0/file/upload", $request);
   // print_r($request);
    print_r($response);
}

function T3(){
    $request = new YopRequest("B112345678901237",
        "nUXQx0Mt0aSKvR0uNOp6kg==", "http://10.151.30.88:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->setSignAlg("sha1");
    $request->addParam("customerNo", "10040011444");
    $request->addParam("requestId", "124");
    $request->addParam("platformUserNo", "8880222");

    $response = YopClient::post("/rest/v1.0/member/queryAccount", $request);
    print_r($response);
}
function T4(){
    $request = new YopRequest("",
        "8intulgnqibv77f1t8q9j0hhlkiy6ei6c82sknv63vib3zhgyzl8uif9ky7a",
        "http://10.151.30.88:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignRet(true);
    $request->setSignAlg("sha1");
    $request->addParam("customerNo", "10040011444");
    $request->addParam("requestId", "124");
    $request->addParam("platformUserNo", "8880222");
    $response = YopClient::post("/rest/v1.0/member/queryAccount", $request);
    //print_r($request);
    print_r($response);
}
T1();
