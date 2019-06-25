<?php
/**
 * contains tasks interface
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
declare(strict_types = 1);

namespace Worker\Casecade;
/**
 * Interface TaskInterface | Declares necessarily methods for tasks
 * @package Worker\Casecade
 */
interface TaskInterface {
    /**
     * returns a task instance
     *
     * @return TaskInterface
     */
    public static function connect(): TaskInterface;

    /**
     * sends message to $queue
     *
     * @param string $queue
     * @return string
     */
    public function run(string $queue): string;

    /**
     * sets task finish message
     *
     * @param string|NULL $message
     * @return TaskInterface
     */
    public function message(string $message = NULL): TaskInterface;

    /**
     * closes connection and destroy task instance
     */
    public static function close(): void ;
}