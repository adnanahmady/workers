<?php
/**
 * contains RabbitMQ connection and queue configurations
 *
 * @author adnan ahmady <adnan.ahmady1394@gmail.com>
 * @copyright 2019 Hamyaraval Corporation
 */
global $queue_name;

return [
  'host'         => env('host',  'rabbit'),
  'port'         => env('port',  5672),
  'user'         => env('user',  'guest'),
  'pass'         => env('pass', 'guest'),
  'queue'        => [
    'order'         => $queue_name . '.' . env('queue_order', 'order'),
    'single'        => $queue_name . '.' . env('queue_single', 'single'),
    'priority'      => $queue_name . '.' . env('queue_priority', 'priority'),
    'success'       => $queue_name . '.' . env('queue_success', 'success'),
    'fails'         => $queue_name . '.' . env('queue_fails', 'fails'),
    'block'         => $queue_name . '.' . env('QUEUE_BLOCK', 'block'),
  ],
];