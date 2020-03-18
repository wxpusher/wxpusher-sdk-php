# wxpusher-sdk-php
------
## What's the wxpusher-sdk-php？

* 一个基于PHP针对于 Wxpusher（http://wxpusher.zjiecode.com） 微信推送服务的快速开发工具类。
* 完整基于 Wxpusher 文档（http://wxpusher.zjiecode.com/docs/） 实现。
* 优化部分参数调用方式，

## 如果它可以帮助你，希望你能点亮一颗Star，谢谢。

## 主要实现功能

### 快速发送消息（基于GET接口）
```
	$wxpusher->quickSend('用户id','主题id','内容','url');
		成功返回true
        失败返回错误信息
```

### 标准消息发送（基于Post接口）
```
	send($content = null,$contentType = 1,$isUids = true,$array_id = [],$url = '',$getMessageId = false)

		$contentType:
    	       1表示文字
    	       2表示html
    	       3表示markdown
    	 
    	  $isUids:
    	       true    发送到用户
    	       false   发送到主题
    	 
    	  $url 需要添加协议头 http://或https://
    	 
    	  $getMessageId:
    	       true    执行完毕后返回messageId和错误信息   array
    	       false   执行完毕后仅返回错误信息，若无错误返回TRUE array bool
    	 
    	  $array_id:
    	       array
    	       string int 单条数据可使用字符串或者整数，自动转化为数组


    	  如果需要输出MessageId
    	  		执行成功后无错误返回id	（array）
    	  		有错误输出错误信息和已执行成功ID （array）

    	  如果无需输出id
    	  		执行全部成功返回 True （bool）
    	  		出现错误输出错误信息 （array）

```
### 检查消息发送状态
```
	$wx->checkStatus( 信息ID（messageId） );
		信息查询状态
        信息发送成功返回 True
        其余状态返回服务器提示信息(msg)
```

### 返回关注用户信息
```
	$wx->getFunInfo(页码,每页数量,UID)
		获取关注用户信息
       $uid 默认为空，返回所有关注用户数据    多维数组
            输入uid，输出指定用户信息        多维数组
     
      远程服务器执行返回success继续执行
       否则返回远程服务器错误信息
     
      查询数据
           得到数据返回多维数组
           空数据返回 NULL
```
### 已关注用户总数
```
	$wx->getFunTotal()
		返回所有关注用户总数（int）
```

## 更加详细方法使用请见Wxpusher.class.php内注释部分

## 应用开源
wxpusher-sdk-php遵循Apache2开源协议发布，并提供免费使用。
版权所有[2020][Meloncn]


