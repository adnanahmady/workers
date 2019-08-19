<?php

namespace Worker\Extras;

use DateTime;
use Worker\Core\Core;
use Worker\Exceptions\InvalidTimeException;
use Worker\Extras\Logger;

class Timer extends DateTime {
    private $startTime;
    private $stopTime;

    public function check($start = NULL, $stop = NULL) {
        try {
            if ($start === NULL || $stop === NULL) {
                $start = getParam('start', true);
                $stop = getParam('stop', true);
            }
            $this->startTime = $start;
            $this->stopTime =  ((strtotime($start) - strtotime($stop)) > 0 ?
                strtotime($stop . ' +1 day') :
                $stop);
        } catch (\Throwable $e) {
            return true;
        }

        if (
            $this->lessThanOrEqual($this->startTime) && $this->greaterThanOrEqual($this->stopTime)
//            strtotime("$jobDate +" . config('time.sleep')) - strtotime('now')
        ) {
            return true;
        }

        return false;
    }

    public function isBetween($start = NULL, $stop = NULL) {
        try {
            if ($start === NULL || $stop === NULL) {
                $start = getParam('start', true);
                $stop = getParam('stop', true);
            }
            $this->startTime = $start;
            $this->stopTime  = "$start +$stop";
        } catch (\Throwable $e) {
            throw new InvalidTimeException('Declared Time is not valid');
        }

        if (
//            $this->lessThanOrEqual($this->startTime) && $this->greaterThanOrEqual($this->stopTime)
            strtotime($this->stopTime) - strtotime('now') > -1 &&
            strtotime($this->startTime) - strtotime('now') < 1
        ) {
            return true;
        }

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