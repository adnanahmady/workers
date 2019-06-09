<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use Workers\Core\MongoConnection;
use Workers\Extras\Timer;
use GuzzleHttp\Client as Guzzle;
use MongoDB\Client as Mongo;
use Workers\Extras\Logger;

class LoginCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $collection = MongoConnection::connect()->{env('MONGO_DB', 'test')}->login;
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
            $result = $this->login($collection);

//            Logger::debug('login succeed', $result->getInsertedCount());
        }

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
                    "channel" => env("SAMAN_LOGIN_CHANNEL"),
                    "password" => env("SAMAN_LOGIN_PASSWORD"),
                    "secretkey" => env("SAMAN_LOGIN_SECRETKEY"),
                    "username" => env("SAMAN_LOGIN_USERNAME")
                ]
            ]
        );
        $content = json_decode($res->getBody()->getContents(), TRUE);
        $result  = $collection->insertOne($content);

        return $result;
}
}