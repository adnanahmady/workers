<?php

$dir = scandir(__DIR__);
unset($dir[0], $dir[1]);
$return = [];
foreach($dir as $file) {
    if (basename(__FILE__) === $file) { continue; }
    foreach(
        (include (__DIR__ . "/{$file}"))
        as
        $key => $value
    ) {
        $return[$key] = $value;
    }
}

return $return;