<?php

return [
  'rabbit_host'         => env('rabbit_host',  'rabbit'),
  'rabbit_port'         => env('rabbit_port',  5672),
  'rabbit_user'         => env('rabbit_user',  'guest'),
  'rabbit_pass'         => env('rabbit_pass', 'guest'),
  'rabbit_queues'        => [
    'order'     => env('rabbit_queue_order', $queue_name . '.order'),
    'priority'  => env('rabbit_queue_priority', $queue_name . '.priority'),
    'success'   => env('rabbit_queue_success', $queue_name . '.success'),
    'fails'     => env('rabbit_queue_fails', $queue_name . '.fails'),
  ],
];