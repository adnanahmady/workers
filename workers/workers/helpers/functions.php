<?php

function env($environment, $default = '') {
    $getenv = getenv(strtoupper($environment));
    $condition = ($getenv !== false && ! empty($getenv));

    return $condition ? $getenv : $default;
}

function app($path, $default = '') {
    $app = Workers\Core\Core::getConfig();
    $path = explode('.', strtolower($path));
    foreach($path as $index) {
        try {
            $app = $app[$index];
        } catch (\Throwable $e) {
            return $default;
        }
    }

    return $app;
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
 * Gets cli key:value pares argument
 *
 * @param $name
 * @param bool $exception
 * @return string
 * @throws InvalidArgumentException
 */
function getParam($name, $exception = false) {
    global $argv;
    $newName = explode(
        ':',
        array_values(preg_grep('/^' . $name . ':(\w+)/i', $argv))[0]
    );
    array_shift($newName);
    $argName = implode(':', $newName);

    if (empty($queue_name) && $exception === true) {
        throw new \InvalidArgumentException($name . ' name must be set');
    }

    return $argName;
}
