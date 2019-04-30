<?php

class YopError {
    public $code;
    public $message;
    public $subCode;
    public $subMessage;

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function __get($name){
        return $this->$name;
    }

}
