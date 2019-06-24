<?php

namespace Workers\Exceptions;

use Throwable;

class WorkerTimeOutException extends \Exception {
    public function __construct(string $message = "", int $code = 408, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}