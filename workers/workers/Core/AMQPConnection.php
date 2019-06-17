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
            static::$connection->amqp = new AMQPStreamConnection(
                app('rabbit_host'),
                app('rabbit_port'),
                app('rabbit_user'),
                app('rabbit_pass')
            );
        }

        return static::$connection;
    }

    public function close() {
        $this->amqp->close();
        static::$connection = NULL;
    }
}