<?php

namespace Worker\Abstracts;

abstract class AbstractSingleton {
    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}