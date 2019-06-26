<?php

namespace Worker\Extras;

/**
 * Class Logger
 * @package Worker\Extras
 * @method debug
 * @method info
 * @method notice
 * @method warning
 * @method error
 * @method critical
 * @method alert
 * @method emergency
 */
class Logger {
    private static $logger;
    private $debug;

    public static function __callStatic($name, $arguments) {
        if (static::$logger === NULL) {
            static::$logger = new static;
            static::$logger->debug = new Debug(app('log.name'), app('log.type'));
        }

        return static::$logger->debug->$name(static::cleanAgrs($arguments));
    }

  public static function cleanAgrs($args) {
    return preg_replace('/\\\\/', '\\', $args);
  }

  protected function __construct() {}
    public function __clone() {}
    public function __wakeup() {}
}