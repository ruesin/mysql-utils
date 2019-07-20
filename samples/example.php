<?php
require_once __DIR__ . '/../vendor/autoload.php';

$configs = [
    'default' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'blog',
        'username' => 'root',
        'password' => 'root',
        'port' => '3306',
        'retry_exception' => [
            // 'MySQL server has gone away'
        ]
    ],
    '' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'blog',
        'username' => 'root',
        'password' => 'root',
        'port' => '3306'
    ],
    '0' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'blog',
        'username' => 'root',
        'password' => 'root',
        'port' => '3306'
    ]
];

//加载配置文件
foreach ($configs as $key => $config) {
    \Ruesin\Utils\MySQL::setConfig($key, $config);
}

//test reconnection
$mysql = \Ruesin\Utils\MySQL::getInstance('default');
for ($i = 10; $i > 0; $i--) {
    echo $i . PHP_EOL;
    sleep(1);
}
$mysql->query("SELECT sleep(30);");
$mysql->query("SELECT sleep(10);");
\Ruesin\Utils\MySQL::getInstance('default')->query("SELECT sleep(5);");

//从配置项中获取配置文件
$mysql = \Ruesin\Utils\MySQL::getInstance('');
$mysql = \Ruesin\Utils\MySQL::getInstance('0');

//关闭连接
\Ruesin\Utils\MySQL::close('default');

//关闭所有连接
\Ruesin\Utils\MySQL::clear();
