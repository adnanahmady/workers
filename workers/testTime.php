<?php

require_once __DIR__ . '/workers/bootstrap.php';

//echo (new \Workers\Extras\Timer())->check();
//echo 'test' . PHP_EOL;
//echo env('rabbit_queue_priority', 'no environment'), PHP_EOL;

//$collection = \Workers\Core\MongoConnection::connect()
//    ->{app('mongo.db')};
//$collection2 = \Workers\Core\MongoConnection::connect('mongo2')
//    ->{app('mongo2.db')};
//$collection2 = $collection2->listCollections();
//$collection = $collection->listCollections();
//
//foreach($collection as $key => $value) {
//    var_dump($value->getName());
//}
//echo '////////////2222222222////////////' . PHP_EOL;
//foreach($collection2 as $key => $value) {
//    var_dump($value->getName());
//}
//$toString = \Ramsey\Uuid\Uuid::uuid4()->toString();
//echo substr(str_replace('-', '', $toString), 15) . PHP_EOL;
//echo $toString . PHP_EOL;

//var_dump(\Workers\Core\Core::getConfig());