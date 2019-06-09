<?php

function env($environment, $default = '') {
    $getenv = getenv(strtoupper($environment));
    $condition = ($getenv !== false && ! empty($getenv));

    return $condition ? $getenv : $default;
}

/**
 * @return mixed
 * @throws Exception
 */
function getParam($name) {
    $queue_name = getArgv($name);

    if (empty($queue_name)) {
        throw new Exception($name . ' name must be set');
    }

    return $queue_name;
}

function makeDir($file) {
    $path = explode('/', $file);
    array_pop($path);
    $newPath = '';
    foreach ($path as $dir) {
        $newPath .= $dir . '/';
        if (! is_dir($newPath)) {
            mkdir($newPath);
        }
    }
}

function getFileName($file) {
    $path = explode('/', $file);
    $fileName = array_shift(explode('-', array_pop($path)));

    return $fileName;
}

/**
 * @param       $name
 * @param array $argv
 *
 * @return mixed
 */
function getArgv($name) {
    global $argv;

    $newName = explode(
        ':',
        array_values(preg_grep('/^' . $name . ':(\w+)/i', $argv))[0]
    );
    array_shift($newName);
    $queue_name = implode(':', $newName);

    return $queue_name;
}
