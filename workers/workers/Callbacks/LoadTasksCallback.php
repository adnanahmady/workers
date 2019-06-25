<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Models\TransactionDocument;
use Worker\Task;
use Worker\Job;

class LoadTasksCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $task       = Task::connect();
        $date     = (string) (new Timer())->format('Y-m-d');
        $data       = TransactionDocument::find(['date' => $date]);

        foreach($data as $row) {
            $bankType = $row['bank_type'];
            unset($row['bank_type']);
            $task->channel()->queue(app('queue.order'))->basic_publish(new Job($bankType, $row));
        }
        Logger::debug('load from database and send to rabbit finished');
        $this->ack($msg);

        return $msg;
    }
}