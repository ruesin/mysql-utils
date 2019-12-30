<?php

namespace Ruesin\Utils;

use Swover\Pool\ConnectionPool;
use Swover\Pool\PoolFactory;

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
     * All Connector instance
     * @var array
     */
    private static $instances = [];

    /**
     * @var PoolFactory|null
     */
    private $pool = null;

    private function __construct($name)
    {
        $config = self::getConfig($name);

        $this->pool = new ConnectionPool(
            isset($config['pool_config']) ? $config['pool_config'] : [],
            new MedooHandler($config)
        );
    }

    private function __clone()
    {
    }

    /**
     * @param $name
     * @param array $config
     * @return mixed | PoolFactory | \Medoo\Medoo
     */
    public static function getInstance($name, $config = [])
    {
        if (!isset(self::$instances[$name]) || !self::$instances[$name]) {
            if (!empty($config)) self::setConfig($name, $config);
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
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

    /**
     * Clear all connector instance
     * @param null $name
     * @return bool
     */
    public static function clear($name = null)
    {
        $instances = $name === null ? self::$instances :
            (isset(self::$instances[$name]) ? [$name => self::$instances[$name]] : []);
        foreach ($instances as $name => $instance) {
            self::$instances[$name] = null;
            unset(self::$instances[$name]);
        }
        return true;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pool, $name], $arguments);
    }
}
