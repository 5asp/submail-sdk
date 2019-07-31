<?php

require('../vendor/autoload.php');

use Stefein\Submail;

$config = array(
    "appid" => "*****",
    "appkey" => "*****************",
    "sign_type" =>  "md5",
    "project"   =>  "******"
);
$obj = new Submail();
$obj->config    =   $config;



$res    =   $obj->mailXsend(array('to'=>'130******@qq.com,130******@qq.com','from'=>'130******@submail.com','from_name'=>'调查','reply'=>'130******@qq.com','cc'=>'130******@qq.com','bcc'=>'130******@qq.com','subject'=>'拎草','tag'=>'*****','vars'=>['name'=>'声']));
var_dump($res);die;