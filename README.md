# MySQL-Utils
基于[Medoo](https://github.com/catfan/Medoo)的单例类，没有对`Medoo`做任何修改，仅为了方便使用而加了层单例壳。

1. 使用`setConfig($name, $config)`加载配置到静态属性`$configs`中，`$name`为连接名，`$config`为连接选项参数
2. 使用`getInstance($name)`获取指定连接名`$name`的`\Medoo\Medoo`实例
3. 获取的即为`Medoo`实例，直接[使用](https://medoo.in/doc)即可

```php
$configs = [
    'blog' => [
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
    'web' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'web',
        'username' => 'root',
        'password' => 'root',
        'port' => '3306'
    ],
];
//加载配置到静态属性
foreach ($configs as $key => $config) {
    \Ruesin\Utils\MySQL::setConfig($key, $config);
}

//获取连接实例
$mysql = \Ruesin\Utils\MySQL::getInstance('blog');

//执行查询
$mysql->query("SELECT sleep(30);");
$mysql->query("SELECT sleep(10);");
\Ruesin\Utils\MySQL::getInstance('blog')->query("SELECT sleep(5);");

//关闭连接
\Ruesin\Utils\MySQL::close('blog');

//清除所有连接
\Ruesin\Utils\MySQL::clear();
```