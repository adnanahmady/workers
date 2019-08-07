<?php

namespace Worker\Core;

use Predis\Autoloader;
use Predis\Client;

class RedisConnection
{
    private static $conn;
    public function __construct($connection = NULL)
    {
        $this->setConnection($connection);
    }

    public function setConnection($connection = NULL)
    {
        Autoloader::register();
        static::$conn = new Client($connection && $connection);
    }

    public function getConnection()
    {
        return static::$conn;
    }
}