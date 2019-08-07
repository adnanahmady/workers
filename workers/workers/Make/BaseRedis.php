<?php

namespace Worker\Models\Redis;

use Worker\Core\RedisModel;

class BaseRedis extends RedisModel
{
    public static function __callStatic($name, $arguments)
    {
        call_user_func_array([static::class, $name], $arguments);
    }
}