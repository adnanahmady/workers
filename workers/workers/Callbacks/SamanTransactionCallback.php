<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\Connection;
use Workers\Extras\Logger;
use GuzzleHttp\Client as Guzzle;
use Workers\Extras\Timer;
use Workers\Job;
use Workers\Models\TransactionDocument;
use Workers\Traits\SenderTrait;

class SamanTransactionCallback extends AbstractCallback {
    use SenderTrait;

    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $data = Job::getJobData($msg);
        if (! empty($data["referenceNumber"])) return $msg;
        $token = $this->getToken();
        $options = $this->getSamanTransactionOptions($data, $token);
        $expire = microtime(true) + 4;
        Logger::debug('SUPERMAN JOB', json_encode(Job::getJobData($msg),
                JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

        while(microtime(true) < $expire)
        {
            try {
                $options['json']['trackerId'] = Uuid::uuid4()->toString();
                $res = (new Guzzle())->request(
                    'POST',
                    app('saman.normal_transfer'),
                    $options
                );

                $content = json_decode($res->getBody()->getContents(), true);
                $content['Date(miladi)'] = (new Timer())->format('Y-m-d');
                $content['date(shamsi)'] = jdate(time())->format('Y-m-d');
                $content['Time(API)'] = (new Timer())->format('H:i:s');
                $content['trackerId'] = $options['json']['trackerId'];
                $result = TransactionDocument::updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => $content],
                    ['upsert' => true]
                );
//        Logger::info('data upserted', $result->getUpsertedCount());
                $hamyar = Connection::connect('hamyar')->{app('hamyar.db')};
                echo var_dump($hamyar->listCollections());




                $data[] = json_decode($res->getBody()->getContents(), true);
                $this->sendTask(
                    app('queue.order'),
                    'insert_to_accounting_plan',
                    $data
                );
                break;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
//                Logger::critical(($e->getResponse()->getBody()->getContents()));
                if ($e->getCode() == '403'):
                    $this->sendTask(
                        app('queue.priority'),
                        'login'
                    );
                sleep(1);
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

//        sleep(30);
        $this->ack($msg);

        return $msg;
    }

    public function getToken() {
        do {
            $token = Login::find(
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

            if ($expiration == NULL || (new Timer())->lessThan($expiration . ' - 3 Minute')) {
                $this->sendTask(
                    app('queue.priority'),
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