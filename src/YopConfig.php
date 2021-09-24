<?php

namespace Yeepay\Yop\Sdk\V1;

class YopConfig
{
    public static $serverRoot = "https://openapi.yeepay.com/yop-center";
    public static $yosServerRoot = "https://yos.yeepay.com/yop-center";

//    public static $serverRoot = "http://ycetest.yeepay.com:30228/yop-center";
//    public static $yosServerRoot = "http://ycetest.yeepay.com:30228/yop-center";

    //public $serverRoot = "http://127.0.0.1:8064/yop-center";
    //public $yosServerRoot = "http://127.0.0.1:8064/yop-center";

    public static $appKey;
    public static $hmacSecretKey;// 签名

    public static $debug = false;

    public static $connectTimeout = 30000;
    public static $readTimeout = 60000;

    public static $maxUploadLimit = 4096000;

    public static $yopPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB";

//    public static $yopPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4g7dPL+CBeuzFmARI2GFjZpKODUROaMG+E6wdNfv5lhPqC3jjTIeljWU8AiruZLGRhl92QWcTjb3XonjaV6k9rf9adQtyv2FLS7bl2Vz2WgjJ0FJ5/qMaoXaT+oAgWFk2GypyvoIZsscsGpUStm6BxpWZpbPrGJR0N95un/130cQI9VCmfvgkkCaXt7TU1BbiYzkc8MDpLScGm/GUCB2wB5PclvOxvf5BR/zNVYywTEFmw2Jo0hIPPSWB5Yyf2mx950Fx8da56co/FxLdMwkDOO51Qg3fbaExQDVzTm8Odi++wVJEP1y34tlmpwFUVbAKIEbyyELmi/2S6GG0j9vNwIDAQAB";

    // 保护参数
    public static $ENCODING = "UTF-8";

}
