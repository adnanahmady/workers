<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Job;
use Worker\Models\SamanTransactionDocument;
use Worker\Traits\CheckTransactionTrait;

class ValidateSamanTransactionCallback extends AbstractCallback {
    use CheckTransactionTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);

        $this->checkReferenceNumber($data);
        $this->amountValidation($data, SamanTransactionDocument::class);
        $this->isTafzilSet($data, SamanTransactionDocument::class);
        $this->isTafzilEmpty($data, SamanTransactionDocument::class);
        $this->checkUser($data, SamanTransactionDocument::class);
        $this->checkSanadpardazTrackerId($data, SamanTransactionDocument::class);
        $this->checkWallet($data, SamanTransactionDocument::class);

        sendTask(config('rabbit.queue.single'), 'saman_transaction', $data);
        $this->ack($msg);
        return $msg;
    }
}