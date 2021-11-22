## 3.1.10

[fix]非文件传输的post请求，用 application/x-www-form-urlencoded

## 3.1.9

[fix]请求时间格式化为ALTERNATE ISO8601(UTC)

## 3.1.7

[fix]配置读取修复

## 3.1.5

[fix]对称加密日期格式化

## 3.1.3

[feat]返回结果验签

## 3.1.2

[fix]失败请求状态封装

## 3.1.1

[feat]支持文件下载
[feat]默认使用SHA256签名算法
[fix]优化header

## 3.1.0

[feat]/rest/v1.0/file/upload 已经废弃，老商户升级sdk时文件上传添加参数由 addParam 改为 addFile
[fix]AES请求传递Authorization
[fix]uuid 生成规则变更