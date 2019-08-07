<?php
/**
 * contains callbacks interface
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
namespace Worker\Interfaces;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface CallbackInterface
 *
 * @package Worker\Casecade
 */
interface CallbackInterface {
    /**
     * main callbacks workspace
     *
     * all callbacks have to use this magic method
     * for running there operations on tasks
     *
     * @param AMQPMessage $msg
     * @return AMQPMessage
     */
    public function __invoke(AMQPMessage $msg): AMQPMessage;
}