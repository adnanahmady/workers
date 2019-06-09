<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Workers\Abstracts\AbstractCallback;
use Workers\Extras\Logger;
use Workers\Traits\SenderTrait;
use Workers\Job;
use Workers\Core\MongoConnection;
use GuzzleHttp\Client as Guzzle;

class InsertToAccountingPlanCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
        if (empty($data["referenceNumber"])) return $msg;
        $collection = MongoConnection::connect()->{env('MONGO_DB', 'test')};
        $options['json'] = $this->getRecPay($data, function ($data) {
            $data['CheqNo'] = Uuid::uuid4()->toString();
            return $data;
        });
        $expire = microtime(true) + 4;

        while(microtime(true) < $expire)
        {
            try {
                $options['json']['RequestId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    env('ACCOUNTING_PLAN_ENDPOINT') . 'RecPay',
                    $options
                );

                $response = json_decode($res->getBody()->getContents(), true);
                $content['sabt_dar_sanadpardaz_(ID)'] = $response['Id'];
                $content['sabt_dar_sanadpardaz_response'] = $response;

                $result = $collection->transactionDocuments->updateOne(
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
                    ])
                );
            }

            sleep(0.5);
        }

        return $msg;
    }
}