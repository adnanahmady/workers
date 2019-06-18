<?php

namespace Workers\Core;

use MongoDB\Client;
use Workers\Abstracts\AbstractSingleton;

class MongoConnection extends AbstractSingleton {
    private static $connection;
    private $mongoConnection;

    public static function get($callback) {
        if (static::$connection === NULL) {
            static::$connection = new static;
            static::$connection->mongoConnection = $callback();
        }

        return static::$connection;
    }

    public static function connect($connection = 'mongo') {
        return static::get(function() use ($connection) {
            return new Client(sprintf(
                'mongodb://%1$s:%2$s@%3$s:%4$s',
                app("$connection.user"),
                app("$connection.pass"),
                app("$connection.host"),
                app("$connection.port")
            ));
        })->mongoConnection;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array(static::$connection->$name, $arguments);
    }
}