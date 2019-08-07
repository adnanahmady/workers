<?php
/**
 * AMQPConnection.php
 *
 * @author adnanahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Core;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Worker\Abstracts\AbstractSingleton;

/**
 * Class AMQPConnection
 *
 * Connects to RabbitMQ Server
 *
 * @package Worker\Core
 */
class AMQPConnection  extends AbstractSingleton {
    /**
     * an instance of AMQPConnection
     *
     * @var AMQPConnection
     */
    private static $connection;

    /**
     * a RabbitMQ Connection
     *
     * @var AMQPStreamConnection
     */
    public $amqp;

    /**
     * connects to RabbitMQ Server
     *
     * @return AMQPConnection
     */
    public static function connect() {
        if (static::$connection === NULL) {
            static::$connection = new static;
            static::$connection->amqp = new AMQPStreamConnection(
                config('rabbit.host'),
                config('rabbit.port'),
                config('rabbit.user'),
                config('rabbit.pass')
            );
        }

        return static::$connection;
    }

    /**
     * closes and destroy RabbitMQ Connection
     */
    public function close() {
        $this->amqp->close();
        static::$connection = NULL;
    }
}