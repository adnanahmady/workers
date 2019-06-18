<?php

namespace Workers\Abstracts;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Casecade\CallbackInterface;
use Workers\Task;
use Workers\Job;

abstract class AbstractCallback implements CallbackInterface {
    abstract public function __invoke(AMQPMessage $msg): AMQPMessage;

    public function sendTask($queue, $job, $data = [], $success = [], $fails = []) {
        Task::connect()->channel()->queue($queue)->basic_publish(
            new Job($job, $data, $success, $fails)
        );
    }
}