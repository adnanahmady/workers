<?php

require_once __DIR__ . '/workers/bootstrap.php';

//echo (new \Workers\Extras\Timer())->check();
//echo 'test' . PHP_EOL;
//echo env('rabbit_queue_priority', 'no environment'), PHP_EOL;

$collection = \Workers\Core\MongoConnection::connect()
    ->{env('MONGO_DB')}->transactionDocuments;
foreach($collection->find([]) as $key => $value) {
    var_dump($value);
}