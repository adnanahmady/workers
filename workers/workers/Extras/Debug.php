<?php

namespace Worker\Extras;

use Monolog\Handler\FirePHPHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
     */
    public function __construct($logName, $logType = 'terminal') {
        $this->logType = $logType;
        if ($this->logType !== 'terminal') {
            $this->logger = new Logger($logName);
            try {
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::DEBUG));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::INFO));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::NOTICE));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::WARNING));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::ERROR));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::CRITICAL));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::ALERT));
                $this->logger->pushHandler(new StreamHandler('/logs/logs.log', Logger::EMERGENCY));
                $this->logger->pushHandler(new FirePHPHandler());
            } catch (\Throwable $e) {}
            $this->logger->info('Logger is now ready!');
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
            $db = (debug_backtrace())[2];
            $arguments['file'] = end(explode('/', $db['file']));
            $arguments['line'] = $db['line'];
            unset($db);
            foreach($arguments as $key => $value) {
                $arguments[$key] = str_replace("\\", '', json_encode($value));
            }
            $this->logger->$name(strtoupper($name), $arguments);
        } else {
            $this->echoTerminal($name, $arguments);
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     */
    public function echoTerminal($name, $arguments) {
        $db = (debug_backtrace())[2];
        $file = end(explode('/', $db['file']));
        $line = $db['line'];
        unset($db);
        echo (new Timer())->format('Y-m-d H:i:s'),
            ' ', strtoupper($name) , " \"$file:$line\" ",
            json_encode($arguments), PHP_EOL;
    }

    /**
     * @param $logType
     */
    public function setLogType ($logType) {
        $this->logType = $logType;
    }
}