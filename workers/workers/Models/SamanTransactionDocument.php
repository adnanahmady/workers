<?php

namespace Worker\Models;

use Worker\Core\Model;

class SamanTransactionDocument extends Model
{
    public static function insertBankResult($transactionId, $response, $trackerId)
    {
        $response['Date(miladi)'] = date('Y-m-d');
        $response['date(shamsi)'] = jdate(time())->format('Y-m-d');
        $response['Time(API)'] = date('H:i:s');
        $response['trackerId'] = $trackerId;
        static::updateOne(
            ['_id' => $transactionId],
            ['$set' => $response]
        );
    }
}