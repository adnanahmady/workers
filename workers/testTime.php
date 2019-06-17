<?php

require_once __DIR__ . '/workers/bootstrap.php';

//echo (new \Workers\Extras\Timer())->check();
//echo 'test' . PHP_EOL;
//echo env('rabbit_queue_priority', 'no environment'), PHP_EOL;

//$collection = \Workers\Core\MongoConnection::connect()
//    ->{app('mongo.db')};
//$conn = new \MongoDB\Client(
//    sprintf(
//        '%5$s://%1$s:%2$s@%3$s:%4$s',
//        app('mongo.user'),
//        app('mongo.pass'),
//        app('mongo.host'),
//        app('mongo.port'),
//        app('db.type')
//    )
//);
//
//$db = $conn->hamyar;
//$collection = $db->listCollections();
//
//foreach($collection as $key => $value) {
////    $coll = $db->selectCollection($value->getName());
////    $toArray = json_encode($coll->find()->toArray(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
////    while($row = array_shift(json_decode($toArray, true))) {
////        $find = preg_grep('/hamyar/', $row);
////        if ($find !== null && !empty($find)) {
////            var_dump(json_encode($find, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
////        }
//    var_dump($value->getName());
////    }
////    echo 'end "' . $value->getName() . '"' . PHP_EOL . PHP_EOL;
//}
//$toString = \Ramsey\Uuid\Uuid::uuid4()->toString();
//echo substr(str_replace('-', '', $toString), 15) . PHP_EOL;
//echo $toString . PHP_EOL;