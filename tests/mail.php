<?php

require('../vendor/autoload.php');

use Stefein\Submail;

$config = array(
    "appid" => "13724",
    "appkey" => "bc3732c4e9689b0a73a053d6aeae893c",
    "sign_type" =>  "md5",
    "project"   =>  "JCB6G2"
);
$obj = new Submail();
$obj->config    =   $config;



$res    =   $obj->mailXsend(
    array('to'=>'xxx@qq.com','from'=>'xxx@xx.top','from_name'=>'调查','reply'=>'xx@xx.top','cc'=>'xx@xx.top','bcc'=>'xx@xx.top','subject'=>'拎草','tag'=>'111','vars'=>['name'=>'声'],'links'=>['mail'=>'baidu.com','account'=>'xx@gg.com']
    )
);
var_dump($res);die;