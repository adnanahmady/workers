<?php
set_time_limit(0);
date_default_timezone_set('Asia/Tehran');

require_once __DIR__ . '/wWorker/bootstrap.php';

use Worker\Extras\Timer;
use MongoDB\Client as Mongo;
use GuzzleHttp\Client as Guzzle;
use Worker\Extras\Logger;

while(true) {
    try {
        $timer = new Timer();
    } catch (\Throwable $e) {
//        Logger::emergency('Timer has problem');
        break;
    }
    if (($timer)->check()) {
        $mongo = \Worker\Core\Connection::connect();
        $collection = $mongo->{app('mongo.db')}->login;
        $time = $collection->find([], ['sort' => ['_id' => -1], 'limit' => 1, 'projection' => [
            'expiration' => 1,
            '_id' => 0
        ]]);
        foreach($time as $value) {$time = ($value['expiration']);}

        if (($timer)->lessThan($time . " - 3 Minute")) {
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