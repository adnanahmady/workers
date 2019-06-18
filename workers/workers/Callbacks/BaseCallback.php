<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use Workers\Extras\Logger;

class BaseCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $this->ack($msg);
        return $msg;
    }
}