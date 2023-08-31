<?php

namespace Yeepay\Yop\Sdk\V1\Util;

use Yeepay\Yop\Sdk\V1\YopConfig;

abstract class FileUtils {

    /**
     * 将下载内容存储到本地临时文件，返回文件句柄
     *
     * @param $response string|resource 响应体
     * @throws \Exception
     */
    public static function saveFile($response)
    {
        $tmpFile = tmpfile();
        if (is_string($response)) {
            fwrite($tmpFile, $response);
        } else if (is_resource($response)) {
            stream_copy_to_stream($response, $tmpFile);
            fclose($response);
        } else {
            throw new \Exception("invalid response" . $response);
        }
        fflush($tmpFile);
        if (YopConfig::$debug) {
            print_r(stream_get_meta_data($tmpFile)['uri']);
        }
        return $tmpFile;
    }

}
