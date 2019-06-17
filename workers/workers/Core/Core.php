<?php

namespace Workers\Core;

class Core {
    protected static $config;

    /**
     * @return mixed
     */
    public static function getConfig()
    {
        if (static::$config === null) {
            static::$config = require_once dirname(__DIR__) . '/config/config.php';
        }

        return static::$config;
    }

    public static function __callStatic($method, $arguments) {
        require_once dirname(__DIR__) . '/helpers/functions.php';

        return call_user_func_array($method, $arguments);
    }
}