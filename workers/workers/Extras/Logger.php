<?php

namespace Worker\Extras;

/**
 * Class Logger
 * @package Worker\Extras
 * @method static debug($message, array $context = array())
 * @method static info($message, array $context = array())
 * @method static notice($message, array $context = array())
 * @method static warning($message, array $context = array())
 * @method static error($message, array $context = array())
 * @method static critical($message, array $context = array())
 * @method static alert($message, array $context = array())
 * @method static emergency($message, array $context = array())
 * @method static setName($value)
 * @method static setType($value)
 * @method static setPath($value)
 * @method static getName()
 * @method static getType()
 * @method static getPath()
 */
class Logger {
    private static $logger;
    private $debug;
    private $name;
    private $type;
    private $path;

    public static function __callStatic($name, $arguments) {
        if (static::$logger === NULL) {
            static::$logger = new static;
            static::$logger->setName(config('log.name'));
            static::$logger->setType(config('log.type'));
            static::$logger->setPath(config('log.path'));
            static::$logger->setDebug();
        }


        $getterSetter = static::$logger->GetterSetter($name, $arguments);
        if ($getterSetter === TRUE || gettype($getterSetter) === 'string')
        {
            return $getterSetter;
        }

        call_user_func_array([static::$logger->debug, $name], $arguments);
    }

    public function __call($name, $arguments)
    {
        $getterSetter = static::$logger->GetterSetter($name, $arguments);
        if ($getterSetter === TRUE || gettype($getterSetter) === 'string')
        {
            return $getterSetter;
        }
    }

    public function setDebug()
    {
        static::$logger->debug = new Debug(
            static::$logger->getName(),
            static::$logger->getType(),
            static::$logger->getPath()
        );
    }

    protected function GetterSetter($name, $arguments)
    {
        $methodExists = method_exists(static::$logger, $name);
        $isGet = preg_match('/^get/i', $name);
        $isSet = preg_match('/^set/i', $name);

        if (! ($isGet || $isSet) OR $methodExists)
        {
            return FALSE;
        }
        $name = preg_replace('/^' . ($isGet ? 'get' : 'set') . '/i', '', $name);

        if (! property_exists($this, $name))
        {
            $name = lcfirst($name);
        }

        if (! property_exists($this, $name))
        {
            return FALSE;
        }

        if ($isSet)
        {
            static::$logger->set($name, current($arguments));
        }
        else
        {
            return static::$logger->get($name);
        }

        return TRUE;
    }

    protected function get($property)
    {
        return @static::$logger->$property;
    }

    protected function set($property, $value)
    {
        static::$logger->$property = $value;
    }

    public static function cleanAgrs($args) {
        return preg_replace('/\\\\/', '\\', $args);
    }


    protected function getDebug() {}
    protected function __construct() {}
    public function __clone() {}
    public function __wakeup() {}
}