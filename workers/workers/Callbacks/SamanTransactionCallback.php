<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\MongoConnection;
use Workers\Extras\Logger;
use GuzzleHttp\Client as Guzzle;
use Workers\Extras\Timer;
use Workers\Job;
use Workers\Traits\SenderTrait;

class SamanTransactionCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
//        if (! empty($data["referenceNumber"])) return $msg;
        $collection = MongoConnection::connect()->{app('mongo.db')};
        $token = $this->getToken($collection->login);
        $options = $this->getSamanTransactionOptions($data, $token);
        $expire = microtime(true) + 4;

        while(microtime(true) < $expire)
        {
            try {
                $options['json']['trackerId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    app('saman.normal_transfer'),
                    $options
                );
                break;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
//                Logger::critical(($e->getResponse()->getBody()->getContents()));
                if ($e->getCode() == '403'):
                    $this->sendTask(
                        $GLOBALS['config']['rabbit_queues']['priority'],
                        'login'
                    );
                else:
//                    Logger::alert(
//                        Job::getJobName($msg),
//                        json_encode(Job::getJobData($msg)),
//                        [],
//                        json_encode([
//                            'transfer_id' => $data['_id'],
//                            'message' => $e->getMessage() ? $e->getResponse()->getBody()->getContents() : 'NULL',
//                            'file' => $e->getFile() ? $e->getFile() : 'NULL',
//                            'line' => $e->getLine() ? $e->getLine() : 'NULL',
//                            'code' => $e->getCode() ? $e->getCode() : 'NULL',
//                        ]));
                    break;
                endif;
            }

            sleep(0.5);
        }

        $content = json_decode($res->getBody()->getContents(), true);
        $content['Date(miladi)'] = (new Timer())->format('Y-m-d');
        $content['date(shamsi)'] = jdate(time())->format('Y-m-d');
        $content['Time(API)'] = (new Timer())->format('H:i:s');
        $content['trackerId'] = $options['json']['trackerId'];
        $result = $collection->transactionDocuments->updateOne(
            ['_id' => $data['_id']],
            ['$set' => $content],
            ['upsert' => true]
        );
//        Logger::info('data upserted', $result->getUpsertedCount());

        $data[] = json_decode($res->getBody()->getContents(), true);
        $this->sendTask(
            $GLOBALS['config']['rabbit_queues']['order'],
            'insert_to_accounting_plan',
            $data
        );

        return $msg;
    }

    public function getToken(\MongoDB\Collection $collection) {
        do {
            $token = $collection->find(
                [], [
                      'sort' => ['_id' => - 1],
                      'limit' => 1,
                      'projection' => [
                          'token' => 1,
                          'expiration' => 1,
                          '_id' => 0
                      ]
                  ]
            );

            foreach ($token as $value) {
                $token      = ($value['token']);
                $expiration = ($value['expiration']);
            }

            if ($expiration == NULL || (new Timer())->greaterThan($expiration . ' - 3 Minute')) {
                $this->sendTask(
                    $GLOBALS['config']['rabbit_queues']['priority'],
                    'login'
                );
            }
        } while (
            !(
                $expiration !== NULL &&
                ! (new Timer())->greaterThan($expiration . ' - 3 Minute')
            )
        );

        return $token;
    }
}