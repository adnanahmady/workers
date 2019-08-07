<?php

namespace Worker\Models\Redis;

use Worker\Interfaces\DeleteInterface;
use Worker\Interfaces\FlagInterface;
use Worker\Core\RedisModel;

class Flag extends RedisModel implements FlagInterface, DeleteInterface
{
    public static function __callStatic($name, $arguments)
    {
        call_user_func_array([static::class, $name], $arguments);
    }

    public function set(String $value)
    {
        $this->connect()->set(basename(static::class), $value);
    }

    public function get()
    {
        return $this->connect()->get(basename(static::class));
    }

    public function delete(): bool
    {
        return $this->connect()->del(basename(static::class));
    }
}