<?php

$this->get('/', function() {
  include BASE_PATH . 'views/login/login.php';
});

$this->post('/', function() {
  include BASE_PATH . 'controllers/login/login.php';
});