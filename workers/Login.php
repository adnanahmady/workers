<?php
set_time_limit(0);
date_default_timezone_set('Asia/Tehran');

require_once __DIR__ . '/workers/bootstrap.php';

use Workers\Extras\Timer;
use MongoDB\Client as Mongo;
use GuzzleHttp\Client as Guzzle;
use Workers\Extras\Logger;

while(true) {
    try {
        $timer = new Timer();
    } catch (\Throwable $e) {
//        Logger::emergency('Timer has problem');
        break;
    }
    if (($timer)->check()) {
        $mongo = \Workers\Core\MongoConnection::connect();
        $collection = $mongo->{app('mongo.db')}->login;
        $time = $collection->find([], ['sort' => ['_id' => -1], 'limit' => 1, 'projection' => [
            'expiration' => 1,
            '_id' => 0
        ]]);
        foreach($time as $value) {$time = ($value['expiration']);}

        if (($timer)->greaterThan($time . " - 3 Minute")) {
//            Logger::debug('is greater than', $time);
            $res = (new Guzzle())->request('POST', app('saman.login.url'), [
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
            ]);
            $content = json_decode($res->getBody()->getContents(), true);
            $result = $collection->insertOne($content);
//            Logger::debug('login succeed', $result->getInsertedCount());
        }
    }
    sleep(60);
}