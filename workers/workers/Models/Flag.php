<?php
/**
 * contains Flag class
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Models;

use Dompdf\FrameDecorator\NullFrameDecorator;
use Psr\Log\NullLogger;
use Worker\Interfaces\DeleteInterface;
use Worker\Interfaces\FlagInterface;
use Worker\Core\Model;

/**
 * Class Flag
 *
 * @package Worker\Models
 */
class Flag extends Model implements FlagInterface, DeleteInterface {
    public function set(String $value)
    {
        static::updateOne(
            ['_id' => 1],
            ['$set' =>
                [
                    'flag' => $value
                ]
            ],
            ['upsert' => true]);
    }

    public function get()
    {
        $result = static::findOne(['_id' => 1], ['projection' => ['_id' => 0, 'flag' => 1]]);

        return isset($result['flag']) ? $result['flag'] : NULL;
    }

    public function delete(): bool
    {
        if (static::get() === NULL) {
            return FALSE;
        }

        $result = static::updateOne(
            ['_id' => 1],
            ['$unset' =>
                [
                    'flag' => true
                ]
            ]);

        return $result->getModifiedCount();
    }
}