<?php
define("ROOT",dirname(__DIR__)."/");

//引入框架autoload类
include ROOT .'autoload.php';

//引入config文件
$params=include( ROOT . 'config/config.php');
defined("CHATENV") or define("CHATENV",getenv('CHATENV'));
defined("PARAMS") or define("PARAMS",$params);
//引入入口文件
include ROOT.'web/websocket.php';

$im=new websockets\websocket();
$im->run();