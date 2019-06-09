<?php

namespace Workers\Core;

class Core {
  public function __call($method, $arguments) {
    require_once dirname(__DIR__) . '/helpers/functions.php';

    return call_user_func_array($method, $arguments);
  }
}