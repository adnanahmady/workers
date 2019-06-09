<?php

namespace Workers\Core;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Workers\Abstracts\AbstractSingleton;

class AMQPConnection  extends AbstractSingleton {
    private static $connection;
    public $amqp;

    public static function connect() {
        if (static::$connection === NULL) {
            static::$connection = new static;
            global $config;
            static::$connection->amqp = new AMQPStreamConnection(
                $config['rabbit_host'], 
                $config['rabbit_port'], 
                $config['rabbit_user'], 
                $config['rabbit_pass']
            );
        }

        return static::$connection;
    }

    public function close() {
        $this->amqp->close();
        static::$connection = NULL;
    }
}