<?php

namespace Workers\Models;

use Workers\Core\Model;

class Passenger extends Model {

    protected $connection = 'hamyar';

    /**
     * Get passengers wallet amount
     *
     * @param $args
     * @return int
     * @throws Exception
     */
    public function getAmount($args)
    {
        $options = [
            'projection' => ['wallet_amount' => 1, '_id' => 1]
        ];
        $res = static::Connect()->find(array("_id" => (int) $args['user_id']), $options);

        return (!empty($res)) ? $res[0]['wallet_amount'] : 0;
    }

    /**
     * Update passengers wallet amount
     *
     * @param $args
     * @return bool
     * @throws Exception
     */
    public function updateWallet($args) {
        $passengerAmount = $this->getAmount($args);
        $args['amount'] = $passengerAmount + $args['amount'];
        $result = static::Connect()->updateOne(
            array(
                '_id' => (int) $args['user_id']
            ),
            array(
                '$set' => array('wallet_amount' => $args['amount'])
            ),
            array(
                'upsert' => false
            )
        );

        if ($result->getModifiedCount()) {
            return true;
        } else {
            return false;
        }
    }
}