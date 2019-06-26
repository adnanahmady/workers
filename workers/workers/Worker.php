<?php
/**
 * containes abstract worker implementation
 *
 * @author adnanahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker;

use Worker\Abstracts\AbstractWorker;
use Worker\Extras\Logger;
use Worker\Extras\Timer;
use Worker\Exceptions\WorkerTimeOutException;

/**
 * Class Worker | checks time and runs callback based on job title | handles callbacks Exceptions
 * @package Worker
 */
class Worker extends AbstractWorker {
    /**
     * call parent channel and then return callback
     *
     * @return AbstractWorker|Worker
     */
    public function channel() {
        parent::channel();
        return $this->callback(function ($msg) {
            $callback = $this->getJobName($msg);

            try {
                $this->checkBlock();

                if (! (new Timer())->check()) {
                    $this->ack($msg);
                    throw new WorkerTimeOutException('Time out Task Exception');
                }

                $Callback = new $callback;
                $Callback($msg);
            } catch (WorkerTimeOutException $e) {
                Logger::alert(
                    Job::getJobName($msg),
                    json_encode(Job::getJobData($msg)),
                    json_encode([]),
                    json_encode([
                        'message' => $e->getMessage() ? $e->getMessage() : 'NULL',
                        'code' => $e->getCode() ? $e->getCode() : 'NULL',
                    ]));
            } catch (\Throwable $e) {
                Logger::alert(
                    Job::getJobName($msg),
                    json_encode(Job::getJobData($msg)),
                    json_encode([]),
                    json_encode([
                        'message' => $e->getMessage() ? $e->getMessage() : 'NULL',
                        'file' => $e->getFile() ? $e->getFile() : 'NULL',
                        'line' => $e->getLine() ? $e->getLine() : 'NULL',
                        'code' => $e->getCode() ? $e->getCode() : 'NULL',
                        'stackTrace' => $e->getTraceAsString() ? $e->getTraceAsString() : 'NULL',
                    ]));
            }

        });
    }
}
