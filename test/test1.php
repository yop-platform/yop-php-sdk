<?php
/**
 * User: wilson
 * Date: 16/6/17
 * Time: 18:44
 */
require_once("../lib/YopClient.php");
require_once("../lib/YopClient3.php");
//下单接口请求参数
function test1()
{
    /*测试私钥*/
    $private_key = "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCDCC2oVe6OYd8ZtuhW9AN8wV9bat5wz3rva5H8iPAv99VQkORANnh6l+a7RNVfN9w+Yii6UeavhSsulgicDUngJdCHaPIsuXRWt26ejSsLeHmxXnWPG2AObZcnyYzUzwZ4MiAWJ6RcRrF7BZGpAPkBGK0kLBeZ9e8Ko8SgRUXzVHmPjg8oF5vV0xMNDj92X0oZBVfzt0rOSqlGVWWgRkgIBz6CZKiy9pmLnKOnpG5qOOdiTdth+DsAR7ABK4lzkPeesAsR1VzP4EqW/TKC64YKhMA3N1ovfMC9EpQ2oCPwvairAsQcB/pvXxHBXttF/BTrTw/Ks9tkh2QMRBvZGHpfAgMBAAECggEABr/1GibTEyKXi4uQjGolg9eyQdNPgiAuBQdVjdzAAriRlITiPSyRKD+K8zqogy8teUk1L+PoLkJ95vhzmRZWJ+XKyC7vyr4C8DSizigXf4/FNQ3YoHaYjCW5E6OeTZgcjTSH0pxYKyi5G809o6cZLKVIxgQ/cv7oQXQOPPNUlyQ/aBl1c1cSDAWbyX7BDduqZmk5BPnyud9vtEOuKAQqFwPfy3/ZfkibilUYcvtNqRSUl/7VinZeAisSXPbKre2qk5ll/YXeavxkBZxdq6/JS5O4ivBtrQy9Fnil+7hBe6Qfw4Lt7Fv5NdObJIVwzq7cMTHGxnUaf3MNRpdkHvJsgQKBgQDrPt7vI5BuMvzM6wILXnxQ76quYPFE9nJ1glYPCpCAirKP5kAEjmH/2mJ4IqTi2uT5pgoPb0zGspL7R1tsJcSgGa98qEgyeG1n+6C9M2a+vmht8VTj0nrZeIIigQH2dF16K8c87H1jgg0N5VrjG+pRKG23dQ0rX4O0B+3MoHUN3QKBgQCOl5hCO2OVvillvvk0Wabll3ytWYZZRN/4COWtDaXY10RkpeBRyDZvAUE9Gyi/ZegfvTfZzV5gPnVFtXqbIEY8u0xD4MQSAuncY4V16cv70cvu4u3xGEZKgzgk8TOfPNxInCWUles6lP451x5B3HIAa63Ii1j3Qd0ceuI8iqT7awKBgQCh8M7Q+r+DTPBANItcvjeAE+yATFXqrmjOweFyS0h8ZH5VlyB8wnNuCKz+nIK7dApqXUXRqEHHCskp1850nW9E80md286Ph91w1oSpmkfhiPwkqxxQFOXi7RVQoVRzj1mGL7rhEr+ij7Vi2n99lgrwwY792sMtF3x3o3mtAsxxtQKBgAf5YFFr4tDP9p6zBFqyHMxAIX/MPuAlIuVLEhUQa1LqDvAV+qp4KNsiVdSl/Sxe9ZE40rPCcWGufH5ufLHKJ0NkMgqlujFLqmphwmfqsDaf7+inFilicyPdnLksJ/fivmrtGIjrrWD0ThdL+WwzeMifPPO3Hz2MmGHsWVSLaFiLAoGBANL7mp+y+J9Olx9LPjR14lanOg4PhnhIJ/CQt41WWgkEbSXfign0LaYwJQ2ly6y8KoaVPN/VeICTQ9RXsvIAwUmy0YB4hvRS6kfsdsP+9MWMooecsnsz+fUgY+Ff6pJL3dhnr0cPqiB0J0xH2gMD80i9QFUfaWAmLD7KvB1y3XA4";
    /*测试公钥*/
    $yop_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB';

    $request = new YopRequest("OPR:10014929805", $private_key, "http://open.yeepay.com/yop-center", $yop_public_key);
    $request->addParam("parentMerchantNo", "10014929805");
    $request->addParam("merchantNo", "10014929805");
    $request->addParam("orderId", "18253166342");
    $request->addParam("orderAmount", "0.01");
    $request->addParam("timeoutExpress", "");
    $request->addParam("requestDate", "2017-08-04 13:23:45");
    $request->addParam("redirectUrl", "http://www.merchant.sscom:8080/uc/payCallback?u8ChannelID=10");
    $request->addParam("notifyUrl", "http://payment.merchant.com:8080/uc/payCallback?u8ChannelID=8");
    $request->addParam("goodsParamExt", "{\"goodsName\":\"abc商品名称\",\"goodsDesc\":\"商品描述\"}");
    $request->addParam("paymentPaamExt", "");
    $request->addParam("industryParamExt", "");
    $request->addParam("memo", "");
    $request->addParam("riskParamExt", "");
    $request->addParam("csUrl", "");

    $response = YopClient3::post("/rest/v1.0/std/trade/order", $request);
    print_r($response);
    if ($response->validSign == 1) {
        echo "返回结果签名验证成功!";
        //  print()
    }
    print_r($response->result);
}

function test2()
{

    $request = new YopRequest("",
        "8intulgnqibv77f1t8q9j0hhlkiy6ei6c82sknv63vib3zhgyzl8uif9ky7",
        "http://172.17.102.173:8064/yop-center");
    $request->addParam("customerNo", "10040011444a");
    $request->addParam("platformUserNo", "8880222");

    // print_r($request);
    //print_r("\n\n\n\n\n\n");

    $response = YopClient::get("/rest/v1.0/member/queryAccount", $request);

    print_r($response);
}

function test3()
{
    $request = new YopRequest("neices",
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
    $request->addParam("customerNo", "211287279703");
    $request->addParam("platformUserNo", "211287279703");
    //print_r($request);
    //print_r("\n\n\n\n\n\n");
    $response = YopClient::post("/rest/v1.0/member/queryAccount", $request);

    print_r($response);
}

function test4()
{
    $request = new YopRequest("",
        "J58961W1061051gx1R7n80s76UHA2499oe51881iWx1Rgu8TV20bY2M7l52", "http://open.yeepay.com/yop-center");
    $request->setEncrypt(true);
    $request->addParam("customerNo", "10040020578d");
    $request->addParam("merchantno", "10040020578");
    $request->addParam("yborderid", "124");
    $request->addParam("requestno", "8880222");
    $response = YopClient::get("/rest/v1.0/paperorder/api/refund/query", $request);
    print_r($response);
}

function test5()
{
    $request = new YopRequest("",
        "s5KI8r0920SQ339oVlFE6eWJ0yk019SD7015nw39iaXJp10856z0C1d7JV5", "http://open.yeepay.com/yop-center");
    $request->setEncrypt(true);
    $request->addParam("customerNo", "10011830665l");
    $request->addParam("customernumber", "10012544672");
    $request->addParam("amount", "0.1");
    $request->addParam("callbackurl", "http://www.baidu.com");
    $request->addParam("webcallbackurl", "http://www.baidu.com");
    $request->addParam("bankid", "ICBC");
    $request->addParam("payproducttype", "SALES");
    $response = YopClient::post("/rest/v1.0/merchant/pay", $request);
    print_r($response);
}

function test6()
{
    $request = new YopRequest("",
        "0owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846", "http://10.151.30.88:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignAlg("sha256");
    $request->addParam("customerNo", "10040020578T");
    $request->addParam("merchantno", "10040020578");
    //$request->addParam("yborderid", "124");
    $request->addParam("requestno", "YOP-SDK-1471601751370");
    $response = YopClient::post("/rest/v1.0/paperorder/api/pay/query", $request);
    print_r($response);
}

function T1()
{
    $secretKey = "purc4lI/VsThnnL0Yu4g1A==";

    $request = new YopRequest("sdk-develop", $secretKey, "http://open.yeepay.com/yop-center");

    $request->setEncrypt(true);
    $request->setSignAlg("sha1");
    #$request->addParam("appKey", "B112345678901237");
    $request->addParam("fileType", "IMAGE");
    $request->addParam("_file", "file:/Library/WebServer/Documents/yop/test/1.jpeg");

    //$request->addParam("_file", "file:/Users/zhangwenkang/Desktop/tomcat-lifecycle.png");


    $response = YopClient::upload("/rest/v1.0/file/upload", $request);
    print_r($response);
}

function T2()
{
    $request = new YopRequest("",
        "8intulgnqibv77f1t8q9j0hhlkiy6ei6c82sknv63vib3zhgyzl8uif9ky7",
        "http://10.151.30.87:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignAlg("sha1");
    $request->addParam("customerNo", "10040011444a");
    $request->addParam("fileType", "IMAGE");
    $request->addParam("fileURI", "file:/usr/local/b1.jpeg");

    $response = YopClient::upload("/rest/v1.0/file/upload", $request);
    // print_r($request);
    print_r($response);
}

function T3()
{
    $request = new YopRequest("neices",
        "LQ2YEfJq8PPRJXR/03nQ0Q==", "http://10.151.30.88:8064/yop-center");
    $request->setEncrypt(true);
    $request->setSignAlg("sha1");
    $request->addParam("customerNo", "10040011444");
    $request->addParam("platformUserNo", "8880222");

    $response = YopClient::post("/rest/v1.0/member/queryAccount", $request);
    print_r($response);
}

//Post 对称
function T4()
{
    $secretKey = "purc4lI/VsThnnL0Yu4g1A==";

    $request = new YopRequest("sdk-develop", $secretKey, "http://open.yeepay.com/yop-center");

    $request->setEncrypt(true);
    $request->setSignAlg("sha256");
    $request->addParam("request_flow_id", "12345678");//请求流水标识
    $request->addParam("name", "张文康");//请求流水标识
    $request->addParam("id_card_number", "370982199101186691");//请求流水标识

    $response = YopClient::post("/rest/v1.0/test/auth/idcard", $request);
    //print_r($request);
    //print_r($response);
    if ($response->validSign == 1) {
        echo "返回结果签名验证成功!";
    }
    print_r($response->result);
}

//Post 非对称
function T5()
{
    /*测试私钥*/
    $private_key = "";
    /*测试公钥*/
    $yop_public_key = '';

    $request = new YopRequest("sdk-develop", $private_key, "http://open.yeepay.com/yop-center", $yop_public_key);
    $request->addParam("request_flow_id", "12345678");//请求流水标识
    //$request->addParam("request_ip", "1.2.3.4");//请求流水标识
    //$request->addParam("exclude_photo", "true");//请求流水标识

    //$request->addParam("name", "张文康");//请求流水标识
    $request->addParam("name", "张文康");//请求流水标识
    $request->addParam("id_card_number", "370982199101186691");//请求流水标识

    $response = YopClient3::post("/rest/v1.0/test/auth/idcard", $request);
    //print_r($request);
    //print_r($response);
    if ($response->validSign == 1) {
        echo "返回结果签名验证成功!";
    }

    print_r($response->result);
}

//Get 对称
function T8()
{
    $secretKey = "purc4lI/VsThnnL0Yu4g1A==";

    $request = new YopRequest("sdk-develop", $secretKey, "http://open.yeepay.com/yop-center");

    $request->setEncrypt(true);
    $request->setSignAlg("sha256");
    $request->addParam("request_flow_id", "12345678");//请求流水标识
    $request->addParam("name", "张文康");//请求流水标识
    $request->addParam("id_card_number", "370982199101186691");//请求流水标识

    $response = YopClient::get("/rest/v1.0/test/auth/idcard", $request);
    //print_r($request);
    //print_r($response);
    if ($response->validSign == 1) {
        echo "返回结果签名验证成功!";
    }
    print_r($response->result);
}

//T5();
test1();