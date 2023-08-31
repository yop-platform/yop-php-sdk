<?php

namespace Yeepay\Yop\Sdk\V1\Test;

use PHPUnit\Framework\TestCase;
use Yeepay\Yop\Sdk\V1\Util\FileUtils;
use Yeepay\Yop\Sdk\V1\YopConfig;

class FIleUtilsTest extends TestCase {

    /**
     * @test 保存文件
     */
    function saveFileTest(){
        YopConfig::$debug=true;

        $response = "abc";

        // 保存string
        FileUtils::saveFile($response);

        // 保存stream
        $response = fopen("https://open.yeepay.com", "r");

        $file = FileUtils::saveFile($response);

        print_r(file_get_contents(stream_get_meta_data($file)['uri']));

    }

}