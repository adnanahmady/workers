<?php

require_once __DIR__ . '/workers/bootstrap.php';

use Worker\Task;
use Worker\Extras\Job;

$i = 0;
$test = Task::connect();

while (true) {
    $test->channel()->queue(config('rabbit.queue.order'))->basic_publish(
        new Job(
            'export_excel',
            [
                'name' => 'adnan',
                'family' => 'ahmady',
                'age' => 18,
                'duration' => $i++
            ]
        )
    );
    echo $test->message();
//    sleep(3);
}

$test->close();