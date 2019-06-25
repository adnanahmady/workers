<?php

namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Worker\Core\Connection;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Models\TransactionDocument;
use Worker\Task;
use Worker\Job;

class ExportExcelCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        var_dump(Job::getJobData($msg)->filename);
        $filename      = var_dump(Job::getJobData($msg)->filename);
        echo '*****************************************88888', PHP_EOL, PHP_EOL;
        /**
         * temp on local
         */
        $inputFileName = (str_replace('localhost', 'web', $filename));
        $file           = './uploads/excels/xlsx/file.xlsx';
        makeDir($file);
        if (empty($filename)) {
            $this->ack($msg);
            Logger::emergency('filename is equal to ' . $filename);

            return $msg;
        }
        $fileName = getFileName($filename);
        try {
            file_put_contents($file, file_get_contents($inputFileName));
        } catch (\Throwable $e) {
            $this->ack($msg);
            Logger::emergency($e->getMessage());

            return $msg;
        }
        Logger::emergency($fileName);

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumn ++;

        $date           = (new Timer())->format('Y-m-d');

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

            $count = TransactionDocument::findOne($key);

            if ($count === NULL) {
                $KEY ['created_at'] = (new Timer())->format('Y-m-d H:i:s');
                $countDocs = TransactionDocument::countDocuments();
                $KEY['_id'] = (int) $countDocs + 1;
                $result = (TransactionDocument::insertOne($KEY));
                Logger::info('data inserted', $result->getInsertedCount());
            }
        }
        Task::connect()->channel()->queue(app('queue.priority'))->basic_publish(new Job('load_tasks'));
        $this->ack($msg);

        return $msg;
    }
}