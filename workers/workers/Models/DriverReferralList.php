<?php
namespace Workers\Models;
use Workers\Core\Model;

class DriverReferralList extends Model {

    protected $connection = 'hamyar';

    protected $collection = 'driver_referral_list';

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getAmount($args)
    {
        $options = [
            'projection' => ['registered_driver_wallet' => 1]
        ];
        $res = static::Connect()
            ->find(array("registered_driver_id" => (int) $args['user_id']), $options)
            ->toArray();

        return (!empty($res)) ? $res[0]['registered_driver_wallet'] : 0;
    }

    /**
     * Update drivers Wallet amount
     *
     * @param $args
     * @return int
     */
    public function updateWallet($args) {
        $driverAmount = static::getAmount($args);
        $args['amount'] = $driverAmount + $args['amount'];
        $result = static::Connect()->updateOne(
            array(
                'registered_driver_id' => (int) $args['user_id']
            ),
            array(
                '$set' => array('registered_driver_wallet' => $args['amount'])
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