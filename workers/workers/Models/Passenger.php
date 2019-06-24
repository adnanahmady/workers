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
        $res = static::Connect()->find(array("_id" => (int) $args['user_id']), $options)->toArray();
echo $res[0];
        return (!empty($res)) ? $res[0]['wallet_amount'] : 0;
    }

    /**
     * Update passengers wallet amount
     *
     * @param $args
     * @return bool
     * @throws Exception
     */
    public function updateWallet($args, $plus = false) {
        $passengerAmount = $this->getAmount($args);
        $args['amount'] = (! $plus) ?
            $passengerAmount - $args['amount'] :
            $passengerAmount + $args['amount'];
        if ($args['amount'] < 0) {
            return false;
        }

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