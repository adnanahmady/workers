<?php

namespace Worker\Abstracts;

use Worker\Extras\Transform;
use Worker\Core\AMQPConnection;

abstract class AbstractConsumer {
    private $connection;
    private $message;
    private $callback;
    private $channel;
    const WAIT_BEFORE_RECONNECT_US = 1;

    public function connect() {
        $this->connection = AMQPConnection::connect()->amqp;

        return $this;
    }

    public function channel() {
        $this->channel = $this->connection->channel();
        $this->channel->basic_qos(null, 1, null);

        return $this;
    }

    public function isConnected() {
        return $this->connection !== NULL;
    }

    public function consume($queue, $message = '') {
        $this->channel->queue_declare($queue, false, true, false,false);
        // change forth parameter to true for disable acknowledgement ability
        $this->channel->basic_consume($queue, '', false, false, false, false, $this->callback);
        $this->message = $message . PHP_EOL;

        return $this;
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

    public function ack(&$msg) {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function close() {
        $this->channel->close();
        $this->connection->close();
        AMQPConnection::connect()->close();
    }

    public function checkBlock()
    {
        if (getParam('time') === 'block' && !(new Timer())->check()) {
            $start = getParam('start');
            $time = time();
            $startTime = $time - strtotime($start);
            $wait = $startTime > 0 ?
                ($time - (strtotime($start . ' +1 day'))) :
                ($startTime);

            if ($startTime > 0) {
                sleep($wait * -1);
            }
        }
    }
}