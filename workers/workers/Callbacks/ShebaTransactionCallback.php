<?php

namespace Workers\Callbacks;

use MongoDB\Client as Mongo;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\MongoConnection;
use Workers\Extras\Logger;
use GuzzleHttp\Client as Guzzle;
use Workers\Extras\Timer;
use Workers\Job;
use Workers\Task;

class ShebaTransactionCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
        if (! empty($data["referenceNumber"])) return $msg;
        $db = MongoConnection::connect()->{app('mongo.db')};
        $token = $this->getToken($db->login);

        $options = [
            'json' => [
                "amount" => $data["amount"],
                "channel" => $data["channel"],
                "cif" => $data["cif"],
                "clientIp" => $data["clientIp"],
                "description" => $data["description"],
                "factorNumber" => $data["factorNumber"],
                "ibanNumber" => $data["ibanNumber"],
                "ownerName" => $data["ownerName"],
                "sourceDepositNumber" => $data["sourceDepositNumber"],
                "token" => $token,
                "trackerId" => $data["trackerId"],
                "transferDescription" => $data["transferDescription"],
            ]
        ];

        for($i = 1; $i < 4; $i ++) {
            try {
                $options['json']['trackerId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    'https://kook.sb24.com:9001/transfer/normal',
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
//                            'message' => $e->getMessage() ? $e->getMessage() : 'NULL',
//                            'file' => $e->getFile() ? $e->getFile() : 'NULL',
//                            'line' => $e->getLine() ? $e->getLine() : 'NULL',
//                            'code' => $e->getCode() ? $e->getCode() : 'NULL',
//                        ]));
                    break;
                endif;
            }
        }

        $result = json_decode($res->getBody()->getContents(), true);
        $content['referenceId'] = $result['referenceId'];
        $content['transferStatus'] = $result['transferStatus'];
        $content['final_transactionStatus'] = $result['transferStatus'];
        $content['final_transactionStatus'] = $result['transferStatus'];
        $content['transactionStatus'] = $result['transactionStatus'];
        $content['Date(miladi)'] = (new Timer())->format('Y-m-d');
        $content['date(shamsi)'] = jdate(time())->format('Y-m-d');
        $content['Time(API)'] = (new Timer())->format('H:i:s');
        $content['trackerId'] = $options['json']['trackerId'];
        $result = $db->transactionDocuments->updateOne(
            ['_id' => $data['_id']],
            ['$set' => $content],
            ['upsert' => true]
        );
//        Logger::info('data upserted', $result->getUpsertedCount());

        return $msg;
    }

    public function sendTask($queue, $job, $data = [], $success = [], $fails = []) {
        Task::
        connect()->channel()->queue($queue)->basic_publish(
            new Job($job, $data, $success, $fails)
        );
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