<?php
define('ROOT_DIR', __DIR__ . '/');
require_once ROOT_DIR . '/../vendor/autoload.php';

//加载配置文件
\Ruesin\Utils\Config::loadPath(ROOT_DIR.'config/');

//从配置项中获取配置文件
$mysql = \Ruesin\Utils\MySQL::getInstance('');
$mysql = \Ruesin\Utils\MySQL::getInstance('0');
$mysql = \Ruesin\Utils\MySQL::getInstance('web.online');

//关闭连接
\Ruesin\Utils\MySQL::close('default');

//指定配置的实例
\Ruesin\Utils\MySQL::getInstance('', \Ruesin\Utils\Config::get('mysql.default'));

//不指定配置的实例
$redis = \Ruesin\Utils\MySQL::getInstance();

//关闭所有连接
\Ruesin\Utils\MySQL::closeAll();
