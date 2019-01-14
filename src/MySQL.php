<?php

namespace Ruesin\Utils;

/**
 * Class MySQL
 *
 * @see \Medoo\Medoo
 * @see https://medoo.in/doc
 * @see http://www.php.net/manual/en/pdo.setattribute.php
 */
class MySQL
{
    private static $_instance = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return \Medoo\Medoo | bool
     */
    public static function getInstance($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);
        $name = self::configToName($config);
        try {
            if (empty(self::$_instance[$name])) {
                self::$_instance[$name] = self::connect($config);
            } else {
                if (self::ping(self::$_instance[$name]) !== true) {
                    self::clearInstance($name);
                    self::$_instance[$name] = self::connect($config);
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        return self::$_instance[$name];
    }

    private static function connect($config)
    {
        if (!isset($config['host']) || !$config['host']) {
            throw new \Exception('Has not host!');
        }

        if (!isset($config['database']) || !$config['database']) {
            throw new \Exception('Has not database!');
        }

        if (!isset($config['port']) || !$config['port']) {
            $config['port'] = 3306;
        }

        if (!isset($config['driver'])) {
            $config['driver'] = 'mysql';
        }

        return new \Medoo\Medoo([
            'database_type' => $config['driver'],
            'database_name' => $config['database'],
            'server' => $config['host'],
            'port' => $config['port'],
            'username' => isset($config['username']) ? $config['username'] : '',
            'password' => isset($config['password']) ? $config['password'] : '',
            'charset'  => empty($config['charset']) ? 'utf8mb4' : $config['charset'],
            'prefix'   => isset($config['prefix']) ? $config['prefix'] : '',
            'option' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //PDO::ATTR_CASE => PDO::CASE_NATURAL,
                //PDO::ATTR_PERSISTENT => true //持久化连接
            ]
        ]);
    }

    private static function getConfig($key, $config)
    {
        if (!$key) {
            if (!empty($config)) {
                return $config;
            }
        }
        $mysqlConfig = Config::get('mysql', []);
        if (empty($mysqlConfig)) return [];

        if (count($mysqlConfig) == count($mysqlConfig, COUNT_RECURSIVE)) {
            return $mysqlConfig;
        } else {
            while (!empty($mysqlConfig)) {
                $tempConfig = array_shift($mysqlConfig);
                if (is_array($tempConfig)) {
                    return $tempConfig;
                }
            }
        }
        return [];
    }

    private static function configToName($config)
    {
        return md5(json_encode($config));
    }

    public static function close($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);
        $name = self::configToName($config);
        self::clearInstance($name);
        return true;
    }

    public static function closeAll()
    {
        foreach (self::$_instance as $name => $val) {
            self::clearInstance($name);
        }
    }

    private static function clearInstance($name)
    {
        if (isset(self::$_instance[$name])) {
            self::$_instance[$name] = null;
            unset(self::$_instance[$name]);
        }
    }

    private static function ping($connect)
    {
        /*
        try{
            $connect->pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\Exception $e) {
            if(strpos($e->getMessage(), 'MySQL server has gone away')!==false){
                return false;
            }
        }
        */
        return true;
    }
}
