<?php

namespace Worker\Interfaces;

interface DeleteInterface
{
    /**
     * Delete|Unset a key
     *
     * @return bool
     */
    public function delete(): bool;
}