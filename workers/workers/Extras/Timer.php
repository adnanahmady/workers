<?php

namespace Workers\Extras;

use DateTime;
use Workers\Core\Core;
use Workers\Extras\Logger;

class Timer extends DateTime {
    private $startTime;
    private $stopTime;

    public function check($start = NULL, $stop = NULL) {
        try {
            if ($start === NULL || $stop === NULL) {
                $start = getParam('start');
                $stop = getParam('stop');
            }
            $check = strtotime($start) - strtotime($stop);
            $this->startTime = strtotime($start);
            $this->stopTime =  ($check > 0 ? strtotime($stop . ' +1 day') : $check);
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