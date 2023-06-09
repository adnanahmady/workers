<?php

namespace Worker\Exceptions;

use Throwable;

class ValidationException extends \Exception {
    public function __construct(string $message = "", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}