<?php

$this->get('/', function() {
  include BASE_PATH . 'views/root/home.php';
});

$this->group('/gc', function() {
  $this->get('redirect', function() {
    include BASE_PATH . 'controllers/settings/gocardless/register-redirect.php';
  });
});