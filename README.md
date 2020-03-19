# wxpusher PHP 快速开发工具类
------
[![GitHub issues](https://img.shields.io/github/issues/wxpusher/wxpusher-sdk-php)](https://github.com/wxpusher/wxpusher-sdk-php/issues)
[![GitHub stars](https://img.shields.io/github/stars/meloncn/wxpusher-sdk-php)](https://github.com/meloncn/wxpusher-sdk-php/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/wxpusher/wxpusher-sdk-php)](https://github.com/wxpusher/wxpusher-sdk-php/network)
[![GitHub license](https://img.shields.io/github/license/wxpusher/wxpusher-sdk-php)](https://github.com/wxpusher/wxpusher-sdk-php/blob/master/LICENSE)

## 简介

* 一个基于PHP对 [Wxpusher](http://wxpusher.zjiecode.com) 微信推送服务的快速开发工具类。
* 完整基于 [Wxpusher 文档](http://wxpusher.zjiecode.com/docs/) 实现。
* 优化部分参数调用方式，

## 主要实现功能

> * 快速短文本信息发送
> * 标准文本格式信息，HTML以及markdown文本
> * 创建自定义二维码请求
> * 检查消息发送状态
> * 获取已关注者信息

## 注意事项
*  PHP Version >= 5.4 的系统环境下。
* 发送标准信息，创建二维码操作需启用CURL支持。

## 基本使用方法

#### 使用前准备
引入本类库文件，可自定义命名空间于您的项目中。使用前进行实例化操作。

实例化时需传入网站获取的APP_TOKEN值
```php
 <?php
 require("wxpusher.class.php");
 $Token = 'AT_xxxxxx';
 $wxpusher = new wxpusher($Token);
 
 // TODO What u want...
 
```
#### 1、快速发送消息
结构

quickSend(用户id,主题id,内容,超链接,debug模式);

* 用户id与主题id择一填写即可
* 内容为简单字符串
* 超链接可选，不使用可选择留空
* debug 为布尔型。为true时消息发送失败返回服务器提示错误信息，否则返回false。默认false

实例代码：
```
$wxpusher->quickSend('','10','Hello','http://localhost.com',false);
```


#### 2、标准信息发送消息
结构

 send(内容,消息类型,是否为用户id,ID,超链接,是否获取messageId)
 
 * 内容为字符串形式，使用 \n 换行
 * 内容类型为int类型，1表示文字 2表示html 3表示markdown
 * 是否为用户Id为bool类型，true代表id参数传入用户UID，false代表id参数传入主题ID
 * 传入单个ID可以使用int类型，多个ID请使用数组形式
 * url可为空
 * 是否返回消息ID为bool类型，如果为true则执行完毕后返回消息ID用于后期追踪。默认false。
 
 实例代码：
 
 ```
 $wxpusher->send('Hello','1','false',10,'www.google.com');
 ```

#### 3、创建参数二维码
结构

Qrcreate(参数,过期时间)

* 参数最大不可超过64位。留空则系统随机生成参数提交
* 过期时间，单位秒，默认1800
* 成功后返回多维数组
     *   | -- 'expires' 过期时间
     *  | -- 'code'     
     *  | -- 'shortUrl 生成二维码短连接
     *  | -- 'extra'   传递进的参数
     *  | -- 'url'     二维码长链接
* 失败后返回服务端提示错误消息（string）

实例代码：

```
$wxpusher->Qrcreate('wxpusher',1800);
```


#### 4、检查远程消息发送状态
结构

checkStatus(消息ID)

* 远程信息成功投递后返回 True （bool）
* 其余状态返回文本提示消息 （string）

实例代码：

```
$wxpusher->checkStatus(1122);
```

#### 5、获取关注用户详情
结构

getFunInfo(页码,每页信息数量,用户UID)

* 页码默认为1，每页信息数量默认为100
* 用户uid留空则返回所有关注着信息，填写uid则返回指定关注者信息
* 空数据返回 NULL 值
* 执行成功后结果皆为多维数组形式 （array）
* 执行失败反馈错误信息（string）

实例代码：
```
$wxpusher->getFunInfo();
```

#### 6、获取关注者总数

无需传入任何参数，执行成功返回结果为int类型

出现错误返回远程服务器提示信息 （string）

实例代码：
```
$wxpusher->getFunTotal();
```


wxpusher-sdk-php遵循Apache2开源协议发布，并提供免费使用。

[2020][Meloncn]


