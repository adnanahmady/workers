<?php

namespace Workers;

use Workers\Abstracts\AbstractWorker;
use Workers\Extras\Logger;
use Workers\Extras\Timer;

class Worker extends AbstractWorker {
    public function channel() {
        parent::channel();
        return $this->callback(function ($msg) {
            $callback = $this->getJobName($msg);
            try {
                if (getArgv('time') === 'block') {
                    $wait = (time() - (strtotime(getArgv('start') . ' +1 day')));

                    if ($wait < 0) {
                        sleep($wait * -1);
                    }
                }

                if (! (new Timer())->check()) {
                    $this->ack($msg);
                    throw new \Exception('UnTime Task Exception');
                }

                (new $callback)($msg);
            } catch (\Throwable $e) {
                Logger::alert(
                    Job::getJobName($msg),
                    json_encode(Job::getJobData($msg)),
                    [],
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
