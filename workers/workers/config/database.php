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
];