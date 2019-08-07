<?php

namespace Worker\Models;

use Worker\Core\Model;

class Passenger extends Model {

    protected $connection = 'hamyar';

    const MIN_PASSENGER_AMOUNT = 0;

    /**
     * Get passengers wallet amount
     *
     * @param $filter
     *
     * @return int
     */
    public function getAmount($filter)
    {
        $filter = (is_array($filter) ? $filter : ['_id' => $filter]);
        $options = [
            'projection' => ['wallet_amount' => 1, '_id' => 1]
        ];
        $res = static::find($filter, $options)->toArray();

        return (!empty($res) && isset($res[0]['wallet_amount'])) ? $res[0]['wallet_amount'] : 0;
    }

    /**
     * Update passengers wallet amount
     *
     * @param      $filter
     * @param      $amount
     * @param bool $plus
     *
     * @return bool
     */
    public function updateWallet($filter, $amount, $plus = false) {
        $filter = (is_array($filter) ? $filter : ['detail_code' => $filter]);
        $filter = DetailRelation::getUser($filter);
        $filter = (is_array($filter) ? $filter : ['_id' => $filter]);
        $passengerAmount = static::getAmount($filter);
        $amount = (! $plus) ?
            $passengerAmount - $amount :
            $passengerAmount + $amount;

        if ($amount < static::MIN_PASSENGER_AMOUNT && ! $plus) {
            return false;
        }
        $result = static::updateOne($filter,
            [
                '$set' => [
                    'wallet_amount' => (int) $amount
                ]
            ],
            ['upsert' => false]
        );

        if ($result->getModifiedCount()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check passengers wallet amount
     *
     * @param      $filter
     * @param      $amount
     * @param bool $plus
     *
     * @return bool
     */
    public function checkWallet($filter, $amount, $plus = false) {
        $filter = (is_array($filter) ? $filter : ['detail_code' => $filter]);
        $filter = DetailRelation::getUser($filter);
        $filter = (is_array($filter) ? $filter : ['_id' => $filter]);
        $passengerAmount = static::getAmount($filter);
        $amount = (! $plus) ?
            $passengerAmount - $amount :
            $passengerAmount + $amount;

        if ($amount < static::MIN_PASSENGER_AMOUNT && ! $plus) {
            return false;
        }

        return true;
    }
}