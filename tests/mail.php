<?php

require('../vendor/autoload.php');

use Stefein\Submail;

$config = array(
    "appid" => "13724",
    "appkey" => "bc3732c4e9689b0a73a053d6aeae893c",
    "sign_type" =>  "md5",
//    "project"   =>  "JCB6G2"
);
$obj = new Submail();
$obj->config    =   $config;

$res    =   $obj->mailSend(
    array(
        'to'=>'cyanxxj@163.com',
        'from'=>'hsia@huechin.top',
        'from_name'=>'调查',
        'reply'=>'Hsia@huechin.top',
        'cc'=>'Hsia@huechin.top',
        'bcc'=>'Hsia@huechin.top',
        'text'=>'烟台市',
        'html'=>'正文',
        'subject'=>'拎草',
        'tag'=>'111',
        'vars'=>array('name'=>'声'),
        'links'=> array('mail'=>'baidu.com','account'=>'xx@gg.com'),
        'attachments'=>array('/Users/submail/submail-sdk/tests/a.png','/Users/submail/submail-sdk/tests/a.png')
    )
);
var_dump($res);die;