<?php

namespace Worker\Extras;

class Transform {
    private $string;

    public function __construct($string) {
        $newString = '';

        foreach (preg_split('/_/', $string) as $value):
            $newString .= ucfirst($value);
        endforeach;

        $this->string = $newString;
    }

    public function __toString(): string
    {
        $file = '\\Worker\\Callbacks\\' . $this->string;
        $path = dirname(__DIR__) . '/Callbacks/' . $this->string . 'Callback.php';

        return ( file_exists($path) ? $file . 'Callback' : $file );
    }
}