<?php

class YopResponse{
    /**
     * 状态(SUCCESS/FAILURE)
     */

    public $state;

    /**
     * 业务结果，非简单类型解析后为LinkedHashMap
     */

    public $result;

    /**
     * 时间戳
     */
    public $ts;

    /**
     * 结果签名，签名算法为Request指定算法，示例：SHA(<secret>stringResult<secret>)
     */
    public $sign;

    /**
     * 错误信息
     */
    public $error;

    public $requestId;

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

}