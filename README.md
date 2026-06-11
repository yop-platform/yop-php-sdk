[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fyop-platform%2Fyop-php-sdk.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fyop-platform%2Fyop-php-sdk?ref=badge_shield)

易宝支付 YOP 开放平台 PHP SDK，用于对接 YOP API。

### 环境依赖

- PHP >= 5.5（兼容 PHP 7.2+）
- 扩展：**openssl**、**curl**、**json**

> **注意**：本 SDK 不依赖 mcrypt 扩展。mcrypt 在 PHP 7.1 已废弃、PHP 7.2 起已从核心移除，无需安装。

检查扩展是否已启用：

```bash
php -m | grep -E 'openssl|curl|json'
```

### 安装

```bash
composer require yeepay/yop-php-sdk
```

### 目录结构

```
├── src
│   ├── Util                        // 工具类
│   │   ├── Base64Url.php           // Base64Url 编解码
│   │   ├── FileUtils.php           // 文件下载处理
│   │   ├── HttpRequest.php         // HTTP 请求
│   │   ├── HttpUtils.php           // HTTP 工具
│   │   └── YopSignUtils.php        // 签名与数字信封解密
│   ├── YopClient.php               // 对称密钥（HMAC）请求客户端
│   ├── YopRsaClient.php            // 非对称密钥（RSA）请求客户端
│   ├── YopConfig.php               // 全局配置
│   ├── YopRequest.php              // 请求封装
│   ├── YopResponse.php             // 响应封装
│   └── YopError.php                // 错误信息
├── test                            // 测试用例
│   ├── AesTest.php                 // 对称密钥请求示例
│   ├── RsaTest.php                 // 非对称密钥请求示例
│   └── ...
```

### 接口调用说明

**对称密钥（HMAC）**：使用 `YopClient`，传入 `appKey` 与 `secretKey`。

```php
use Yeepay\Yop\Sdk\V1\YopClient;
use Yeepay\Yop\Sdk\V1\YopRequest;

$request = new YopRequest($appKey, $secretKey);
$request->addParam("key", "value");
$response = YopClient::post("/rest/v1.0/...", $request);
```

**非对称密钥（RSA）**：使用 `YopRsaClient`，传入 `appKey` 与商户私钥。

```php
use Yeepay\Yop\Sdk\V1\YopRsaClient;
use Yeepay\Yop\Sdk\V1\YopRequest;

$request = new YopRequest($appKey, $privateKey);
$request->addParam("key", "value");
$response = YopRsaClient::post("/rest/v3.0/...", $request);
```

**回调解密**：使用 `YopSignUtils::decrypt()` 解密数字信封。

```php
use Yeepay\Yop\Sdk\V1\Util\YopSignUtils;

$plainText = YopSignUtils::decrypt($cipherText, $merchantPrivateKey, $yopPublicKey);
```

更多示例请参考 `test/` 目录下的测试用例。

## License

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fyop-platform%2Fyop-php-sdk.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fyop-platform%2Fyop-php-sdk?ref=badge_large)
