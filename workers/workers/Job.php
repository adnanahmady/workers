<?php
namespace Workers;

use Workers\Extras\Timer;

class Job {
    private $job = '';
    private $date = '';
    private $data = [];
    private $success = [];
    private $fails = [];

    public function __construct($job, $data = [], $success = [], $fails = []) {
        $this->job = $job;
        $this->date = (new Timer())->format('Y-m-d');
        $this->data = $data;
        $this->success = $success;
        $this->fails = $fails;
    }

    public function __toString() {
        return json_encode(get_object_vars($this));
    }

    public static function getJobData($job) {
        return json_decode($job->body, true)['data'];
    }

    public static function getJobDate($job) {
        return (string) json_decode($job->body)->date;
    }

    public static function getJobSuccess($job) {
        return json_decode($job->body, true)['success'];
    }

    public static function getJobFails($job) {
        return json_decode($job->body, true)['fails'];
    }

    public static function getJobName($job) {
        return (string) json_decode($job->body)->job;
    }
}