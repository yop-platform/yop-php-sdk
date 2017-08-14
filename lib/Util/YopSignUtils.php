<?php

require_once("Base64Url.php");
require_once("AESEncrypter.php");

/**
 * User: wilson
 * Date: 16/7/7
 * Time: 17:33
 */
abstract class YopSignUtils
{

    /**
     * 签名生成算法
     * @param array $params API调用的请求参数集合的关联数组，不包含sign参数
     * @param array $ignoreParamNames 忽略的参数数组
     * @param String $secret 密钥
     * @param String $algName 加密算法
     *
     * md2
     * md4
     * md5
     * sha1
     * sha256
     * sha384
     * sha512
     * ripemd128
     * ripemd160
     * ripemd256
     * ripemd320
     * whirlpool
     *
     * @return string 返回参数签名值
     */
    static function sign($params, $ignoreParamNames = '', $secret, $algName = 'sha1')
    {
        $str = '';  //待签名字符串
        //先将参数以其参数名的字典序升序进行排序
        $requestparams = $params;

        ksort($requestparams);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($requestparams as $k => $v) {
            //查看Key 是否为忽略参数
            if (!in_array($k, $ignoreParamNames)) {
                //为key/value对生成一个keyvalue格式的字符串，并拼接到待签名字符串后面

                //value不为空,则进行加密
                if (!empty($v)) {
                    $str .= "$k$v";
                }

            }

        }
        //将签名密钥拼接到签名字符串两头
        $str = $secret . $str . $secret;
        //通过指定算法生成sing

        return hash($algName, $str);
    }


    /**
     * 签名验证算法
     * @param array $result API调用的请求参数集合的关联数组，不包含sign参数
     * @param String $secret 密钥
     * @param String $algName 加密算法
     * @param String $sign 签名值
     * @return string 返回签名是否正确 0 - 如果两个字符串相等
     */
    static function isValidResult($result, $secret, $algName, $sign)
    {
        $newString = $secret . $result . $secret;

        if (strcasecmp($sign, hash($algName, $newString)) == 0) {
            return true;
        } else {
            return false;
        }

    }

    static function decrypt($source, $private_Key, $public_Key)
    {

        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_Key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        extension_loaded('openssl') or die('php需要openssl扩展支持');


        /* 提取私钥 */
        $privateKey = openssl_get_privatekey($private_key);

        ($privateKey) or die('密钥不可用');


        //分解参数
        $args = explode('$', $source);


        if (count($args) != 4) {
            die('source invalid : ');
        }

        $encryptedRandomKeyToBase64 = $args[0];
        $encryptedDataToBase64 = $args[1];
        $symmetricEncryptAlg = $args[2];
        $digestAlg = $args[3];

        //echo $encryptedRandomKeyToBase64;


        //用私钥对随机密钥进行解密
        openssl_private_decrypt(Base64Url::decode($encryptedRandomKeyToBase64), $randomKey, $privateKey);


        openssl_free_key($privateKey);

        $encryptedData = openssl_decrypt(Base64Url::decode($encryptedDataToBase64), "AES-128-ECB", $randomKey, OPENSSL_RAW_DATA);
        //echo $encryptedData;


        //分解参数
        //String sourceData = StringUtils.substringBeforeLast(data, "$");
        //String signToBase64 = StringUtils.substringAfterLast(data, "$");

        $signToBase64 = substr(strrchr($encryptedData, '$'), 1);

        $sourceData = substr($encryptedData, 0, strlen($encryptedData) - strlen($signToBase64) - 1);

        $public_key = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($public_Key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";


        $publicKey = openssl_pkey_get_public($public_key);


        //$sourceData = '{"orderId":"RIA17081181822735","requestFlowId":"12345678","authType":"IDCARD","authInterfaceType":"LOCAL","authInterfaceId":41,"status":"SUCCESS","fee":0.80,"cost":0.00,"invokeRecords":[{"orderId":"RIA17081181822735","authType":"IDCARD","authInterfaceId":41,"authStatus":"SUCCESS","fee":0.80,"cost":0.00,"elapsedTime":2}],"name":"张文康","idCardNumber":"370982199101186691"}';
        //$signToBase64 = 'Afc615iQCFKggZ2hrscqGqRtX_IGLLdabDkScO00_b1Pv7hr_rEfVhm48d1mrm-lNalTAaBKY5qUi0eHew44An83PtQcCc7b-8LuOwB7Qre_Cl9_nfzvCVuLCSWBxsuKU2nghZAgi7Tc25Q8plEnugy_hN3LLTMJ20hdlhYhxM67tckmSgOWNob_M2k7ksUJ28spd7sWW__A_5BiJRsPc91ftwHvNS5TGtwMEi3sOcuE5d9E8FmtWqjcOEK4XqFmAkVoiLY1Lwqu7S1pIZLbwJIZFyVTwPrtTbGFXS2yjRm7LgRHDKUgp6uJBxmHPts6acY9O77F7a9EmTPuxyLHEQ';

        $res = openssl_verify($sourceData, Base64Url::decode($signToBase64), $publicKey, $digestAlg); //验证

        //echo "verify sig result:".$res; //输出验证结果，1：验证成功，0：验证失败

        openssl_free_key($publicKey);

        if ($res == 1) {
            return $sourceData;
        } else {
            Die("verifySign fail!");
        }
    }


}

//YopSignUtils::decrypt('J13cFxsBx2opUounLAAxJnf94wQKMqqeVJp3BqXtNLBqZUXeYsS-HmVzeBFcgRz1aSpAVE6Nu05rFs8skPJ7iHGEeca_xcpWULHSg5cGnmQNb7QTcGG8hrxB-ZWgCxLGZBO9-vyU20QZa0gPT_WIc_GNCwlmqFf5p4WW2Fz-fXZ0Q9bOwAEgaCnNRipJcfN0qsNeoHfbrkwbcUaOx2LJmwmCJ0JMBdMzM9Pny8tEG0dEhqoepi1UNXnbrrFl5tYQwheVHO9SqaCQIgPPUy2esTDFtdHvxrcaB4Wwapx4cDF5wnQhv6PEHS75SGyrKW5RvbVxAkApWDmYg5U4WbIkSQ$JKCEs0Ztk7LBWxmuaxrHdcePwUGBpksa4rGWj0R4Al_hDE4ZoDrEcUx4_B5qoMJ0eGKcLTyXXI459ke1v54EThn7UCuloyWo5u_1LGiG-FVUGcFIN1mh7taPcuBDeiZfU_TAdWANIrqxnTJbqDz-gwM4XJEBFkZP6DST4bkkunkafXyjS4cCfkGwuZXm8E84Sm-p8Etn-NcKcNq-hI7NYBTjff26AV5g6ZsaPwGW_jdCNVNZuYQb4tL7FqUQi-bEaE0MuRuuTBUYfXIlccSbhaqbD5xna3bNNa45gOFq3huV2dIiJeXxg-sUcm5ySJIm-Yei7UDjR6W63SEwlXWt4h0s5EWPCyvNrNHmZbcoIGmxGP86tP_R1YZGGsNPrwu0arGXr5OCX17cexfyIlcJENvY813ksmGtBcM5qqGOE5HwcYNkmquRJwkhm0AB5uPWoxh70aCTDf9-lJtb3Q-FeJdziMHkSehBDZifX_mj8Q7ek8jEIN3ze99cFNyIujzJKY7BVjlpIQ9e9Ob6OVHCP97KcIoXvPiaSYYyN1V_MqE3MQbQ_Uv6Betmz_KYj-M0Vvu65L37URnfgJ3FzAmzY5ZAtbi8_P6zG68nEgaF76ViJ5vFpBcwzQFETC1tKmh4rpAaLRJgV86QAYFfgSCDuh3Pio85Yk7GJ4cMfAJtv_KGY3MYkm78kvMQIDp4gWwCA_0B_k-MUfpalMLrl1GvKTBQXBDD3iDiSOPh6IV9kv3sTzAv2VWpZSqB056j856U0vDmaDBZzxtQOYrVHgRW949qDRnN5x7QPa7BvbLqKzHrWLwJuVc__3SwYu5bKht4a6TM6t6r6yQ6tGcnVR11gE7u5tHZ9k-d8oPKJqEgTcnMP7K-RNgegu_qUL9vJdSC$AES$SHA256','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');
//echo YopSignUtils::decrypt('UcX0j2pzSXBCIFefBTO09Bka8S3mG6nLzuT7GnMZZKNGKL9mj7TKJZmnCSahUnsEYf_rsGi_KqTuM1w0g9y-fbkLW1epCQYCyxelxqgjlRwyGQDB6XzG4yJKTI9GwXaxolbj63PUtU8Gi6BBmTOoAtldHWtlcuX_tQI-pcUpzXl0I7VxdApUXD6ya8I9ByhQXhCm5gwO5Fn0Apv9Fh67AL1kkhye8A7_0lCzKEkHzpMkGAtVw6mCoOJw1x5Bj48vRLUikCxRtp4lOqePPa2KK5X5AkOBZiieGiPCBfRJvhGX6P5HK57zzeKaa_fu1To30fx83ajcPcj_MldOFEhckA$uyOQyTvXEOg0zIPzy8UnuWtgeIuFCkr_NLPmS8C1yVkcEpqPtyXs5BFjVt5tF51g0LZ4fvmpZc2PQGuEbBzdl3ojP5SvKkUXYaVXzXR8HqCQoIHVwaTl4SUl86JzJsx8hR0mFB_ViswxdWYk1vUuiiid17LCEOV-R6ZP360bs8Lhu9JdgR--EcYiLnZ-tOQ2GSFFN2zmiAUyTlk_jKXccrvXxEoM2J81DGVvFOLb29SYALs5o8NmRaMboUBx0azoylLxWVTbPudfUrBDlm_YmJvgtZJbl0hTv8bSKWMr7y_aMqjVjKMRY6ThbT-yY8xsS2KQLHQS6i2ed5O1kb3DZ1vRgzDcmnWNSb7rVLcJMj-TpeG47bj2f7-Bw3IBS_7YxOBpqyBUfhoCcAeCK8uY5AyHl6KdpMFUo6--RE_h1W2TUmlFarPmXNX40BIgSsEy15Llrp156uuqArTy06keusNjRJ5sUy6h_P6DT8DDhhef96F7AYwWi8a6R5dhNfq2hBs5-OaK2AUCRgMtys46o8BDN4rWtkeAYhnARD8dylvnRKhocI3xqp6tg7AVdCxcbEf1GypnwgOOuTVRxFLpzE7_7mUDWFFZIAYSE_gD1bTFUHwSrb_sqA5eNdimxHuZfbfPgS9nca0he1QVRFGtRp8hKe0nFMb68Iu3p5WEJuoflAu-lliWhl-KyBNOb4z0xOrACxsnrpiG4TrNehkHtgAjzkh5iRbzQTK87lAzpdGhqPv_ADLzr3iFcardOOkEmkVkiRyIhgXMuDMancsNVX_V7HOZLpLM-0r4n-9wVCk-QqIeyFtknpqrDkYjlZSjg1badf19c8-7DYLLFtJrmkTp0gkDZN0Y-LDf-AxbsvoMMdndE9DqGF9Li4Y_8OMwEjfIEmiho_XC4gviBVX6R8ZB-zErDqa0-U_QgmSu209rjy40CGNMNZaOeMUXDHVkuZvmivAPeYMaBi0iEi2RA0Xu2sf2VjoLXg0pVekGKMojHDsLm3y66hmcJaYCJ-VW7ONv0gMAhA249oR8okfaNjcU5RhjbZuTSMS-Rcw16vEgV3fP9lNEaqKhwB-ctZrR0kcGYDQ2ZE1i55RSOMNgWJfVmChfcvdoeSqbfzYjO0CFnVAJM-yVK4Mcn-cdQ0KA06wLJASvUHqhRGhUN4ulY1txTlznZXsnPu2nhoXLRw1Y6KcQgMB6qYbL5k50mGPKuOO0ABC3OygbWwrdfzX32LNk07BHJo99wTZxKybQ71idBy5FPsIZVMqP0e_xSeH-XMmTaBH6M2TdkH9gLJFTY9aqIpMd4pAQYmbTUiZYA8z-BkqZKz32sepkOw7MQ4jf4SLkOZGW0lj2njZqE6vkiLGVUx3jJVr3yWsxz0JTxhHDeaHZoW9sMNTycJk4UAg5CFvz3on20AVpErsXt7O2_80GgEBffGd5VDWCUjsxQh3SYhYPtcwk_vukosaNW2x5bn_fB1B55kTVyO9V29c1IWBvSeMmk3Zo7gdA6UKCv7frkxVqHiRdu7oZinxU8pM48aqWXk-I8iuiufgjTPwmmvpY8Zj_MOi7j7BlWyrz40XbjLuqnewDMY4Lb1JQL80Sv5yfgerPTqrY2bhw1S69PCF8Xsb1DdU2egTzkNBsBfVzyptiZjsXzdjYe6bviLUhQQgsEkLhNhRuxaxckTXJZspo1GvZHt1u9qdZ-1BEtLtnWVSV5ZvCAZ7eeSS8h89JY-erl-gVRT3qjstaNANYWlyJd3Re-fad2pAnUj_IB3LkgDyOXBZvO8j5RvHRrdf2ihM-u2S15X4F5-5CBFiciuOYvFijiuq0BM2ieY2WJMg3DMI_VtBbfxuynMGc3ayH0pBDebQ7FK7NrrPbjvJVrvcpRy5kpSfxoPsjiUZgdeb-CaBBMtmmL1yeDatcRa2JghqYOsXYLjQ8XEzDiygV5Z-F8QwLKyaAj7neyegVJ96dmvPv94_Ku8_prb6p3-ZpjHpe_e1h1qUM-q0nVKOVKSz8j36IwjmzwHF6q4tempImRjmmxMiJvSi1cvOPCJVzoyY9EB1DHgtbw6Q6jzA-ngiTyZD9K0HZ6u-e4iHOhWeWg6VPF_DE77MCdiNz5jlgt7XY7u99kyg_nYBhHKjcrQJU0Kd2Q4UiPh02hsiqx5LKV7VXKPz4-0ANEizUKrdorNaK8k95bnGesoR1ebdx4TbsjpPL4Xx-bChdn8BLcmKPkuKB4vzEpKmup01FQkQhHKzVtNoWpbmeITkUsp1pc5Fcxn6MyMptOA8z8qXR6X2VYuEbBgEi_Atv5gtl9LgFX3qZoLubHxxYw6_LtmeNTLzCHro4oElloCgyZw9A6DFe3C2id6cuyZFaqVjK26WSB40gokffnvk5cWhk7MfVKegNvkeC0OOiyQbRJUwONqi4uymt_GA5NpcEjVrgbVWAnWrJfBmmjmc_xbigvAu43KVleBLmD8Xjwrj674jM7P51BzS6V2EgdIhlK1tPZux2NhS0MN8DuOwOse2QOwW2QxXRtV605ldrRC0y2yWD_xVxbngoa3RNB6fe-_xeNu_yPIk_ZmTyJI6qlY8SvM70gEJndLwFW_mJkXurgl-LK-SndGMHYm93TEDoei0lIs81TuBK1YeiMrhMwM_ve8G_FfxrVhPLCuY43ApqwVtS-MSLQ2gywPOg2K8NIN1OY71fzJaHrZHQq4AKUimXUsJSNVoBeLDE6mVxu1V3gOEvA6JqVEVeOkLpo-VuBO770RgKqIRS6ypp__ZRoGdxFJhL6usvIwQeHaaOewisqOj-yQC0yxyMvMmpPApsVzzSsqYM59ABbXkZlfZT4S6_jWkQUqty_HUMQyOUa_8a2C3Xrm4-CNB_WEPLjpAdcDTB6yOWX_7D1vum4YZBEnXGEfDC48J945Kxq5rsQ_BOzGIeSecQS8u_zbcQF0AkcvaUTXNrHZgSLLCwA7mPnyfIRpK-2dqtVXJAaDBmorMfCHACek9AqC1Z3PIUKVKPSiCVC5TmApBgsrWvto_LwknV7X7rbQVQHARn0ux1fPMNZD5EgbU3eDqShp0yGyuC8Pih7zEClDe9TnmQtKvJ3gFo3NrB_gROMIW7u110aeghQ8Wm1HkUNz1EBYkzgHZhKDh0crDxEY8SjUYKLBw-ebCrqEpoPzbJ95I-276M7l_ynwlpTeoPKJQuddIQsEhnursT5A5AaJSFTgya6vP2BlqvAV0U8TryFeJDlOGp8dYdBXNhHL3Xk6IDfNZuhB2PN-uC1mh9-elDkj9Pv7ZU8Lmlad9CHytfS7pFhY2QW2dElcQpIpL1ki3YYY7emAqexefGI-ONXl47CzRUtTbXOlTXWb99KPhTNpzRRis6DwP9OPyvr-NzSC8_0zQQXotjiuig_DsJChzd3HEmXAlP1cGAIybY3LmYvO32-J9d8GibW1wPdHyB2VOQuYqwLKwJbAtcoEQ3H1Jnd2Pc3E1zEVtxg2GVwQSD5HmYc3DNHgRrRN46PJQMmflNdjzX6HQ4SZ-zxJ-cYJ_iVbU-YYskrq4IwD7friTfIquoPw58XXq-6d91dpwDNvveTGl_LX0tiMsmjawt9z549EL8Yvvnui_brQw0VaNhCvaUV3ig0nHlioUJRCL-dkkJz3uH9ny1sGHrJaBzkHwzx0BDvR9-_CX2ARrDFdnJiMRAwWVglv4FQWxlZ7w0vjlG5G17zNqQCMQItk4107lxWLft5HEuHENJefu9o4D3z_318-ePh6wS1n3cXhiZ_cZ4DuVQOdaWC-4_bxKv3u0s64_d_R6HS0aQlRGv5wP8NILG056BbYb8fhEhG9sKfUHbsoKUXHO8eHj33oExrcXeeR7HWa4n7u4KYbGRoPFArk-ZAejRn-whcuIPtYs1xLxomSGVMSsIvwP17o3AWpHXiJWTicUG0jOpKDQ9giqFTKcptHZAacWlcRungFTK8Xeu6R3QdbDsYA1wefGD6Dc_hJhOTBu10gselvpfJnUOCfwqYnFZ6fa28QWuwkI7V72fJPkAvZCTCG8cP8vEGlJqZww_CAyGbA_spgPtF446wlCB4DOmtfjMS-z2ATI5XYUsukCZabAYjb1hOmNzDEQe5mFra6v-8cPaeRNu7HBqmv9W9zy7oVRIpbaDJ32dVR0cc7JOWtlDkyUR6w58YH9HsPM54rMl5TYsxH9t9bECbisUfxJreExgSN_FMtp-8W_rbaysfaN-aG36HuaHp2w9nYydgVhVARmw5I8Mrsj5j9dFgqnW6vZhoHiN_0-mGBz8ssCq-T9yB5oOz2nYrgLiSBPBYZ-g4nCYq8BjI7zQ5Di7db8sSDgJp8_iC1YgfkHQMR9IThGP4spVS3cC7N97OztOFIPD_sbJxfMm0eFEiXYZTeHlOh5Dg7I9Si84DgJgDnP8vyflT1nMBRIuybYK6eXbO6gWIzGxcpIEAr8qEnamFaP4wfSbhUJqdbCr8C8pbkQLHt9HSUpQqvGlXjUp75HpczVEIX1Acm7eiPJ4Pu140JOgijylb0tvFBvl7IYtfY2zjQTOD9xSTK_NPZFy3UBrySSGdq-UqhVYSMPNXHf9fd069KnxrR6KMf-WNG4m0HGTLwuB4B5oLqhs-6YA3AixcK9fMda6nhnE69CmHzHmfmIJgDX6jisSpThylUrYN0oVsvhh1Ywg-xa2S1mFiJGnvRNH2yVDsA_ZnyyyMG77exyaL1Z37IC5egVm50QVJwfDGZcF4s0rB1Y2J69QFD64KChvtsK7Y7y3l1rOl3YWwy2dKEv5miZROmZ-G_4HJcQdQvzrZADwvESSbOuL6SWFNvF3h8NEPgZFg1OWus-7fybhOgNbXtRtCEu00tyvUsrHOn17-6tsXooMQe6rVdYRXvFxM_C3hQTKF9OexpnzWvTx8aAtGPy60uxLWYWIkae9E0fbJUOwD9mfLLIwbvt7HJovVnfsgLl6BWbnRBUnB8MZlwXizSsHVjYnr1AUPrgoKG-2wrtjvLeXWs6XdhbDLZ0oS_maJlE6Zn4b_gclxB1C_OtkAPC8RJJLQUdSGjPlvCeEpxC2eeXOmMj_LS411prmEak3KxoOSzdtD2Szf8opHLjfRrnPU6cQpuBjFB4GWF3QUYEgmRZmsyObCxknHXN0h33ZlE1V83UfQBWg-W2aSJMTUpcuSx_q2anInpOzPbTsU6zx_tSeCEvJQGagSusSK0j5JZlDADrziCrjbGTsgUzB7V1qh1Wvv-0v6wW5W-ypiDVm3P6PTr9_uk0OwMeW0ZsucyQ2UHCEIYIIi1GfrsqsS1BUO3c06HZGSSyQMWSlyebUgUVjAYtNDex-qMXmZuBm_FKa6HaURMW8jCANk79PwrQh9dh9EBHjVkcutfeZV1byFopbZmXF0TLbOOlxfi_Qta7LSMKfj8DAn0Tx4X9S_RJP6xPHuMeLVYdtA__nM7ZQTJxjix1zv4wFWXvRw8fTEh351lwNoHiaQY1fujsLmKu_ntQetPTaXWGpj7_9iN5RdVXCTDRvJ9MgPCtFco6fWWdlCh4NoCa3U5W6_6Kg0dmqq0b3y8zh6gBD3vm9eVhDdFdw9hV6Fq-CcyWFxe50OtPS8pPAk8m10nBue5ToQAPZuD4wGP1sfq9cAQ2_SnAw6-BHwkies62dTJcpWI2j2z3SlO880qL8NVDscaPKnL6eK0msFRYUKysx4511Avj3kpTf0a50JsgSHCL5Ji-MO2gpD2SULwXw3XwNF6K6HdKbbMwox3epNPSqCdxZlYxVYlQMkS80mzCs2NygcuAG1JHFNO25jCj2udPgrZ0ZVctZawGBvxBf5kITz3kXUcW9y20J6XBNJnWcjKcyX_8qfJI-HW5WHYwjGcbQt0OAfjGpPfkaaGhtVxLslXU5ZIaH24Z9AhW-DhF-MtSGYHp_My6NApz90AspfaCf0pXkkDK7HfjznbMAeg4Z79fh3Grv_6Nz$AES$SHA512','MIIEpAIBAAKCAQEAhWf5Bkq9+JsHDQkqEV8be+0Zm6AjU/6w7dw8c7iDDh3F1Q9cJkSb3MBrxD0HFQSF/Lh65Yj8U041hYi4mDs9sYLfoIZEVpXgOXd2OLsPJR/pFl32xpQddsRznMyyEsoQPPBg782dgP3Ly0QWJmfulpOzDSA6DTO3Q+aeySMiYs/VR1pr0Z4yrSvZCTyP+xFH8zys2uUxIG/LsUSsaivy9M/0WyNMG7caWc6oblWMqdcbk9wv0Ry0BRxIUGzl63tYNUf8Z1TqDpFAsG4C4+JZGSRNDCnFVAh4GcnJsRqpyDwqnaB1mbF9W+8Zoh4sOLmR+V0HjzIrB3AzS9wvIlCHFQIDAQABAoIBABPo+ZSD0ShqUroSVRH0pNBxCXJdiwg9KcDGLsuCjSStMtpiiXk4oh5nJW5LQWRUoX6fNdBOCoKQWJKOXiZyKPn2M1Ps1gQqKCXLe3xqBo+e3JW2/l6SuncASNTtA+Kj/5posb74a/pVZnX2umuO9V/JuV5LIf7YahCbObWBJd+jNiUgXYpwDB5GsosjvYoE/Limc41vmrnvpTV6s9WiipJO+P6zm4xEJqPEgFJ6QjX7NkkyN4sPvbDGB5hz/97LT3H+aAWvjEwDj4Z85irOkBnEW9BWn2vM2fLoA4cmGhROZ81SSMTzX/O7wrqXDVUyQzcMa7FIzF9QXd9tyjzqwEECgYEA7XGbClhgjLt5cuZCO4UraU07FnjIxKe+d3vBEuy3bJWtKu3AvVZ0WTsQVorbilQi4zxClkoUVku5G+uXhvljn4CZunApufLucX6ZfF20oQqik4gjmDGpvJFrfT09Z5S6ENXIKZAJXmTfgg/1tnlPT/Il2XghZt0D/4j9Pa+0OdECgYEAj9Ty8149qOteJvV0in0A1bue6MDT5L/SWxAqz9fLkk9/ChVmr8CIAtfWKUtlKEDinj/6hp+F+k4O9u+CeM9okHGGL6RQzJvjarbo5bKTTK+DH3ErAh7hwxpjuzaaP88K9R6AiTLFYpgB5DJlZKZuJZ9D4X0nflve42OBLF8IhgUCgYEA7UbwyybD3P7ff62QFFCgsAsIeA1de/+w+0/FAjdhmPX95X9PcyW5AQ5f5ku+1f38Gx414F/I8O+c3MTSWIRRRKxLcx7w46xbETmVAc3WWnP5QPrzrvw6BYFAbBfNi/v48CfibX5NjnG5VQzD24RgeKCfqDE/F77XZv2rK4Cw1nECgYBU48JgkQajZAc1xzj5Y73SZ+HqTaTCJdTpmikqcprbx7+bG/Z3VJLx2qGzzaPulh0qeWhLfGt+yANdCw9ebkuwtNAV3k0x9e/LVBkxOKxnXk9th0VzAvcMR88E970iW/iDo3UJhMWq4zx6iqP9O51W5yERPOTKVz69xkS/A3fsYQKBgQC4tYGtRYaZN5RbR9BTIoeuCJKD6qDf89xeEKLpvwIe62JVlqDW3uo7cLVOqlzV59dtuMpEC+L9NdLyb+6Fs/tOuSaT1DK8na3BOYzPWgrBPdz1sjsrRcxsVNPKU9byxMJWU0YGq5ZVUPT3w1S/Bw530SxHnheKOzEQSQZ/KXt5Bg==','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4g7dPL+CBeuzFmARI2GFjZpKODUROaMG+E6wdNfv5lhPqC3jjTIeljWU8AiruZLGRhl92QWcTjb3XonjaV6k9rf9adQtyv2FLS7bl2Vz2WgjJ0FJ5/qMaoXaT+oAgWFk2GypyvoIZsscsGpUStm6BxpWZpbPrGJR0N95un/130cQI9VCmfvgkkCaXt7TU1BbiYzkc8MDpLScGm/GUCB2wB5PclvOxvf5BR/zNVYywTEFmw2Jo0hIPPSWB5Yyf2mx950Fx8da56co/FxLdMwkDOO51Qg3fbaExQDVzTm8Odi++wVJEP1y34tlmpwFUVbAKIEbyyELmi/2S6GG0j9vNwIDAQAB');
