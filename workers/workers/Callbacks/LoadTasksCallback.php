<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Models\SamanTransactionDocument;
use Worker\Models\ShebaTransactionDocument;

class LoadTasksCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $date     = (string) (new Timer())->format('Y-m-d');
        if (Job::getJobData($msg)['subject'] == 'saman')
        {
            $data = SamanTransactionDocument::find(['date' => $date]);
        }
        else
        {
            $data = ShebaTransactionDocument::find(['date' => $date]);
        }

        foreach($data as $row) {
            sendTask(config('rabbit.queue.order'), 'validate_' . $row['bank_type'], $row);
        }
        Logger::info('load from database and send to rabbit finished');
        $this->ack($msg);

        return $msg;
    }
}