<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Job;
use Worker\Models\ShebaTransactionDocument;
use Worker\Traits\CheckTransactionTrait;

class ValidateShebaTransactionCallback extends AbstractCallback {
    use CheckTransactionTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);

        $this->checkReferenceNumber($data);
        $this->amountValidation($data, ShebaTransactionDocument::class);
        $this->isTafzilSet($data, ShebaTransactionDocument::class);
        $this->isTafzilEmpty($data, ShebaTransactionDocument::class);
        $this->checkUser($data, ShebaTransactionDocument::class);
        $this->checkSanadpardazTrackerId($data, ShebaTransactionDocument::class);
        $this->checkWallet($data, ShebaTransactionDocument::class);

        sendTask(config('rabbit.queue.single'), 'sheba_transaction', $data);
        $this->ack($msg);
        return $msg;
    }
}