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
    /**
     * All Configs
     *
     * @var array
     */
    private static $configs = [];

    /**
     * All instance
     * @var array
     */
    private static $instances = [];

    /**
     * @var \Medoo\Medoo
     */
    private $connection = null;

    /**
     * Connection name
     * @var null
     */
    private $name = null;

    /**
     * Connection config
     * @var array
     */
    private $config = [];

    private function __construct($name)
    {
        $this->name = $name;
        $this->config = self::getConfig($this->name);
        $this->connection = $this->connect($this->config);
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
        if (!isset(self::$instances[$name]) || !self::$instances[$name]) {
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
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
     * @param string $name
     * @param array $config
     * @param bool $rewrite
     */
    public static function setConfig($name, array $config, $rewrite = true)
    {
        if (array_key_exists($name, self::$configs) && $rewrite !== true) return;
        self::$configs[$name] = $config;
    }

    /**
     * @param $key
     * @return array
     */
    public static function getConfig($key)
    {
        return array_key_exists($key, self::$configs) ? self::$configs[$key] : [];
    }

    public static function clear()
    {
        foreach (self::$instances as $name => $instance) {
            self::close($name);
        }
    }

    public static function close($name)
    {
        if (!isset(self::$instances[$name])) return true;
        self::$instances[$name] = null;
        unset(self::$instances[$name]);
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
            //https://www.php.net/manual/zh/pdostatement.errorinfo.php
            return call_user_func_array([$this->exception($e->errorInfo['2'], $e), $name], $arguments);
        } catch (\Exception $e) {
            return call_user_func_array([$this->exception($e->getMessage(), $e), $name], $arguments);
        }
    }

    private function exception($message, $e)
    {
        if (!isset($this->config['retry_exception']) || !is_array($this->config['retry_exception']))
            throw $e;

        foreach ($this->config['retry_exception'] as $exception) {
            if (strpos($message, $exception) === false)
                continue;

            $this->connection = $this->connect($this->config);
            return $this->connection;
        }

        throw $e;
    }
}
