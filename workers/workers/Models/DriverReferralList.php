<?php
namespace Worker\Models;
use Worker\Core\Model;

class DriverReferralList extends Model {

    protected $connection = 'hamyar';

    protected $collection = 'driver_referral_list';

    const MIN_DRIVER_AMOUNT = 200000;

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getAmount($filter, $usePhone = false)
    {
        $filter = (! $usePhone) ? $filter : Driver::getDriver($filter);
        $filter = (is_array($filter) ? $filter : ['registered_driver_id' => (int) $filter]);
        $options = [
            'projection' => ['registered_driver_wallet' => 1]
        ];
        $res = static::find($filter, $options)->toArray();

        return (!empty($res)) ? $res[0]['registered_driver_wallet'] : 0;
    }

    /**
     * Update drivers Wallet amount
     *
     * @param $args
     * @return int
     * [phone => '9309616418'], 90000
     */
    public function updateWallet($filter, $amount, $plus = false) {
        $filter = Driver::getDriver($filter);
        $filter = (is_array($filter) ? $filter : ['registered_driver_id' => (int) $filter]);
        $driverAmount = static::getAmount($filter);
        $amount = (! $plus) ?
            $driverAmount - $amount :
            $driverAmount + $amount;

        if ($amount < static::MIN_DRIVER_AMOUNT) {
            return false;
        }

        $result = static::Connect()->updateOne($filter,
            array(
                '$set' => array('registered_driver_wallet' => $amount)
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