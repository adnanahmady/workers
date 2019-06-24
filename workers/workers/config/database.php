<?php

return [
    'mongo'         => [
        /**
         * drivers are [mongodb, pgsql, mysql, odbc]
         */
        'driver' => env('MONGO_DRIVER', 'mongodb'),
        'db' => env('MONGO_DB', 'test'),
        'user' => env('MONGO_USER', 'root'),
        'pass' => env('MONGO_PASS', 'secret'),
        'host' => env('MONGO_HOST', 'mongo'),
        'port' => env('MONGO_PORT', 27017),
    ],
    'hamyar'         => [
        /**
         * drivers are [mongodb, pgsql, mysql, odbc]
         */
        'driver' => env('MONGO2_DRIVER', 'mongodb'),
        'db' => env('MONGO2_DB', 'test'),
        'user' => env('MONGO2_USER', 'root'),
        'pass' => env('MONGO2_PASS', 'secret'),
        'host' => env('MONGO2_HOST', 'mongo'),
        'port' => env('MONGO2_PORT', 27017),
    ],
];