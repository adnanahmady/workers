<?php

namespace Worker\Core;

class RedisModel {
    protected $conn;

    public function connect()
    {
        if ($this->conn === null) {
            $this->conn = new RedisConnection(env('REDIS_CONNECTION', NULL));
        }


        return $this->conn->getConnection();
    }
}