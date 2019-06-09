<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\MongoConnection;
use Workers\Extras\Logger;
use Workers\Extras\Timer;
use Workers\Task;
use Workers\Job;

class LoadTasksCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $task       = Task::connect();
        $collection = MongoConnection::connect()->{env('MONGO_DB', 'test')}->transactionDocuments;
        $date     = (string) (new Timer())->format('Y-m-d');
        $data       = $collection->find(['date' => $date]);

        foreach($data as $row) {
            $bankType = $row['bank_type'];
            unset($row['bank_type']);
            $task->
            channel()->
            queue($GLOBALS['config']['rabbit_queues']['order'])->
            basic_publish(new Job($bankType, $row));
        }
//        Logger::debug('load from database and send to rabbit finished');
        return $msg;
    }
}