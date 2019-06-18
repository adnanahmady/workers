<?php

namespace Workers\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Workers\Abstracts\AbstractCallback;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Workers\Core\MongoConnection;
use Workers\Extras\Logger;
use Workers\Extras\Timer;
use Workers\Task;
use Workers\Job;

class ExportExcelCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $filename      = (json_decode($msg->body))->data->filename;
        /**
         * temp on local
         */
        $inputFileName = (str_replace('localhost', 'web', $filename));
        $file           = './uploads/excels/xlsx/file.xlsx';
        makeDir($file);
        $fileName = getFileName($filename);
        try {
            file_put_contents($file, file_get_contents($inputFileName));
        } catch (\Throwable $e) {
//            Logger::emergency($e->getMessage());
        }
//        Logger::emergency($fileName);

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumn ++;

        $date           = (new Timer())->format('Y-m-d');
        $connection = MongoConnection::connect();
        $db = $connection->{app('mongo.db')};
        $collection = $db->transactionDocuments;
        for ($row = 2; $row <= $highestRow; $row ++) {
            $KEY = [];
            for ($col = 'A'; $col != $highestColumn; $col ++):
                $KEY[
                    str_replace (
                        ' ',
                        '_',
                        $worksheet
                            ->getCell( $col . 1 )
                            ->getValue()
                    )
                ] = (string) $worksheet
                    ->getCell( $col . $row )
                    ->getValue();
            endfor;
            $KEY ['date'] = $date;
            $KEY ['bank_type']  = $fileName . '_transaction';
            $key = $KEY;
            if ($fileName == 'saman') {
                unset(
                    $key['trackerId'],
                    $key['Date(miladi)'],
                    $key['date(shamsi)'],
                    $key['Time(API)'],
                    $key['sabt_dar_sanadpardaz_(ID)'],
                    $key['sabt_dar_mongo_hamyar133_(ID)'],
                    $key['referenceNumber']
                );
            } else {
                unset(
                    $key['referenceId'],
                    $key['transferStatus'],
                    $key['primitive_transactionStatus'],
                    $key['final_transactionStatus'],
                    $key['sabt_dar_sanadpardaz_(ID)'],
                    $key['transactionStatus'],
                    $key['Date(miladi)'],
                    $key['date(shamsi)'],
                    $key['Time(API)'],
                    $key['trackerId']
                );
            }

            $count = $collection->findOne($key);

            if ($count === NULL) {
                $KEY ['created_at'] = (new Timer())->format('Y-m-d H:i:s');
                $countDocs = $collection->countDocuments();
                $KEY['_id'] = (int) $countDocs + 1;
                $result = ($collection->insertOne($KEY));
//                Logger::info('data inserted', $result->getInsertedCount());
            } else {
//                $KEY ['updated_at'] = (new Timer())->format('Y-m-d H:i:s');
//                $result = ($collection->updateOne(['_id' => $count['_id']], ['$set' => $KEY], ['upsert' => true]));
//                Logger::info('data upserted', $result->getUpsertedCount());
            }
        }
        Task::
        connect()->
        channel()->
        queue(app('queue.priority'))->
        basic_publish(new Job('load_tasks'));

        $this->ack($msg);
        return $msg;
    }
}