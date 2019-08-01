<?php

require('../vendor/autoload.php');

use Stefein\Submail;
$config = array(
    "appid" => "20903",
    "appkey" => "7a0c147710773c613c035fb9771c7788",
    "sign_type" =>  "sha1",
    "project"   =>  "9k3Hm3"
);
$obj = new Submail();
$obj->config    =   $config;

//$res = $obj->voiceSend('xxxxxxx','【SUBMAIL】您的短信验证码：4438，请在10分钟内输入。');

//$res = $obj->getCredits('voice');

//$vars   =   array('code'=>rand(100,999));
//$res    =   $obj->voiceXsend("xxxxxx",$vars);

//$data=[
//    ['to'=>'xxxxxxx','code'=>rand(100,999)],
//    ['to'=>'xxxxxxx','code'=>rand(100,999)]
//];
//$res    =   $obj->voiceMultixsend($data);

$res    =   $obj->voiceVerify("**********",rand(1000,9999));
var_dump($res);