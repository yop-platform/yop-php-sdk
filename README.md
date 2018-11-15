###########环境依赖
php v5.5+
安装php的mcrypt扩展 openssl

###########部署步骤
1. 添加php的mcrypt扩展 openssl


###########目录结构描述
|
├── lib                         // 函数库
│   ├── AESEncrypter.php        // AES函数集
│   ├── Base64Url.php           // Base64Url函数集
│   ├── BlowfishEncrypter.php   // 加解密处理
│   ├── HttpRequest.php         // Http请求函数集
│   ├── HttpUtils.php           // Http处理共通函数集
│   ├── StringBuilder.php       // 字符串创建函数集
│   ├── StringUtils             // 字符串处理函数集
│   └── YopSignUtils.php        // YOP签名共通函数集
├── YopClient.php               // 对称秘钥请求处理函数集
├── YopClient3.php              // 非对称秘钥请求处理函数集
├── YopConfig.php               // YOP配置信息函数集
├── YopRequest.php              // YOP请求处理函数集
├── YopResponse.php             // YOP返回处理函数集
├── test                        // 测试
│   ├── info.php                // PHP环境测试页面
│   └── Test.php                // 测试程序页面
├── Readme.txt                  // help



POST /yop-center/rest/v1.0/paperorder/api/pay/query HTTP/1.1
Accept: text/plain, application/json, application/*+json, */*
Content-Type: application/x-www-form-urlencoded
User-Agent: Java/1.8.0_91
Host: 10.151.30.88:8064
Connection: keep-alive
Content-Length: 233
```

```
0e65894ea9f91213f6991a5c81fa23b4ed1345e13f31d0fb43d0a1831760a3f7
```

```
0owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846TcustomerNo10040020578formatjsonlocalezh_CNmerchantno10040020578method/rest/v1.0/paperorder/api/pay/queryrequestnoYOP-SDK-1471601751370ts1471601751342v1.00owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846T
```

```
format=json
locale=zh_CN
ts=1471601751342
customerNo=10040020578
sign=0e65894ea9f91213f6991a5c81fa23b4ed1345e13f31d0fb43d0a1831760a3f7
encrypt=C%252FG0UURN6jufCiZP0aeDgcSkdpQHXn8JqlIrxxBsrW1LOLTTmWpXSUHOpcDoBWda2WBzJYEBH
```


```
0owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846TcustomerNo10040020578formatjsonlocalezh_CNmerchantno10040020578method/rest/v1.0/paperorder/api/pay/queryrequestnoYOP-SDK-1471601751370ts1471601751342v1.00owN80Vs39386sSSi7B76wa7497P41gZ3G4b8971V8R8sc6lS7ns4FA2846T
0004b49b668fba3e3c8d88bce978419227468281646b9c03ea174b2158c7ad18
```

```
appKey=
format=json
locale=zh_CN
ts=1471599539977
customerNo=10040020578
sign=0004b49b668fba3e3c8d88bce978419227468281646b9c03ea174b2158c7ad18
encrypt=jqIh5ikW6033NV6jQ9o5XJaOJ5czDmd2MiOUkTtoBeyOaI7lglVF58bh1LEuRqoxMpWKooEUYRnDD6jOe28TvQ%253D%253D
```

```
{
  "state" : "SUCCESS",
  "result" : "HTg9s/K7xqECF5KspdG6wvLiEICimhmB3hOn78tWbUsmSYQ5pBeQB9Z3AazmXb/3RcHeEAacaQyd4RnlPNNV8K+9oBUjPR+Ux47H34M46UBkb9Y8VcIK8Dhr2WVMIOp/sjG8vXuiqzoybqq7RZUExcCYK0k8DqWLqkGOC90TU8a+4DiQGuLG0mkx3XhRg60zD9AvJsZO3n+Dclk7EsXvE5uJ96gPIM8NsmiI0PKYndfM70sEUYgjZNsj7LEcCTfn7FXB+hXVL7FzuvSUnRHlGQjH8btkp7tV2TCMjKImcBvU1WIQp2pqqwR864aaDQ2wZfEAWVdb2NnNoycHbw==",
  "ts" : 1471599541473,
  "sign" : "ab7f48bcc3b91d06b1108ac3458caac460c3f3d4918202e04b59ea13d3127035"
}
```
