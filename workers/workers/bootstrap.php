<?php
date_default_timezone_set('Asia/Tehran');

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::create(dirname(__DIR__));
$dotenv->load();

require_once __DIR__ . '/helpers/functions.php';

use Workers\Core\Core;
use Workers\Extras\Logger;

$logConfig = include_once __DIR__ . '/config/log.php';

try {
    $queue_name = getParam('queue');

    $config = include_once __DIR__ . '/config/queue.php';
} catch (\Throwable $e) {
    Logger::critical($e->getMessage());
    exit;
}

try {
    $sub_queue = getParam('sub');

    $queue = "$queue_name.$sub_queue";
} catch (\Throwable $e) {
    $queue = $config['rabbit_queues']['order'];
}

$core = new Core;
