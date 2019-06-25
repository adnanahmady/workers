<?php

/**
 * contains log configurations
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
return [
    'log' => [
        'name' => env('LOG_NAME', 'terminalLog'),
        /**
         * terminal, file
         */
        'type' => env('LOG_TYPE', 'file'),
    ]
];