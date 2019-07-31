<?php

require('../vendor/autoload.php');

use Stefein\Submail;
$config = array(
    "appid" => "******",
    "appkey" => "******",
    "sign_type" =>  "sha1",
    "project"   =>  "******"
);
$obj = new Submail();
$obj->config    =   $config;

//$res = $obj->messageSend('130********','【SUBMAIL】您的短信验证码：4438，请在10分钟内输入。');

//$res = $obj->getLog('message');

//$res = $obj->getCredits('message');

$vars   =   array('code'=>rand(100,999));
$res    =   $obj->messagexSend("130********",$vars);


//$content    =   '【Submail】您好，@var(name)，您的取货码为 @var(code)';
//$data=[
//    ['to'=>'130********','name'=>'master','code'=>rand(100,999)],
//    ['to'=>'131********','name'=>'slave','code'=>rand(100,999)]
//];
//$res    =   $obj->messageMultisend($content,$data);


//$data=[
//    ['to'=>'130********','name'=>'master','code'=>rand(100,999)],
//    ['to'=>'131********','name'=>'John','code'=>rand(100,999)]
//];
//$res    =   $obj->messageMultixsend($data);


var_dump($res);