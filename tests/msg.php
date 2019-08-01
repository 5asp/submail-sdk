<?php

require('../vendor/autoload.php');

use Stefein\Submail;
$config = array(
    "appid" => "33389",
    "appkey" => "151c16b18b0b74aec179a759e175a7cc",
    "sign_type" =>  "sha1",
    "project"   =>  "DrP9S3"
);
$obj = new Submail();
$obj->config    =   $config;

//$res = $obj->messageSend('13027232773','【SUBMAIL】您的短信验证码：4438，请在10分钟内输入。');

//$res = $obj->getLog('message');

//$res = $obj->getCredits('message');

//$vars   =   array('code'=>rand(100,999));
//$res    =   $obj->messagexSend("xxxx",$vars);


//$content    =   '【Submail】您好，@var(name)，您的取货码为 @var(code)';
//$data=[
//    ['to'=>'13027232773','name'=>'master','code'=>rand(100,999)],
//    ['to'=>'13027232773','name'=>'slave','code'=>rand(100,999)]
//];
//$res    =   $obj->messageMultisend($content,$data);


//$data=[
//    ['to'=>'xxxxxxx','name'=>'master','code'=>rand(100,999)],
//    ['to'=>'xxxxxxx','name'=>'John','code'=>rand(100,999)]
//];
//$res    =   $obj->messageMultixsend($data);


var_dump($res);