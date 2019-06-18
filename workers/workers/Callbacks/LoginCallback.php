<?php

namespace Workers\Callbacks;

use GuzzleHttp\Exception\GuzzleException;
use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\MongoConnection;
use Workers\Extras\Timer;
use GuzzleHttp\Client as Guzzle;
use MongoDB\Client as Mongo;
use Workers\Extras\Logger;

class LoginCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $collection = MongoConnection::connect()->{app('mongo.db')}->login;
        $time = $collection->find([],
            [
                'sort' => [
                    '_id' => -1
                ],
                'limit' => 1,
                'projection' => [
                    'expiration' => 1,
                    '_id' => 0
                ]
            ]
        );
        foreach($time as $value) {$expiration = ($value['expiration']);}

        try {
            if (
                (new Timer())->greaterThan($expiration . " - 3 Minute")
            ) {
                throw new \Exception('Login Exception');
            }
        } catch (\Throwable $e) {
            try {
                $result = $this->login($collection);

            } catch (GuzzleException $e) {
            }

//            Logger::debug('login succeed', $result->getInsertedCount());
        }
        $this->ack($msg);

        return $msg;
    }

    /**
     * @param \MongoDB\Collection $collection
     *
     * @return \MongoDB\InsertOneResult
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(\MongoDB\Collection $collection) {
        $res     = (new Guzzle())->request(
            'POST', 'https://kook.sb24.com:9000/login', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    "channel" => app('saman.login.channel'),
                    "password" => app('saman.login.pass'),
                    "secretkey" => app('saman.login.secret_key'),
                    "username" => app('saman.login.user')
                ]
            ]
        );
        $content = json_decode($res->getBody()->getContents(), TRUE);
        $result  = $collection->insertOne($content);

        return $result;
}
}