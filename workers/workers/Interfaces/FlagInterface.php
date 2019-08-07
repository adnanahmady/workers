<?php

namespace Worker\Interfaces;

interface FlagInterface {
    /**
     * set Flag key value
     *
     * @param String $value
     */
    public function set(String $value);

    /**
     * return Flags value
     *
     * @return Null|String
     */
    public function get();
}