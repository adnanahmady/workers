<?php

/**
 * contains log configurations
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
return [
    'name' => env('LOG_NAME', 'logger'),
    /**
     * terminal, file
     */
    'type' => env('LOG_TYPE', 'file'),
    /**
     * storage log path
     */
    'path' => env('LOG_PATH', '/logs/logs.log'),
    'block' => [
        'path' => env('LOG_BLOCK_PATH')
    ]
];