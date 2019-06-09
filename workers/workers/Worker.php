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
                if ((new Timer())->check()) {
                    (new $callback)($msg);
                } else {
                    throw new \Exception('UnTime Task Exception');
                }
            } catch (\Throwable $e) {
//                Logger::alert(
//                    Job::getJobName($msg),
//                    json_encode(Job::getJobData($msg)),
//                    [],
//                    json_encode([
//                        'message' => $e->getMessage() ? $e->getMessage() : 'NULL',
//                        'file' => $e->getFile() ? $e->getFile() : 'NULL',
//                        'line' => $e->getLine() ? $e->getLine() : 'NULL',
//                        'code' => $e->getCode() ? $e->getCode() : 'NULL',
//                    ]));
            }
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        });
    }
}
