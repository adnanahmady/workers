<?php

namespace Worker\Abstracts;

use Worker\Interfaces\FlagInterface;

class AbstractReflector
{
    protected $reflectingClass;

    public function __construct(FlagInterface $reflectingClass)
    {
        $this->setReflectingClass($reflectingClass);

        return $this->getReflectingClass();
    }

    public function setReflectingClass($reflectingClass)
    {
        $this->reflectingClass = $reflectingClass;
    }

    /**
     * @return mixed
     */
    public function getReflectingClass()
    {
        return $this->reflectingClass;
    }
}