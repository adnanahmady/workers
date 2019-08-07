<?php
/**
 * bootstraps hole app
 */
set_time_limit(0);
ini_set('max_execution_time', 0);
date_default_timezone_set('Asia/Tehran');
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Worker\Extras\Logger;

try {
    $queue_name = getParam('queue', true);
} catch (\Throwable $e) {
    Logger::critical($e->getMessage());
    exit;
}

try {
    $sub_queue = getParam('sub', true);

    $queue = "$queue_name.$sub_queue";
} catch (\Throwable $e) {
    $queue = config('rabbit.queue.order');
}
