<?php
define('ROOT_DIR', __DIR__ . '/');
require_once ROOT_DIR . '/../vendor/autoload.php';

//加载配置文件
$configs = include ROOT_DIR . '/config/mysql.php';
foreach ($configs as $key => $config) {
    \Ruesin\Utils\MySQL::setConfig($key, $config);
}

//test reconnection
$mysql = \Ruesin\Utils\MySQL::getInstance('default');
for ($i = 10; $i > 0; $i--) {
    echo $i.PHP_EOL;
    sleep(1);
}
$mysql->query("SELECT sleep(30);");
$mysql->query("SELECT sleep(10);");
\Ruesin\Utils\MySQL::getInstance('default')->query("SELECT sleep(5);");

//从配置项中获取配置文件
$mysql = \Ruesin\Utils\MySQL::getInstance('');
$mysql = \Ruesin\Utils\MySQL::getInstance('0');
$mysql = \Ruesin\Utils\MySQL::getInstance('web');

//关闭连接
\Ruesin\Utils\MySQL::close('default');

//关闭所有连接
\Ruesin\Utils\MySQL::clear();
