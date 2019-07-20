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

    private static $config = [];

    private $key = null;

    private function __construct($name)
    {
        $config = self::getConfig($name);
        $this->connection = $this->connect($config);
        $this->key = $name;
    }

    private function __clone()
    {
    }

    /**
     * @param $name
     * @return \Medoo\Medoo
     */
    public static function getInstance($name)
    {
        if (empty(self::$_instance[$name])
            || self::ping(self::$_instance[$name]) !== true) {
            self::clearInstance($name);
            self::$_instance[$name] = new self($name);
        }
        return self::$_instance[$name];
    }

    /**
     * @param array $config
     * @return \Medoo\Medoo
     * @throws \Exception
     */
    private function connect(array $config)
    {
        foreach (['host', 'port', 'database', 'driver'] as $item) {
            if (!isset($config[$item]) || !$config[$item]) {
                throw new \Exception('Missing ' . $item . ' configuration item!');
            }
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
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ]);
    }

    /**
     * @param string $key
     * @param array $config
     * @param bool $rewrite
     */
    public static function setConfig(string $key, array $config, $rewrite = true)
    {
        if (array_key_exists($key, self::$config) && $rewrite !== true) return;
        self::$config[$key] = $config;
    }

    /**
     * @param $key
     * @return array
     */
    public static function getConfig($key)
    {
        return array_key_exists($key, self::$config) ? self::$config[$key] : [];
    }

    public static function clear()
    {
        foreach (self::$_instance as $name => $val) {
            self::clearInstance($name);
        }
    }

    public static function close(string $name)
    {
        if (!isset(self::$_instance[$name])) return true;
        self::$_instance[$name] = null;
        unset(self::$_instance[$name]);
        return true;
    }

    private static function ping($instance)
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
            //TODO inject
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
