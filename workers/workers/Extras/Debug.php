<?php

namespace Worker\Extras;

use Monolog\Handler\FirePHPHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\NullLogger;

/**
 * Class Debug
 * @package Worker\Extras
 */
class Debug {
    /**
     * @var string|null
     */
    private $logType = NULL;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Debug constructor.
     *
     * @param        $logName
     * @param string $logType
     * @param null   $logPath
     */
    public function __construct($logName, $logType = 'terminal', $logPath = NULL) {
        $this->logType = $logType;
        if ($this->logType !== 'terminal') {
            $this->logger = new Logger($logName);
            try {
                $this->logger->pushHandler(new StreamHandler(
                    ($logType === NULL OR empty($logType)) ? config('log.path') : $logPath
                ));
                $this->logger->pushHandler(new FirePHPHandler());
            } catch (\Throwable $e) {}
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     */
    public function __call($name, $arguments) {
        if ($this->logType !== 'terminal') {
            $this->logger->pushProcessor(function ($record) {
                $record['extra']['backtrace'] = debug_backtrace();

                return $record;
            });

            call_user_func_array([$this->logger, $name], $arguments);
        } else {
            call_user_func_array([$this, 'echoTerminal'], [$name, $arguments]);
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     */
    public function echoTerminal($name, $arguments) {
        $backtrace = end(debug_backtrace());
        $file = end(explode('/', $backtrace['file']));
        $line = $backtrace['line'];
        unset($backtrace);
        echo (new Timer())->format('Y-m-d H:i:s'),
        ' ', strtoupper($name) , " \"$file:$line\" ",
        json_encode(array_shift($arguments), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), ' ',
        json_encode($arguments, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),
        PHP_EOL;
    }

    /**
     * @param $logType
     */
    public function setLogType ($logType) {
        $this->logType = $logType;
    }
}