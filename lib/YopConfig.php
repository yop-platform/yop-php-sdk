<?php

class YopConfig{
    //public static $serverRoot = "https://openapi.yeepay.com/yop-center";
    //public static $yosServerRoot = "https://yos.yeepay.com/yop-center";

    //public static $serverRoot = "http://ycetest.yeepay.com:30228/yop-center";
    //public static $yosServerRoot = "http://ycetest.yeepay.com:30228/yop-center";

    public static $serverRoot = "http://127.0.0.1:8064/yop-center";
    public static $yosServerRoot = "http://127.0.0.1:8064/yop-center";

    public static $appKey;
    public static $hmacSecretKey;// 签名

    public static $debug=false;

    public static $connectTimeout=30000;
    public static $readTimeout=60000;

    public static $maxUploadLimit=4096000;

    public static $yopPublicKey="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB";

    // 保护参数
    public static $ENCODING = "UTF-8";

}
