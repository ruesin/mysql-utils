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

    /**
     * @var \Medoo\Medoo
     */
    private $connection = null;

    /**
     * @var array
     */
    private $config = [];

    private function __construct($config)
    {
        $this->connection = $this->connect($config);
        $this->config = $config;
    }

    private function __clone()
    {
    }

    /**
     * @return \Medoo\Medoo | bool | self
     */
    public static function getInstance($key = '', $config = [])
    {
        $config = self::getConfig($key, $config);
        $name = self::configToName($config);
        try {
            if (empty(self::$_instance[$name])) {
                self::$_instance[$name] = new self($config);
            } else {
                if (self::ping(self::$_instance[$name]) !== true) {
                    self::clearInstance($name);
                    self::$_instance[$name] = new self($config);
                }
            }
        } catch (\Exception $e) {
            error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . PHP_EOL);
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
            'charset' => empty($config['charset']) ? 'utf8mb4' : $config['charset'],
            'prefix' => isset($config['prefix']) ? $config['prefix'] : '',
            'option' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //PDO::ATTR_CASE => PDO::CASE_NATURAL,
                //PDO::ATTR_PERSISTENT => true //持久化连接
            ]
        ]);
    }

    public static function closeAll()
    {
        foreach (self::$_instance as $name => $val) {
            self::clearInstance($name);
        }
    }

    public static function close($key = '', $config = [])
    {
        return self::clearInstance(self::configToName(self::getConfig($key, $config)));
    }

    private static function clearInstance($name)
    {
        if (!isset(self::$_instance[$name])) return true;
        self::$_instance[$name] = null;
        unset(self::$_instance[$name]);
        return true;
    }

    private static function getConfig($key, $config)
    {
        if (!empty($config)) {
            return $config;
        }

        if ($key) {
            return Config::get('mysql.' . $key);
        }

        $mysqlConfig = Config::get('mysql', []);
        if (empty($mysqlConfig)) return [];

        if (array_key_exists($key, $mysqlConfig)) {
            return $mysqlConfig[$key];
        }

        if (count($mysqlConfig) == count($mysqlConfig, COUNT_RECURSIVE)) {
            return $mysqlConfig;
        }

        while (!empty($mysqlConfig)) {
            $tempConfig = array_shift($mysqlConfig);
            if (is_array($tempConfig)) {
                return $tempConfig;
            }
        }
        return [];
    }

    private static function configToName($config)
    {
        return md5(json_encode($config));
    }

    private static function ping(self $instance)
    {
        /*try{
            $instance->connection->pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\Exception $e) {
            if(strpos($e->getMessage(), 'MySQL server has gone away')!==false){
                return false;
            }
        }*/
        return true;
    }

    public function __call($name, $arguments)
    {
        if (empty($this->connection) || !$this->connection instanceof \Medoo\Medoo) {
            return false;
        }

        if (!method_exists($this->connection, $name)) {
            return false;
        }

        try {
            return call_user_func_array([$this->connection, $name], $arguments);
        } catch (\PDOException $e) {
            $error = $e->errorInfo;
            if ($error['1'] == '2006' || strpos($error['2'], 'MySQL server has gone away') !== false
                || $error['1'] == '1317' || strpos($error['2'], 'Query execution was interrupted') !== false
            ) {
                error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . ' . ' . $this->connection->last() . PHP_EOL);
                $this->connection = self::connect($this->config);
                return call_user_func_array([$this->connection, $name], $arguments);
            }
            throw $e;
        }
    }
}
