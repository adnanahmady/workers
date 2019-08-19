<?php
/**
 * contains database connection configurations
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
return [
    'mongo'         => [
        /**
         * drivers are [mongodb, pgsql, mysql, sqlsrv, odbc]
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
         * drivers are [mongodb, pgsql, mysql, sqlsrv, odbc]
         */
        'driver' => env('MONGO_HAMYAR_DRIVER', 'mongodb'),
        'db' => env('MONGO_HAMYAR_DB', 'test'),
        'user' => env('MONGO_HAMYAR_USER', 'root'),
        'pass' => env('MONGO_HAMYAR_PASS', 'secret'),
        'host' => env('MONGO_HAMYAR_HOST', 'mongo'),
        'port' => env('MONGO_HAMYAR_PORT', 27017),
    ],
];