<?php
/**
 * contains DriverReferralList Class
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Models;

use Worker\Core\Model;

/**
 * Class DriverReferralList
 *
 * @package Worker\Models
 */
class DriverReferralList extends Model {

    /**
     * @var string connection name
     */
    protected $connection = 'hamyar';

    /**
     * @var string name of the collection that model must connect to regardless models name
     */
    protected $collection = 'driver_referral_list';

    /**
     * @const minimum of drivers amount that must remain
     */
    const MIN_DRIVER_AMOUNT = 200000;

    /**
     * Get drivers wallet amount
     *
     * @param $filter
     * @param bool $usePhone
     * @return int
     */
    public function getAmount($filter, $usePhone = false)
    {
        $filter = (! $usePhone) ? $filter : DetailRelation::getUser($filter);
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
     * @param $filter
     * @param $amount
     * @param bool $plus
     * @return bool
     */
    public function updateWallet($filter, $amount, $plus = false) {
        $filter = DetailRelation::getUser($filter);
        $filter = (is_array($filter) ? $filter : ['registered_driver_id' => (int) $filter]);
        $driverAmount = static::getAmount($filter);
        $amount = (! $plus) ?
            $driverAmount - $amount :
            $driverAmount + $amount;

        if ($amount < static::MIN_DRIVER_AMOUNT && ! $plus) {
            return false;
        }

        $result = static::updateOne($filter,
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

    /**
     * Check drivers Wallet amount
     *
     * @param $filter
     * @param $amount
     * @param bool $plus
     * @return bool
     */
    public function checkWallet($filter, $amount, $plus = false) {
        $filter = DetailRelation::getUser($filter);
        $filter = (is_array($filter) ? $filter : ['registered_driver_id' => (int) $filter]);
        $driverAmount = static::getAmount($filter);
        $amount = (! $plus) ?
            $driverAmount - $amount :
            $driverAmount + $amount;

        if ($amount < static::MIN_DRIVER_AMOUNT && ! $plus) {
            return false;
        }

        return true;
    }
}