<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Worker\Abstracts\AbstractCallback;
use Worker\Extras\Logger;
use Worker\Models\TransactionDocument;
use Worker\Traits\SenderTrait;
use Worker\Job;
use GuzzleHttp\Client as Guzzle;

class InsertToAccountingPlanCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
        if (empty($data["referenceNumber"])) {
            $this->ack($msg);

            return $msg;
        }
        $options['json'] = $this->getRecPay($data, function ($data) {
            $data['CheqNo'] = substr(str_replace('-', '', Uuid::uuid4()->toString()), 15);

            return $data;
        });
        $expire = microtime(true) + 4;

        while(microtime(true) < $expire)
        {
            try {
                $options['json']['RequestId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    app('accounting_plan.recpay'),
                    $options
                );

                $response = json_decode($res->getBody()->getContents(), true);
                $content['sabt_dar_sanadpardaz_(ID)'] = $response['Id'];
                $content['sabt_dar_sanadpardaz_response'] = $response;

                $result = TransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => $content],
                    ['upsert' => true]
                );
                Logger::info('data upserted', $result->getUpsertedCount());
                break;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                Logger::emergency(
                    Job::getJobName($msg),
                    json_encode($options),
                    json_encode([]),
                    json_encode([
                        'message' => $e->getMessage() ? $e->getResponse()->getBody()->getContents() : 'NULL',
                        'file' => $e->getFile() ? $e->getFile() : 'NULL',
                        'line' => $e->getLine() ? $e->getLine() : 'NULL',
                        'code' => $e->getCode() ? $e->getCode() : 'NULL',
                        'stackTrace' => $e->getTraceAsString() ? $e->getTraceAsString() : 'NULL',
                    ])
                );
            }

            sleep(0.5);
        }

        $this->ack($msg);
        return $msg;
    }
}