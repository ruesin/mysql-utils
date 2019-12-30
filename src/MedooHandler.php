<?php

namespace Ruesin\Utils;

use Swover\Pool\ConnectorInterface;

class MedooHandler implements ConnectorInterface
{
    private $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        return new \Medoo\Medoo([
            'database_type' => $this->config['driver'],
            'database_name' => $this->config['database'],
            'server' => $this->config['host'],
            'port' => $this->config['port'],
            'username' => $this->config['username'] ?? '',
            'password' => $this->config['password'] ?? '',
            'charset' => $this->config['charset'] ?? 'utf8mb4',
            'prefix' => $this->config['prefix'] ?? '',
            'option' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL
            ]
        ]);
    }

    public function disconnect($connection)
    {
        // TODO: Implement disconnect() method.
    }

    public function reset($connection)
    {
        // TODO: Implement reset() method.
    }

    public function ping($connection)
    {
        // TODO: Implement ping() method.
    }
}