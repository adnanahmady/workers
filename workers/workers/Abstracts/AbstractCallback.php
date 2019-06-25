<?php

namespace Worker\Abstracts;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Casecade\CallbackInterface;
use Worker\Task;
use Worker\Job;

abstract class AbstractCallback implements CallbackInterface {
    const WAIT_FOR_LOGIN = 3;

    abstract public function __invoke(AMQPMessage $msg): AMQPMessage;

    public function sendTask($queue, $job, $data = [], $success = [], $fails = []) {
        Task::connect()->channel()->queue($queue)->basic_publish(
            new Job($job, $data, $success, $fails)
        );
    }

    public function ack(&$msg) {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }
}