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
                $result = $collection->transactionDocuments->updateOne(
                    ['_id' => $data['_id']],
                    ['$set' => $content],
                    ['upsert' => true]
                );
//        Logger::info('data upserted', $result->getUpsertedCount());
                $mongo2 = MongoConnection::connect('mongo2')->{app('mongo2.db')};



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

//        sleep(120);
        $this->ack($msg);

        return $msg;
    }

    /**
     * Get passengers wallet amount
     *
     * @param $args
     * @return int
     * @throws Exception
     */
    public function getPassengerAmount($mongo_db, $args)
    {
        $options = [
            'projection' => ['wallet_amount' => 1, '_id' => 1]
        ];
        $res = $mongo_db->find('passengers', array("_id" => (int) $args['userid']), $options);

        return (!empty($res)) ? $res[0]['wallet_amount'] : 0;
    }

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getDriverAmount($mongo_db, $args)
    {
        $options = [
            'projection' => ['registered_driver_wallet' => 1]
        ];
        $res = $mongo_db->find('driver_referral_list', array("registered_driver_id" => (int) $args['userid']), $options);

        return (!empty($res)) ? $res[0]['registered_driver_wallet'] : 0;
    }

    /**
     * Update passengers wallet amount
     *
     * @param $args
     * @return bool
     * @throws Exception
     */
    public function updatePassengerWallet($mongo_db, $args) {
        $passengerAmount = $this->getPassengerAmount($mongo_db, $args);
        $args['amount'] = $passengerAmount + $args['amount'];
        $result = $mongo_db->updateOne('passengers',
            array(
                '_id' => (int) $args['userid']
            ),
            array(
                '$set' => array('wallet_amount' => $args['amount'])
            ),
            array(
                'upsert' => false
            )
        );

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update drivers Wallet amount
     *
     * @param $args
     * @return int
     */
    public function updateDriverWallet($mongo_db, $args) {
        $driverAmount = $this->getDriverAmount($mongo_db, $args);
        $args['amount'] = $driverAmount + $args['amount'];
        $result = $mongo_db->updateOne('driver_driverinfo',
            array(
                'registered_driver_id' => (int) $args['userid']
            ),
            array(
                '$set' => array('registered_driver_wallet' => $args['amount'])
            ),
            array(
                'upsert' => false
            )
        );

        if ($result) {
            return true;
        } else {
            return false;
        }
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