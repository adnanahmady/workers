<?php

namespace Worker\Core;

use Worker\Exceptions\InvalidFieldException;

class Make {
    /**
     * @var string $ext files extension
     */
    public $ext = '.php';

    /**
     * @var array $paths make valid options and their destination paths
     */
    protected $paths = [
        'callback' => 'workers/Callbacks/',
        'interface' => 'workers/Interfaces/',
        'extra' => 'workers/Extras/',
        'exception' => 'workers/Exceptions/',
        'model' => 'workers/Models/',
        'redis' => 'workers/RedisModels/',
        'reflector' => 'workers/Reflector/',
        'abstract' => 'workers/Abstracts/',
        'trait' => 'workers/Traits/',
        'config' => 'workers/config/',
    ];

    /**
     * @var array $nameOnly sets make options that must be ignored
     *                      of attach make option to end of file name
     */
    protected $nameOnly = ['extra', 'redis', 'model', 'config'];

    public function getPath($key)
    {
        return $this->paths[$key];
    }

    /**
     * Makes a valid file based on make:[file]
     * and name and in file words with name:[filename]
     *
     * like: php maker make:callback name:ExampleCallback
     *   OR php maker make:callback name:Example
     *
     * @return string
     * @throws \ErrorException
     * @throws \Worker\Exceptions\InvalidFieldException
     */
    public function make()
    {
        $getName = getParam('name');
        $getMake = getParam('make');

        if (! $getName) {
            throw new \InvalidArgumentException('No "name" Argument');
        }

        if (! $this->getPath($getMake)) {
            throw new \InvalidArgumentException("No \"$getMake\" Specified");
        }
        $makePath = dirname(__DIR__) . '/Make/';
        $path = dirname(__DIR__, 2) . '/' . $this->getPath($getMake);
        $baseFile = "Base" . ucfirst($getMake);
        $newFile = preg_match("/$getMake/i", $getName) || in_array($getMake, $this->nameOnly)
            ? $getName : $getName . ucfirst($getMake);

        if (! file_exists("$makePath$baseFile$this->ext")) {
            throw new InvalidFieldException("No file with $getMake type exists in make repository.");
        }

        if (file_exists("$path$newFile$this->ext")) {
            throw new InvalidFieldException("\"$newFile\" Already exists.");
        }
        $content = str_replace(
            "$baseFile",
            "$newFile",
            file_get_contents("$makePath$baseFile$this->ext")
        );

        if (! file_put_contents("$path$newFile$this->ext", $content)) {
            throw new \ErrorException("Coul\'nt Create \"$newFile\".");

        }
        $fileType = in_array($getMake, $this->nameOnly) ? $getMake : '';

        return "\"$newFile\" $fileType Successfully created.";
    }
}