<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS);

require BASE_PATH . 'vendor/autoload.php';
require "helperclasses/ClassLoader.php";

if (getenv('ENV_JSON_FILE')) {
  require 'common/env/loader.php';
}