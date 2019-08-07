<?php

require_once __DIR__ . '/vendor/autoload.php';
//
//\Worker\Extras\Logger::setPath('/logs/test.log');
//\Worker\Extras\Logger::setDebug();
////
////echo \Worker\Extras\Logger::getPath();
//\Worker\Extras\Logger::info('some test');

$shebaCheck = new \Worker\Extras\ShebaCheck(json_decode('{
    "channel": "hamyar_aval_channel",
    "cif": "2720890",
	"referenceId": "9805130562007291"
}', TRUE));
//$shebaCheck->setToken((new \Worker\Extras\Token())->make());
$shebaCheck = $shebaCheck->toArray();
$data = ['a' => 'b'];
$data += $shebaCheck;
var_dump(
    config('time.start'),
    config('time.stop'),
    (new \Worker\Extras\Timer())->isBetween(config('time.start'), config('time.stop')),
    $shebaCheck,
    $data
);
//class A {
//    use \Worker\Traits\CheckTransactionTrait;
//
//    public function Test()
//    {
//
//        $this->checkWallet(['tafzil' => '700000082', 'amount' => '101495'],
//            \Worker\Models\SamanTransactionDocument::class);
//    }
//}
//
//(new A)->Test();

//$queue_name = getParam('queue', TRUE);
//$options = new \Worker\Extras\Sheba(json_decode('{
//  "amount": 1,
//  "channel": "hamyar_aval_channel",
//  "cif": "2720890",
//  "clientIp": "string",
//  "description": "string",
//  "factorNumber": "987654",
//  "ibanNumber": "IR310120010000004654932379",
//  "ownerName": "محمد مهدی درفشی",
//  "sourceDepositNumber": "814-810-2720890-1",
//  "token": "05db3172-cb01-4a38-b4ce-34ecd2c6cb33",
//  "trackerId": "46deaaa9-db3e-45a7-99d9-b5983ba247ef",
//  "transferDescription": "string"
//}'));
////
////var_dump($sheba->toArray());
//
//$options->setToken((new \Worker\Extras\Token())->make());
//
//$options->setTrackerId(\Ramsey\Uuid\Uuid::uuid4()->toString());
//$result = ($options->toArray());
//var_dump(
//    jdate(time())->format('Y-m-d')
//);
//putenv('LOG_TYPE=terminal');
//\Worker\Extras\Logger::emergency('test on emergancy');

//$response = json_decode('{
//    "referenceId": "9805150562001042",
//    "ownerName": "محمد مهدی درفشی",
//    "currency": "IRR",
//    "transferStatus": "READY_TO_TRANSFER",
//    "amount": 1,
//    "ibanNumber": "IR310120010000004654932379",
//    "description": "string",
//    "transactionStatus": "ACCEPTED",
//    "sourceIbanNumber": "IR290560081481002720890001",
//    "factorNumber": "987654",
//    "transferDescription": "string"
//}', true);
//$result = \Worker\Models\ShebaTransactionDocument::insertBankResult(
//    $data['_id'],
//    $response,
//    $options['json']['trackerId']
//);
//
//var_export($result->getModifiedCount());

//var_dump($sheba->setToken('test value'));
//var_dump($sheba->getToken());
//Logger::get()->logPath('/logs/logTest.log');
//Logger::error('some error');
//Logger::debug('some debug');
//use Monolog\Logger as Log;
//$logger = new Log('test');
//$fileHandler = new \Monolog\Handler\StreamHandler('storage/mylogs.log', \Monolog\Logger::INFO);
//$fileHandler->setFormatter(new \Monolog\Formatter\JsonFormatter);
//$logger->pushHandler($fileHandler);

//$logger = new \Katzgrau\KLogger\Logger('/logs/log/my_app', Psr\Log\LogLevel::WARNING, array (
//    'logFormat' => '{date} - {level} - {file} - {line} - {message}',
//    'filename'  => 'error_log'
//));
//
//$logger->info('INFO message');
//$logger->error('an error');
//Logger::get()->logPath('/logs/tt.log');
//Logger::error(getParam('worker', false, '_'));
//Logger::info(json_encode($argv));
//sleep((int) getParam('worker', false, '_'));

//use Worker\Models\Redis\Flag;
//
//Flag::set('true');
//
//echo Flag::get(), PHP_EOL;

//use Worker\Models\Flag;

//use Worker\Reflector\Reflector;
//
//$flag  = new Reflector(new \Worker\Models\Flag);
//$worker = new Reflector(new \Worker\Models\Worker);
//
//var_dump('empty: ', $flag->isEmpty());
//var_dump('isset: ', $flag->isSet());
//var_dump($worker->get());
//
//$flag->set('gsld');
//$worker->set('worker sample');
//
//var_dump('delete: ', $flag->delete());
//var_dump($worker->get());
//var_dump(time(true), time());
//var_dump(time(true) + 8);
//$flag = true;
//while($flag === true) {
//    if (Flag::get() == 'false') {
//        Flag::set('true');
//        $flag = false;
//    } else {
//        sleep(0.5);
//    }
//}
//do {
//    if (Flag::get() == 'false') {
//        Flag::set('true');
//    } else {
//        sleep(1);
//    }
//} while(Flag::get() == 'true');
//Flag::set('a');
////

//$message = 'template: forgot_password
//token: ##PASSWORD##';
//
//
//use Predis\Autoloader as PredisAutoloader;
//
//PredisAutoloader::register();
//
//try {
//    $redis = new \Predis\Client();
//}
//catch (\Throwable $e) {
//    die($e->getMessage());
//}
//var_dump(($redis->get('flag') === NULL));
//if ($redis->get('flag')) {
//    echo 'FLAG IS SET', PHP_EOL;
//} else {
//    echo 'FLAG IS NOT SET', PHP_EOL;
//}
//$redis->set('flag', 'fa');
//var_dump($redis->del(['flag']));
//while (true) {
//    sleep((int) $argv[1]);
//    $redis->set('flag', true);
//    echo (new \DateTime())->format('H:i:s'), ' FLAG: ', $redis->get('flag'), PHP_EOL;
//    sleep((int) $argv[2]);
//    $redis->set('flag', false);
//    echo (new \DateTime())->format('H:i:s'), ' FLAG: ', $redis->get('flag'), PHP_EOL;
//}


//$config = \Worker\Core\Core::getConfig();
//var_dump($config);

//$user = \Worker\Models\DetailRelation::getUser(60000266);
//var_dump($user);
//$details = [
//    60000266,
//    700000086,
//    60000012,
//    700000092,
//    700000093,
//    700000082,
//    700000090,
//];
//foreach($details as $detail) {
//    if (preg_match('/^70/', $detail)) {
//        $accountCheck = Passenger::updateWallet(['detail_code' => $detail], 20000, true);
//    } else {
//        $accountCheck = Driver::updateWallet(['detail_code' => $detail], 20000, true);
//    }
//    echo (
//        (preg_match('/^70/', $detail) ?
//            'passengers' :
//            'drivers')
//        ) . " \"$accountCheck\"\n\n";
//}

//class A {
//    use \Worker\Traits\SenderTrait;
//}
//$a = new A;
//$b = $a->getSamanTransactionOptions(json_decode('{
//    "_id": 1002,
//    "additionalDocumentDesc": "string",
//    "amount": "5",
//    "channel": "hamyar_aval_channel",
//    "cif": "2720890",
//    "clientIp": "string",
//    "destinationComment": "string",
//    "destinationDeposit": "805-800-2562226-1",
//    "referenceNumber": "00001562474230240446",
//    "sourceComment": "string",
//    "sourceDeposit": "814-810-2720890-1",
//    "name": "محمدمهدی درفشی",
//    "tafzil": "60000266",
//    "trackerId": "05ae6865-96b7-4bdb-b0d9-44e4b60599b8",
//    "Date(miladi)": "2019-07-07",
//    "date(shamsi)": "1398-04-16",
//    "Time(API)": "09:07:10",
//    "sabt_dar_sanadpardaz_(ID)": "",
//    "sabt_dar_mongo_hamyar133_(ID)": "",
//    "trackerId_sanadpardaz": "",
//    "date": "2019-07-07",
//    "bank_type": "saman_transaction",
//    "created_at": "2019-07-07 09:03:57",
//    "exception": "trackerId_sanadpardaz is not empty"
//}', true), \Ramsey\Uuid\Uuid::uuid4()->toString());
//var_dump(json_encode($b));
//$mongo = (new Client('mongodb://root:secret@mongo:27017'))->hamyar->driver_referral_list->updateOne(
//    array(
//        'registered_driver_id' => (int) $args['user_id']
//    ),
//    array(
//        '$set' => array('registered_driver_wallet' => $args['amount'])
//    ),
//    array(
//        'upsert' => false
//    )
//);
//$mongo->getModifiedCount()

//$hamyar = Connection::connect('hamyar')->{app('hamyar.db')};
//for ($id = 2000; $id < 5000; $id ++) {
////    $amount = Driver::updateWallet(['user_id' => $id, 'amount' => 100505]);
//    $amount = Driver::getAmount(['user_id' => $id, 'amount' => 100000]);
//        echo "[$id]: " . $amount . PHP_EOL;
//}

//
//var_dump($hamyar->passengers->getAmount(1)->toArray());

//$class = 'CallToKababCaseClass';
//
//$preg = preg_match_all('/[A-Z][a-z]*/', $class, $matches);
//var_dump(strtolower(implode('_', current($matches))));
//foreach($hamyar->listCollections() as $value) {
//
//    echo var_dump($value->getName());
//}

//$transactions = \Worker\Models\TransactionDocument::find();
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//$spreadsheet = new Spreadsheet();
//$list = json_decode(json_encode($transactions->toArray()), true);
//$array_keys = [];
//foreach($list as $key => $value) {
//    if (current($value) < 2) {
//        $spreadsheet->setTitle($key);
//    }
//    if (count($value) > count($array_keys)) {
//        $array_keys = array_keys($key);
//    }
//    $index = 0;
//    foreach($value as $k => $val) {
//        $value[$k] = (empty($val) ? 'no value' : $val);
//    }
//    $list[$key] = $value;
//}
//array_unshift($list, $array_keys);
////var_dump($array_keys);
//$sheet = $spreadsheet->getActiveSheet()->fromArray($list, null, 'A1');
//$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
//$writer->save('aaaaaaaaaaaaaa.xlsx');







//$url = 'http://localhost/public/uploads/excels/saman-1398-04-24.xlsx';
//
//$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
//$reader->setReadDataOnly(TRUE);
//$fiveMB = 5 * 1024 * 1024;
//$tempnam = tempnam(sys_get_temp_dir(), 'excel_');
//file_put_contents($tempnam, 'http://google.com');
//foreach(scandir(sys_get_temp_dir()) as $file) {
//    if (! in_array($file, ['.', '..']))
//        echo $file, ' ', filesize(sys_get_temp_dir() . '/' . $file), PHP_EOL;
//}
//$spreadsheet = $reader->load($tempnam);
//echo fread($handle, filesize($tempnam));
//unlink($tempnam);
//
//
//$worksheet = $spreadsheet->getActiveSheet();
//$highestRow = $worksheet->getHighestRow();
//$highestColumn = $worksheet->getHighestColumn();
//
//echo $highestColumn;



//$file = file_get_contents('https://www.google.com');

//echo $file;




















//$list = json_decode(json_encode($transactions->toArray()), true);
//$spreadsheet = new Spreadsheet();
//$sheet = $spreadsheet->getActiveSheet();
//$keys = [];
//foreach ($list as $index => $row)
//{
//    foreach ($row as $key => $value)
//    {
//        if (! in_array($key, $keys))
//        {
//            $col = empty($keys) ? 'A' : end(array_keys($keys));
//            $keys[(empty($keys) ? $col : ++ $col)] = $key;
//        }
//    }
//}
//
//foreach ($keys as $col => $key) {
//    $sheet
//        ->getColumnDimension($col)
//        ->setAutoSize(false)
//        ->setWidth(20)
//    ;
//    $sheet
//        ->getCell(($col) . 1)
//        ->setValue($key)
//        ->getStyle($col . 1)
//        ->getFill()
//        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//        ->getStartColor()->setARGB('00AAFF58');
//    ;
//}
//
//foreach ($list as $row)
//{
//    $index = current($row) + 1;
//
//    foreach ($keys as $col => $key)
//    {
//        $colValue = (gettype($row[$key]) === 'string') ? $row[$key] : json_encode($row[$key]);
//        $sheet
//            ->setCellValueExplicit(
//                $col . $index,
//                $colValue,
//                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
//            )
//            ->getStyle($col . $index)
//            ->getAlignment()
//            ->setWrapText(true)
//        ;
//    }
//}
//$writer = new Xlsx($spreadsheet);
//$writer->save('/logs/test.xlsx');




















