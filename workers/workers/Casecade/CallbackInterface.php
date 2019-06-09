<?php

namespace Workers\Casecade;

use PhpAmqpLib\Message\AMQPMessage;

interface CallbackInterface {
    public function __invoke(AMQPMessage $msg): AMQPMessage;
}