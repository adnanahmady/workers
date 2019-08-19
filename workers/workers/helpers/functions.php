<?php
/**
 * contains global helper functions
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */

function loadEnvironments($path)
{
    $dotenv = \Dotenv\Dotenv::create($path);
    $dotenv->load();
}
loadEnvironments(dirname(__DIR__, 2));

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
function config($path, $default = '') {
    $config = Worker\Core\Core::getConfig();
    $path = explode('.', strtolower($path));
    foreach($path as $index) {
        try {
            $config = $config[$index];
        } catch (\Throwable $e) {
            return $default;
        }
    }

    return $config;
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
 * create and remove a temp file in /tmp path
 *
 * @param null $data
 * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
 */
function tempFile($data = null)
{
    $file = tempnam(sys_get_temp_dir(), 'excel_');
    $handle = fopen($file, 'w');
    fwrite($handle, $data);
    $return = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    fclose($handle);
    unlink($file);

    return $return;
}

/**
 * Gets cli key:value pares argument
 *
 * @param $name
 * @param bool $exception
 * @param string $separator
 * @return string
 * @throws InvalidArgumentException
 */
function getParam($name, $exception = false, $separator = ':') {
    global $argv;
    $newName = explode(
        $separator,
        array_values(preg_grep('/^' . $name . $separator . '(\w+)/i', $argv))[0]
    );
    array_shift($newName);
    $argName = implode(':', $newName);

    if (empty($argName) && $exception === true) {
        throw new \InvalidArgumentException($name . ' name must be set');
    }

    return $argName;
}

function sendTask($queue, $job, $data = [], $success = [], $fails = [], $date = NULL) {
    \Worker\Task::connect()->channel()->queue($queue)->basic_publish(
        new \Worker\Extras\Job($job, $data, $success, $fails, $date)
    );
}

function setInterval($callback, $sleep)
{
    while (true)
    {
        $callback();
        sleep($sleep);
    }
}

function setTimeout($callback, $sleep)
{
    sleep($sleep);
    $callback();
}