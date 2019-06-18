<?php

return [
    'log' => [
        'name' => env('LOG_NAME', 'terminalLog'),
        /**
         * terminal, file
         */
        'type' => env('LOG_TYPE', 'file'),
    ]
];