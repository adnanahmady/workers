<?php
namespace Workers\Models;
use Workers\Core\Model;

class Driver extends Model {

    protected $connection = 'hamyar';

    protected $collection = 'people';

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getAmount($args) {
        return DriverReferralList::getAmount($args);
    }

    /**
     * Update drivers Wallet amount
     *
     * @param $args
     * @return int
     */
    public function updateWallet($args) {
        return DriverReferralList::updateWallet($args);
    }
}