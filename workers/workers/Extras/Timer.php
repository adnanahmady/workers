<?php

namespace Workers\Extras;

use DateTime;
use Workers\Core\Core;
use Workers\Extras\Logger;

class Timer extends DateTime {
    private $startTime;
    private $stopTime;

    public function check($startTime = NULL, $stopTime = NULL) {
        $log = new Debug('terminalLog');

        try {
            $this->startTime = $startTime === NULL ?
                strtotime(Core::getParam('start')) :
                $startTime;
            $this->stopTime  = $stopTime === NULL ?
                strtotime(Core::getParam('stop')) :
                $stopTime;
        } catch (\Throwable $e) {
//            Logger::info('No Start or Stop time is set.');

            return true;
        }
        $currentTime = time();

        if ($currentTime > $this->startTime && $currentTime < $this->stopTime) {
//            Logger::debug('it is current time.', date('H:i:s', $currentTime));

            return true;
        }
//        Logger::debug('it is not current time.', date('H:i:s', $currentTime));

        return false;
    }

    public function greaterThan($time) {
        return time() > strtotime($time);
    }
}