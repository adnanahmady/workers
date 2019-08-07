<?php

namespace Worker\Core;

/**
 * Class Core
 * @package Worker\Core
 * @method static string env(string $environment, string $default)
 * @method static mixed|string app(string $config, string $default)
 * @method static void makeDir(string $filePath)
 * @method static string getFileName(string $file)
 * @method static \InvalidArgumentException|string getParam(string $paramName, boolean $exception = false)
 */
class Core {
    /**
     * @var /app/workers/config/ $config
     */
    protected static $config;

    /**
     * loads app configurations
     *
     * @return mixed
     */
    public static function getConfig()
    {
        if (static::$config === null) {
            $config = [];
            $path = dirname(__DIR__) . '/config/';
            $dir = array_diff(scandir($path), ['.', '..']);
            foreach($dir as $file) {
                $lowercaseKey = strtolower(current(explode('.', $file)));
                if (isset ($config[$lowercaseKey])) {
                    $config[$lowercaseKey] += include "{$path}{$file}";
                } else {
                    $config[$lowercaseKey] = include "{$path}{$file}";
                }
            }
            static::$config = $config;
        }

        return static::$config;
    }

    public static function __callStatic($method, $arguments) {
        return call_user_func_array($method, $arguments);
    }
}