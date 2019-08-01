## **Submail-sdk**

via [Composer](https://getcomposer.org/doc/00-intro.md)

`composer require stefein/submail-sdk`

laravel/lumen/thinkphp 5/ slim/yii 适用

````
<?php

use Stefein\Submail;

/*
*   @sign_type  为 md5 sha1 或者 normal 
*   @config   =   array('appid','appkey','sign_type');
*   以上为基本参数
*   使用模板发送 则需要加入 控制台项目名称project，如:JCB6G2 
*/

$obj = new Submail($config);
````
###### **通用接口**

1.getLog

````
/*
*   @可选参数('message','internationalsms','voice','mms','mail')
*/

$res = $obj->getLog('message'); 
````

2.getCredits

````
/*
*   @可选参数('message','internationalsms','voice','mms','mail')
*/

$res = $obj->getCredits('message'); 
````


#### **短信**

**messageSend** 

````
$obj->messageSend('130*********','【SUBMAIL】您的短信验证码：4438，请在10分钟内输入。');
````

**messagexSend** 

````
$vars   =   array('code'=>rand(100,999));
$obj->messagexSend("130*********",$vars);
````

**messageMultisend**

````
$content    =   '【Submail】您好，@var(name)，您的取货码为 @var(code)';
$data=[
    ['to'=>'130********','name'=>'****','code'=>rand(100,999)],
    ['to'=>'131********','name'=>'****','code'=>rand(100,999)]
];
$obj->messageMultisend($content,$data);
````

**messageMultixsend** 

````
//$data=[
//    ['to'=>'130********','name'=>'master','code'=>rand(100,999)],
//    ['to'=>'131********','name'=>'John','code'=>rand(100,999)]
//];
//$res    =   $obj->messageMultixsend($data);
````

#### **语音**

**voiceXsend** 

````
$vars   =   array('code'=>rand(100,999));
$obj->voiceXsend("131********",$vars);
````

**voiceMultixsend** 

````
$data=[
    ['to'=>'131********','code'=>rand(100,999)],
    ['to'=>'130********','code'=>rand(100,999)]
];
$obj->voiceMultixsend($data);
````

**voiceVerify** 

````
$obj->voiceVerify("130********",rand(1000,9999));
````

#### **邮件**

**mailSend** 

````
$res    =   $obj->mailSend(
    array(
        'to'=>'xxxx@163.com',
        'from'=>'xxxx@xxx.top',
        'from_name'=>'调查',
        'reply'=>'Hsia@xxxx.top',
        'cc'=>'xxxx@xxxx.top',
        'bcc'=>'xxxx@xxx.top',
        'text'=>'xxxx',
        'html'=>'xxxx',
        'subject'=>'xxxx',
        'tag'=>'xxxx',
        'vars'=>array('name'=>'xxx'),
        'links'=> array('mail'=>'xxxxx','account'=>'xxxxx'),
        'attachments'=>array('/Users/submail/submail-sdk/tests/a.png','/Users/submail/submail-sdk/tests/a.png')
    )
);
````

**mailXsend** 

````
$res    =   $obj->mailXsend(
    array(
        'to'=>'xxxx@163.com,xxxx@qq.com,',
        'from'=>'xxxx@xxx.top',
        'from_name'=>'xxxx',
        'reply'=>'Hsia@xxxx.top',
        'cc'=>'xxxx@xxxx.top',
        'bcc'=>'xxxx@xxx.top',
        'text'=>'xxxx',
        'html'=>'xxxx',
        'subject'=>'xxxx',
        'project'=>'xxxx',
        'tag'=>'xxxx',
        'vars'=>array('name'=>'xxx'),
        'links'=> array('mail'=>'xxxxx','account'=>'xxxxx')
        )
    )
);
````

#### **国际短信**



[Changelog](./CHANGELOG.md)

[LICENSE](./LICENSE)



