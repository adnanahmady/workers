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

    public static function connect() {
        return static::get(function() {
            return new Client(sprintf(
                'mongodb://%1$s:%2$s@%3$s:%4$s',
                app('mongo.user'),
                app('mongo.pass'),
                app('mongo.host'),
                app('mongo.port')
            ));
        })->mongoConnection;
    }
}