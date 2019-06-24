<?php

namespace Workers;

use Workers\Abstracts\AbstractWorker;
use Workers\Extras\Logger;
use Workers\Extras\Timer;
use Workers\Exceptions\WorkerTimeOutException;

class Worker extends AbstractWorker {
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

                (new $callback)($msg);
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
                    ]));
            }

        });
    }
}
