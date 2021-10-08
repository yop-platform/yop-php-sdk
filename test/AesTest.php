<?php
namespace Yeepay\Yop\Sdk\V1\Test;

use PHPUnit\Framework\TestCase;
use Yeepay\Yop\Sdk\V1\YopClient;
use Yeepay\Yop\Sdk\V1\YopConfig;
use Yeepay\Yop\Sdk\V1\YopRequest;

class AesTest extends TestCase {

    /**
     * @test Get请求 对称秘钥
     */
    function get_aes(){
        YopConfig::$debug=true;

        $appKey = "yop-boss";
        $secretKey = "PdZ74F6sxapgOWJ31QKmYw==";
        $request = new YopRequest($appKey, $secretKey);

        //加入请求参数
        $request->addParam("request_flow_id", "12345678");//请求流水标识
        $request->addParam("name", "xxx");//请求流水标识
        $request->addParam("id_card_number", "xxx");//请求流水标识

        //提交Get请求
        $response = YopClient::get("/rest/v3.0/auth/idcard", $request);
    }

    /**
     * @test Post请求 对称秘钥
     */
    function post_aes(){
        YopConfig::$debug=true;

        $appKey = "yop-boss";
        $secretKey = "PdZ74F6sxapgOWJ31QKmYw==";
        $request = new YopRequest($appKey, $secretKey);

        //加入请求参数
        $request->addParam("request_flow_id", "12345678");//请求流水标识
        $request->addParam("name", "xxx");//请求流水标识
        $request->addParam("id_card_number", "370982199101186691");//请求流水标识

        //提交Post请求
        $response = YopClient::post("/rest/v3.0/auth/idcard", $request);
    }
}