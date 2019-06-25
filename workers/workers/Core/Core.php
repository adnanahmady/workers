<?php

namespace Worker\Core;

class Core {
    protected static $config;

    /**
     * @return mixed
     */
    public static function getConfig()
    {
        if (static::$config === null) {
            $path = dirname(__DIR__) . '/config/';
            $dir = scandir($path);
            unset($dir[0], $dir[1]);
            $config = [];
            foreach($dir as $file) {
                foreach((include "{$path}{$file}") as $key => $value) {
                    $lowerKey = strtolower($key);
                    if (isset ($config[$lowerKey])) {
                        $config[$lowerKey] += $value;
                    } else {
                        $config[$lowerKey] = $value;
                    }
                }
            }

            static::$config = $config;
        }

        return static::$config;
    }

    public static function __callStatic($method, $arguments) {
        require_once dirname(__DIR__) . '/helpers/functions.php';

        return call_user_func_array($method, $arguments);
    }
}