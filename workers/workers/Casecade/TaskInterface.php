<?php
declare(strict_types = 1);

namespace Worker\Casecade;

interface TaskInterface {

    public static function connect(): TaskInterface;

    public function run(string $queue): string;

    public function message(string $message = NULL): TaskInterface;

    public static function close();
}