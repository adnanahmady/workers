<?php

namespace Worker\Abstracts;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Core\AMQPConnection;
use Worker\Extras\Logger;
use Worker\Interfaces\CallbackInterface;
use Worker\Task;
use Worker\Extras\Job;

abstract class AbstractCallback implements CallbackInterface {
    const WAIT_FOR_LOGIN = 3;
    const REQUEST_WAIT = 2;

    /**
     * sets expiration time of waiting for a request
     */
    const EXPIRE_TIME = 8;

    /**
     * handles tasks from rabbitMQ
     *
     * @param \PhpAmqpLib\Message\AMQPMessage $msg
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    abstract public function __invoke(AMQPMessage $msg): AMQPMessage;

    /**
     * tells rabbitMQ to remove task from its queue
     *
     * @param $msg
     */
    public function ack(&$msg) {
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    /**
     * makes process sleep
     *
     * @param string $jobDate
     */
    public function wait(string $jobDate)
    {
        $seconds = strtotime($jobDate) - strtotime('now');
        $steps = 5;
        Logger::debug(strtotime('now') - strtotime($jobDate));
        Logger::debug($seconds);
        for ($second = $seconds; $second > 0; $second -= $steps)
        {
            setTimeout(function (){
                AMQPConnection::connect()->amqp->getIO()->read(0);
            }, $steps);
        }
    }
}