<?php

namespace Worker\Reflector;

use Worker\Abstracts\AbstractReflector;
use Worker\Interfaces\DeleteInterface;
use Worker\Interfaces\FlagInterface;

class BaseReflector extends AbstractReflector implements FlagInterface, DeleteInterface
{
    protected function reflect()
    {
        return $this->getReflectingClass();
    }

    public function set(String $value)
    {
        $this->reflect()->set($value);
    }

    public function get()
    {
        return $this->reflect()->get();
    }

    public function delete(): bool
    {
        return $this->reflect()->delete();
    }

    public function isEqualTo($value = '')
    {
        return $this->reflect()->get() == $value;
    }

    public function isEmpty()
    {
        return ! $this->isSet() && $this->isEqualTo();
    }

    public function isSet()
    {
        return $this->reflect()->get() === NULL;
    }
}