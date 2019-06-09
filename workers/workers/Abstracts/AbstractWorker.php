<?php

namespace Workers\Abstracts;

use Workers\Extras\Transform;
use Workers\Core\AMQPConnection;

abstract class AbstractWorker {
    protected static $worker;
    private $connection;
    private $message;
    private $callback;
    private $channel;

    public static function connect() {
        if (static::$worker === NULL) {
            set_time_limit(0);
            static::$worker = new static;
            static::$worker->connection = AMQPConnection::connect()->amqp;
        }

        return static::$worker;
    }

    public function channel() {
        if ($this->channel === NULL) {
            $this->channel = static::$worker->connection->channel();
        }

        return $this;
    }

    public function consume($queue) {
        $this->channel->queue_declare($queue, false, true, false,false);
        // change forth parameter to true for disable acknowledgement ability
        $this->channel->basic_consume($queue, '', false, false, false, false, $this->callback);
        $this->channel->basic_qos(null, 1, null);
        $this->message = " [*] worker is run and its waiting for messages. to cancel press CTRL+C" . PHP_EOL;

        return $this;
    }

    public function getJobName($msg) {
        $callbackName = json_decode($msg->body)->job;

        return (string) new Transform((string) $callbackName);
    }

    public function callback($callback) {
        $this->callback = $callback;

        return $this;
    }

    public function run() {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        return $this;
    }

    public function message() {
        return $this->message;
    }

    public function close() {
        $this->channel->close();
        $this->connection->close();
        AMQPConnection::connect()->close();
        static::$worker = NULL;
    }


    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}