<?php

$this->get('/', function () {
  include BASE_PATH . 'controllers/checkout/home.php';
});

$this->group('/v1', function () {
  
});