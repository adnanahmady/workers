<?php
/**
 * contains export excel class
 */
namespace Worker\Callbacks;

use PhpAmqpLib\Message\AMQPMessage;
use Worker\Abstracts\AbstractCallback;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Models\SamanTransactionDocument;
use Worker\Extras\Job;
use Worker\Models\ShebaTransactionDocument;

/**
 * Class ExportExcelCallback
 *
 * @package Worker\Callbacks
 */
class ExportExcelCallback extends AbstractCallback {
    public function __invoke(AMQPMessage $msg): AMQPMessage {
        $filename      = Job::getJobData($msg)['filename'];
        /**
         * temp on local
         */
        $inputFileName = (str_replace('localhost', 'web', $filename));
        $file           = './uploads/excels/xlsx/file.xlsx';
        makeDir($file);
        if (empty($filename)) {
            $this->ack($msg);
            Logger::emergency('file name is empty', ['filename' => $filename]);

            return $msg;
        }
        $fileName = getFileName($filename);
        try {
            file_put_contents($file, file_get_contents($inputFileName));
        } catch (\Throwable $e) {
            $this->ack($msg);
            Logger::emergency($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);

            return $msg;
        }
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumn ++;
        $date           = date('Y-m-d');

        foreach($worksheet->getRowIterator() as $row) {
            if ($row->getRowIndex() == 1) {
                continue;
            }
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
                    ->getCell( $col . $row->getRowIndex() )
                    ->getValue();
            endfor;
            $KEY ['date'] = $date;
            $KEY ['bank_type']  = $fileName . '_transaction';
            $KEY ['created_at'] = (new Timer())->format('Y-m-d H:i:s');

                if ($fileName == 'saman')
                {
                    $countDocs = SamanTransactionDocument::count();
                    $KEY['_id'] = (int) $countDocs + 1;
                    (SamanTransactionDocument::insertOne($KEY));
                }
                else
                {
                    $countDocs = ShebaTransactionDocument::count();
                    $KEY['_id'] = (int) $countDocs + 1;
                    (ShebaTransactionDocument::insertOne($KEY));
                }
        }
        sendTask(config('rabbit.queue.priority'), 'load_tasks', ['subject' => $fileName]);
        $this->ack($msg);

        return $msg;
    }
}