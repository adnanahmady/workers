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
            $this->startTime = $start;
            $this->stopTime =  ((strtotime($start) - strtotime($stop)) > 0 ?
                strtotime($stop . ' +1 day') :
                $stop);
        } catch (\Throwable $e) {
//            Logger::info('No Start or Stop time is set.');

            return true;
        }

        if ($this->lessThanOrEqual($this->startTime) && $this->greaterThanOrEqual($this->stopTime)) {
            Logger::debug('it is current time.',
                $this->greaterThanOrEqual($this->startTime) && $this->lessThanOrEqual($this->stopTime));

            return true;
        }
        Logger::debug('it is not current time.',
            $this->lessThanOrEqual($this->startTime) && $this->greaterThanOrEqual($this->stopTime));

        return false;
    }

    public function lessThan($time) {
        return time() > strtotime($time);
    }
    public function greaterThanOrEqual($time) {
        return time() <= strtotime($time);
    }

    public function greaterThan($time) {
        return time() < strtotime($time);
    }
    public function lessThanOrEqual($time) {
        return time() >= strtotime($time);
    }
}