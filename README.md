# MySQL-Utils
基于[Medoo](https://github.com/catfan/Medoo)的MySQL工具类。

## 依赖
- [catfan/medoo](https://github.com/catfan/Medoo)
- [ruesin/utils](https://github.com/ruesin/utils)

## 使用
使用`\Ruesin\Utils\MySQL::getInstance($key,$config)`获取`\Medoo\Medoo`实例。
- 参数`$key`可选，如果有值，则获取Config配置项中`mysql.$key`的配置。
- 参数`$config`可选，如果有值，优先使用此配置。
- 如果`$key`和`$config`都没有值，则会从`Config::get('mysql')`中获取第一个一维数组。即`mysql`如果是一维数组则直接用`mysql`配置，否则取第一个数组。 


使用`\Ruesin\Utils\MySQL::close($key,$config)`关闭指定连接。

使用`\Ruesin\Utils\MySQL::closeAll()`关闭全部连接。