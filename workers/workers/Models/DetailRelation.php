<?php

namespace Worker\Models;

use Worker\Core\Model;

class DetailRelation extends Model
{
    protected $connection = 'hamyar';

    /**
     * Get drivers wallet amount
     *
     * @param $args
     * @return int
     */
    public function getUser($filter)
    {
        $filter = (is_array($filter) ? $filter : ['detail_code' => (string) $filter]);
        $options = [
            'projection' => ['userid' => 1]
        ];
        $res = static::find($filter, $options)->toArray();

        return (!empty($res) && isset($res[0]['userid'])) ? $res[0]['userid'] : 0;
    }
}