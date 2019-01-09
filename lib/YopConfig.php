<?php

class YopConfig{
    //app config

    public $serverRoot = "https://openapi.yeepay.com/yop-center";
    public $yosServerRoot = "https://yos.yeepay.com/yop-center";

    //public $serverRoot = "http://ycetest.yeepay.com:30228/yop-center";
    //public $yosServerRoot = "http://ycetest.yeepay.com:30228/yop-center";

    //public $serverRoot = "http://127.0.0.1:8064/yop-center";
    //public $yosServerRoot = "http://127.0.0.1:8064/yop-center";

    public $appKey;
    public $aesSecretKey;
    public $hmacSecretKey;

    public $debug=false;

    public $connectTimeout=30;
    public $readTimeout=60;

    public $maxUploadLimit=4096000;

    //加密算法
    public $ALG_MD5 = "MD5";
    public $ALG_AES = "AES";
    public $ALG_SHA = "SHA";
    public $ALG_SHA1 = "SHA1";

    // 保护参数
    public $ENCODING = "UTF-8";
    public $SUCCESS = "SUCCESS";
    public $CALLBACK = "callback";
    // 方法的默认参数名
    public $METHOD = "method";
    // 格式化默认参数名
    public $FORMAT = "format";
    // 本地化默认参数名
    public $LOCALE = "locale";
    // 会话id默认参数名
    public $SESSION_ID = "sessionId";
    // 应用键的默认参数名 ;
    public $APP_KEY = "appKey";
    // 服务版本号的默认参数名
    public $VERSION = "v";
    // 签名的默认参数名
    public $SIGN = "sign";

    // 商户编号
    public $CUSTOMER_NO = "customerNo";

    // 时间戳
    public $TIMESTAMP = "ts";
    public $publicED_KEY=array();

    public function __construct(){
        array_push($this->publicED_KEY,$this->APP_KEY, $this->VERSION, $this->SIGN, $this->METHOD, $this->FORMAT, $this->LOCALE, $this->SESSION_ID, $this->CUSTOMER_NO, "", false, $this->TIMESTAMP );
    }

    public function __set($name, $value){
        $this->$name = $value;

    }
    public function __get($name){
        return $this->$name;
    }

    public function getSecret(){
        if(!empty($this->appKey) && strlen($this->appKey) > 0){
            return $this->aesSecretKey;
        }else{
            return $this->hmacSecretKey;
        }
    }

    public function ispublicedKey($key){
        if(in_array($key,$this->publicED_KEY)){
                return true;
        }
        return false;
    }

}
