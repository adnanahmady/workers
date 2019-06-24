<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use Workers\Extras\Logger;
use Workers\Extras\Timer;
use Workers\Models\TransactionDocument;
use Workers\Task;
use Workers\Job;

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