<?php
namespace Yeepay\Yop\Sdk\V1\Test;

use PHPUnit\Framework\TestCase;
use Yeepay\Yop\Sdk\V1\YopConfig;
use Yeepay\Yop\Sdk\V1\YopRequest;
use Yeepay\Yop\Sdk\V1\YopRsaClient;

class RsaTest extends TestCase {

    /**
     * @test Post请求 非对称秘钥 form格式
     */
    function post_rsa_form(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->addParam("request_flow_id", "12345678");//请求流水标识
        $request->addParam("name", "xxx");//请求流水标识
        $request->addParam("id_card_number", "370982199101186691");//请求流水标识

        //提交Post请求
        $response = YopRsaClient::post("/rest/v3.0/auth/idcard", $request);
    }

    /**
    * @test Post请求 非对称秘钥 form格式 array 数组
    */
    function post_rsa_form_1(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->addParam("strList", join(",", array("strList_example", "nihao")));
        $request->addParam("doubleAmount", 1.2);
        $request->addParam("intNum", 56);
        $request->addParam("bigDecimalNum", 1.2);
        $request->addParam("longNum", 789);

        //提交Post请求
        $response = YopRsaClient::post("/rest/v1.0/test-wdc/test-param-parse/pojo-param-form", $request);
    }

    /**
     * @test Post请求 非对称秘钥 json格式
     */
    function post_rsa_json(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->setJsonParam("{\"strList\":[\"你好\"]}");

        //提交Post请求
        $response = YopRsaClient::post("/rest/v1.0/test-wdc/test-param-parse/pojo-param", $request);
    }

    /**
     * @test Post请求 非对称秘钥 上传
     */
    function post_rsa_upload(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->addFile("merQual", "./1.jpeg");

        //提交Post请求
        $response = YopRsaClient::upload("/yos/v1.0/sys/merchant/qual/upload", $request);
    }

    /**
     * @test Get请求 非对称秘钥
     */
    function get_rsa(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->addParam("request_flow_id", "12345678");//请求流水标识
        $request->addParam("name", "xxx");//请求流水标识
        $request->addParam("id_card_number", "xxx");//请求流水标识

        //提交Post请求
        $response = YopRsaClient::get("/rest/v3.0/auth/idcard", $request);
    }

    /**
     * @test Get请求 非对称秘钥 文件下载
     */
    function download_rsa(){
        YopConfig::$debug=true;

        /*商户私钥*/
        $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDJvpvNBByNR/i8Uys1uSJJd9ly3lXBQQxcQIiC+sVDtN8Ejo/g5/k9RGoDplXKnzDQ9pNWpcY9GnYgIbFMKIwUqjGaKCTC4/fGZvt4ugqJaDQKeVQsOIs+475DoCi0X7yC0lk8Z8o+C6HqWghq4aiap1WUYameBy2hPxinX0uWisocZ7np/s01z5jE9afv4Agbq/RLv14YQ0LDVFrsKHjNo4XYe6IR3Wynt28WMa9Gs0Y6WXKhgH58KvksrJX0+TztRbbHnCt3DZ74seaxn+bo9UL8K0Q2T3d02qK0bGBIoZkTLWjzaVVacs7xQ0nXU33BaQ600sXi4y54o/HWlUaBAgMBAAECggEAYoGvkWtwhYue6FWzw4eiNj1O78egF7yrTGA2R74qk+S9AHybxDWAfWnqWd3eBhG0xFOhna1UHoHNK+NHrugdffmcPqlbSc4GLdoa79fnTTCUOIkFkJILa6nIPTz2oxwb78TFzbjgB2umo8dSVN3adak/IDSPnZnjrdghMZhWUCqWllI8/33IeWRu3JUeLSeuGvlU8xF5j1ALXXIyleNjep4HPC/+NNE20kWRQS70ffagYG7NuZA0OQSam1n70+2VNmGYoGSd5LcAlUy4/U7Jh525Wx0vjovoR8AGrAUuTsc/blg1fvYusr3Z7stS7vmPTWcfCYVJ49gOHE4YGfb0AQKBgQDj6b+XdVVjY6XcyVFKCbarlJgbhh9rjGECx2DAlnSmQ+67+IQNpAg8txZ3NykrkjXMZIIOrEugsBp86jrjRI7lF4eI2nWqpe4T7ZNEOKMRRXxT1ediYDLr1PAszx/l0/P4Ro3xubtOjOtzx+xKcGASb34c0Hft99uzOYhmVpvErwKBgQDim0pItsBjv18sGlSJZqBweXM1Frmz0fNxy9fTnTTmRA/o5atWLrvACkH2yxBG7gBOIX71CvurCQy+kFwCMZ6//sbDahm3hrdPCoBP9dp5z7POcFMoJHvZzaxkpbdGlYegXt2km6JltRu8FZX1uEJH77C7vi0ewcwzqN2viAvTzwKBgAi2IY2ffYEMCQX0Z/gFgQbz6hB7Qu4wcnDRwB/8YD8Or6xdpmaDE5GGigRKhndU4luKp/H5ofZlZM3Lgi63qyKUkKipeP/p0bzPQubDp2/8kPD/ZxW6iZe8DuYXkKePP28I+1n2+HLbLhDB3oVF4FY0DsT5LuxYofwqwczvmIqfAoGBAIno8muQdUP/et9vYtWAVNI+x8OeggQTGXK/GSnbeg9NitU1uXGo3XDBjWWyLcTNIfhq4EYnmgR8bHophyV6p1+3oaXaE66i2TrMbEy9lmod4xMXPzSmB44FYw6Z4BGf/Tu3oHKGmW4Gq8tq46n4qrX3BPstgW4/iZRDCC/Ev1X3AoGAZjYOhhnRbxMo/ZuCuTdgqkKk/iOiZRByPhdntvXoGuu3eWsf6LNImDm7LJdLj/nSZhhh1JnKNs3bvvzCjNv0OnxcHTqLufCpTzsh7fOTCAH1YN8qcAdGLUaKDq9w/WnfOsezViCZSwvrSybV0nND+sYEoc+DPluG97isqHjq9ys=";

        $request = new YopRequest("app_100800095600031", $private_key);

        //加入请求参数
        $request->addParam("strParam", "xxx");

        //提交Post请求
        $response = YopRsaClient::get("/rest/v1.0/test-wdc/test-param-parse/input-stream-result", $request);
    }
}