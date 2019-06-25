<?php
namespace Worker\Models;
use Worker\Core\Model;

class Driver extends Model {

    protected $connection = 'hamyar';

    protected $collection = 'people';

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getAmount($filter, $usePhone = false) {
        return DriverReferralList::getAmount($filter, $usePhone);
    }

    /**
     * Update drivers Wallet amount
     *
     * @param $args
     * @return int
     */
    public function updateWallet($filter, $amount, $plus = false) {
        return DriverReferralList::updateWallet($filter, $amount, $plus);
    }

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getDriver($filter)
    {
        $filter = (is_array($filter) ? $filter : ['phone' => (string) $filter]);
        $options = [
            'projection' => ['_id' => 1]
        ];
        $res = static::find($filter, $options)->toArray();

        return (!empty($res) && isset($res[0]['_id'])) ? $res[0]['_id'] : 'no driver';
    }

}