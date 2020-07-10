<?php

$this->get('/', function() {
  include BASE_PATH . 'views/login/login.php';
});

$this->post('/', function() {
  include BASE_PATH . 'controllers/login/login.php';
});

$this->post('/2fa', function() {
  include BASE_PATH . 'controllers/login/2fa.php';
});

$this->get('/pass', function() {
  include BASE_PATH . 'controllers/login/pass.php';
});