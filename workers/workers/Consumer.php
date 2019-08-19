<?php
/**
 * containes abstract worker implementation
 *
 * @author adnanahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker;

use Worker\Abstracts\AbstractConsumer;
use Worker\Abstracts\AbstractWorker;
use Worker\Extras\Job;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Exceptions\WorkerTimeOutException;
use Worker\Extras\Transform;
use Worker\Models\SamanTransactionDocument;
use Worker\Models\ShebaTransactionDocument;

/**
 * Class Worker | checks time and runs callback based on job title | handles callbacks Exceptions
 * @package Worker
 */
class Consumer extends AbstractConsumer {
    public function __construct()
    {
        return $this->callback(function ($msg) {
            $callback = (string) new Transform((string) Job::getJobName($msg));

            try {
                $this->checkBlock();

                if (! (new Timer())->check()) {
                    $jobData = Job::getJobData($msg);
                    if (preg_match('/saman/', $jobData['bank_type']))
                    {
                        SamanTransactionDocument::updateOne(
                            ['_id' => $jobData['_id']],
                            ['$set' => ['exception' => 'Time out Task Exception']]
                        );
                    } else {
                        ShebaTransactionDocument::updateOne(
                            ['_id' => $jobData['_id']],
                            ['$set' => ['exception' => 'Time out Task Exception']]
                        );
                    }
                    throw new WorkerTimeOutException('Time out Task Exception');
                }

                $Callback = new $callback;
                $Callback($msg);
            } catch (WorkerTimeOutException $e) {
                $this->ack($msg);
                Logger::emergency($e->getMessage());
            } catch (\Throwable $e) {
                $this->ack($msg);
                Logger::emergency($e->getMessage());
            }
        });

    }
}
