<?php
/**
 * contains global helper functions
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */

/**
 * returns specified environment variable
 * or returns default value
 *
 * @param $environment
 * @param string $default
 * @return array|string
 */
function env($environment, $default = '') {
    $getenv = getenv(strtoupper($environment));
    $condition = ($getenv !== false && ! empty($getenv));

    return $condition ? $getenv : $default;
}

/**
 * returns specified configuration field
 * if not exist returns default value
 *
 * @param $path
 * @param string $default
 * @return mixed|string
 */
function app($path, $default = '') {
    $app = Worker\Core\Core::getConfig();
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

/**
 * makes specified path directories
 *
 * @param $file
 */
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

/**
 * get downloaded excel file name from address
 *
 * @param $file
 * @return mixed
 */
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

    if (empty($argName) && $exception === true) {
        throw new \InvalidArgumentException($name . ' name must be set');
    }

    return $argName;
}
