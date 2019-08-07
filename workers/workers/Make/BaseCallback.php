<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;

class BaseCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $this->ack($msg);
        return $msg;
    }
}