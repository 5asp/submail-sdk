<?php

require('../vendor/autoload.php');

use Stefein\Submail;
$config = array(
    "appid" => "****",
    "appkey" => "****************",
    "sign_type" =>  "sha1",
    "project"   =>  "****"
);
$obj = new Submail();
$obj->config    =   $config;

//$res = $obj->voiceSend('130*******','【SUBMAIL】您的短信验证码：4438，请在10分钟内输入。');

//$res = $obj->getCredits('voice');

//$vars   =   array('code'=>rand(100,999));
//$res    =   $obj->voiceXsend("130******",$vars);

//$data=[
//    ['to'=>'130******','code'=>rand(100,999)],
//    ['to'=>'130******','code'=>rand(100,999)]
//];
//$res    =   $obj->voiceMultixsend($data);

//$res    =   $obj->voiceVerify("130******",rand(1000,9999));
//var_dump($res);