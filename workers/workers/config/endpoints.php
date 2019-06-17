<?php

return [
    'accounting_plan' => [
        'endpoint' => env('ACCOUNTING_PLAN_ENDPOINT'),
        'recpay' => env('ACCOUNTING_PLAN_ENDPOINT') . 'RecPay',
    ],
    'saman' => [
        'normal_transfer' => env('SAMAN_NORMAL_TRANSFER_URL'),
    ],
];