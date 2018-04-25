<?php

require_once ("../lib/YopClient.php");
require_once ("../lib/YopClient3.php");
require_once ("../lib/Util/YopSignUtils.php");

//Post请求 非对称秘钥
function T1(){
    /*商户私钥*/
    $private_key ="MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDjqLUXxNWQ9s7M9sCmO02prhXK+027qSad2Tgu8ichJTBkz27JFQNjDc22zn6BY86jKyRy6WUob9pLjdE/n9QYlsAX06pF8R2+LKGsEeL2O0hSdA3jfxuINWomkos6iahyBWaJ8XkuJnq7NVs9eeD2JLRfju72qjuieN3QQlZoRmV3B90fmZbZJVRFo4qYEKnCb2ddHKsIrkKovR6gDuJw1Hrik07COTzlio6alP8vF1MXdmPoqQEMR5XYmXI3jvKrmJBXv8hiydYXvELdWDf5GEMVDMn5D8Alf2g4vlL735f1CQaapaHPP/5ByDUrfEX61JxrXe6HN3fYYOfck+4VAgMBAAECggEARr1JaBVVdzH5mF6kBgMvYqYhR21G/iTVRv8UIiJEXlw76Vc7SWgqIUFMxpzrWjE1KCZjsQEs2Z/H6OosNuTm9q0BC2ZN5mXBpDIiGmGEcJaDJnvBRQtd9rkCPvISKSJVIPjkn8BUsy5pvHob28JfUJyfH4I+Zo8G8Lk101yJlOSU4jUu9b6G81AKLeEeAYGda6WS3gi7hEZJE8lGYHaV8isyejY789LLg1bpYdblaFGDrr0Ry6eutBgvHsbqikptZaszmZIYbuoTbUZ/mb/u2FD0JzpO0qTzCSrJ2quxAtExyXyWKDRlIpkTfcdQQZbraBfT50brDJxsxTy7GQOGgQKBgQDzJFXRAYkA7PCu4XbXH7lnRyTsJpvjOzLfmZflk3b4JSw23POr74Omj0s8k4LrhNqZkid/NkPugnQ16sCV64mjwnuYHwdFAM5L06RZcYYJzdsHPGsNRXdgLMesDn+E/ElME9wqT1SMeb3SJRMoh+mhB09ibuFeJZ/S8DoeBCYqDQKBgQDvssOIIh6Be+HO5wZI7xFtdZI0xNBpgYd9FKWsYXZLb7J3JNdsypQ026w6b0HbWxo6swHza9Oee9xI9kT00h5A4ULeUK9vzytunhQ64SQv+dyuV6uq8GR1t+Ct3aRiTptq+OE8lGYV5lnrDtf4w921ASg/+z6Il85WMLGSAeF6KQKBgB9Zdf8m1YVbB4Vk6K/AFWWtlKMmLz9TKLIP2bfuFY7VLCg+wORQU4csPqUm1+CKFq1qXNFYK9Ya1+UDyaUK44pLzyfECGnrOq4oMa6epoupES6TqbHiXQv3eh9DF0oMRV8byG1kISCjqcfoo9m7/HGYwbGNXl2HOF44PyYTnuAZAoGAVEwCB/Y31EHFAAavZybschpO+M/em7w58fs46I6+E3mMwenq+Q+lX5GB2GsYNhSr/MjHftwE6E+XAPHa/l6G1TpjUdv+Vsv88kIVyz8ygszUl1utu8gfDGdGVN8F2Kzca5prW+q0nC8OoGPwAEShy+5EQXwSWedJYXZufHK84UECgYEAr/wh4uofqw3PWb5TSAfiu66gXKM8jbWFJFrts5vUmKu72RWo32KyPNVyKuzxmOa2yND+p8OHkx1kWJJNIqZlwmp4dLiICjveN6jeiWzAvMs2Far6GF26KuJexrOOuf+/Q40yNjKkuFmnhYH5gSJCvU8c2WIotfThng9jpFOijtM=";
    /*YOP公钥*/
    $yop_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB';

    $request = new YopRequest("app_eyMpU7WNTxPW464B", $private_key,  "http://10.151.30.87:8064/yop-center",$yop_public_key);
    //加入请求参数

    $request->setJsonParam("{\"requestFlowId\":\"123\",\"idCardNumber\":\"370921198706011539\",\"name\":\"纪柏涛\"}");
    //提交Post请求
    $response = YopClient3::post("/rest/v1.0/test_apino_fly/auth2/auth-id-card", $request);
    if($response->validSign==1){
        echo "返回结果签名验证成功!\n";
    }
    //取得返回结果
    print_r($response->stringResult);
}

T1();
