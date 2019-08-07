<?php
/**
 * contains endpoints
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
return [
    'accounting_plan' => [
        'endpoint' => env('ACCOUNTING_PLAN_ENDPOINT'),
        'recpay' => env('ACCOUNTING_PLAN_ENDPOINT') . 'RecPay',
    ],
    'saman' => [
        'normal_transfer'   => env('SAMAN_NORMAL_TRANSFER_URL'),
        'sheba'             => env('SAMAN_SHEBA_TRANSFER_URL'),
        'check_sheba'       => env('SAMAN_SHEBA_TRANSFER_CHECK_URL')
    ],
];