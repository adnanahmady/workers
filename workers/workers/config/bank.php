<?php
/**
 * contains bank configurations
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
return [
    'saman' => [
        'login' => [
            'channel' => env('SAMAN_LOGIN_CHANNEL'),
            'user' => env('SAMAN_LOGIN_USERNAME'),
            'pass' => env('SAMAN_LOGIN_PASSWORD'),
            'secret_key' => env('SAMAN_LOGIN_SECRETKEY'),
            'url' => env('SAMAN_LOGIN_URL'),
        ],
    ],
];