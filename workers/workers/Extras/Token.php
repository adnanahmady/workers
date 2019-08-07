<?php

namespace Worker\Extras;

use Worker\Models\Login;

class Token
{
    const WAIT_FOR_LOGIN = 3;

    public function make(): string
    {
        do {
            $expiration = NULL;

            $token = Login::find(
                [], [
                    'sort' => ['_id' => - 1],
                    'limit' => 1,
                    'projection' => [
                        'token' => 1,
                        'expiration' => 1,
                        '_id' => 0
                    ]
                ]
            )->toArray();

            foreach ($token as $value) {
                $token      = ($value['token']);
                $expiration = ($value['expiration']);
            }

            if ($expiration == NULL || (new Timer())->lessThanOrEqual($expiration . ' - 3 Minute')) {
                sendTask(
                    config('rabbit.queue.priority'),
                    'login'
                );
                sleep(static::WAIT_FOR_LOGIN);
            }
        } while (
            !(
                $expiration !== NULL &&
                (new Timer())->greaterThanOrEqual($expiration . ' - 3 Minute')
            )
        );

        return $token;
    }
}