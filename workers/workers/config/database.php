<?php

return [
    'db' => [
        'type' => env('DB_TYPE', 'mongodb'),
    ],
    'mongo'         => [
        'db' => env('MONGO_DB', 'test'),
        'user' => env('MONGO_USER', 'root'),
        'pass' => env('MONGO_PASS', 'secret'),
        'host' => env('MONGO_HOST', 'mongo'),
        'port' => env('MONGO_PORT', 27017),
    ],
    'mongo2'         => [
        'db' => env('MONGO2_DB', 'test'),
        'user' => env('MONGO2_USER', 'root'),
        'pass' => env('MONGO2_PASS', 'secret'),
        'host' => env('MONGO2_HOST', 'mongo'),
        'port' => env('MONGO2_PORT', 27017),
    ],
];