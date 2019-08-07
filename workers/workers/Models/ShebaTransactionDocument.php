<?php

namespace Worker\Models;

use Worker\Core\Model;
use Worker\Traits\CheckTransactionTrait;

class ShebaTransactionDocument extends Model
{
    use CheckTransactionTrait;

    /**
     * Updates Sheba collection with Bank Result
     *
     * @param $transactionId
     * @param $response
     * @param $trackerId
     *
     * @return object
     */
    public static function insertBankResult($transactionId, $response, $trackerId)
    {
        $response['Date(miladi)'] = date('Y-m-d');
        $response['date(shamsi)'] = jdate(time())->format('Y-m-d');
        $response['Time(API)'] = date('H:i:s');
        $response['trackerId'] = $trackerId;
        return static::updateOne(
            ['_id' => $transactionId],
            ['$set' => $response]
        );
    }

    /**
     * Updates Users wallet amount based on their detail number
     *
     * @param      $data
     * @param bool $plus
     *
     * @return bool|int
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    public function updateUserWallet($data, $plus = FALSE)
    {
        $this->checkWallet($data, static::class);

        if (preg_match('/^70/', $data['tafzil']))
        {
            return Passenger::updateWallet(
                ['detail_code' => $data['tafzil']],
                $data['amount'],
                $plus
            );
        }

        return Driver::updateWallet(
            ['detail_code' => $data['tafzil']],
            $data['amount'],
            $plus
        );
    }
}