<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Logger;

class BaseCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $this->ack($msg);
        return $msg;
    }
}