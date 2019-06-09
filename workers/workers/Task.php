<?php
namespace Workers;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Core\AMQPConnection;

class Task {
    private $connection;
    private $channel;
    private static $self;
    private $queue;
    private $message;

    public static function connect() {
        if (static::$self === null) {
            set_time_limit(0);
            static::$self = new static;

            static::$self->connection = AMQPConnection::connect()->amqp;
        }

        return static::$self;
    }

    public function channel() {
        if ($this->channel === null) {
            $this->channel = $this->connection->channel();
        }

        return $this;
    }

    public function queue($queue) {
        $this->queue = $queue;
        $this->channel->queue_declare($this->queue, false, true, false, false);

        return $this;
    }

    public function basic_publish($message) {
        $this->channel->basic_publish(new AMQPMessage($message, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]), '', $this->queue);
        $this->message = " [x] sent $message\n";

        return $this;
    }

    public function message() {
        return $this->message;
    }

    public function close($closeConnection = false) {
        $this->channel->close();
        $this->connection->close();
        if ($closeConnection == TRUE) {AMQPConnection::connect()->close();}
        static::$self = NULL;
    }

    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}
