<?php

$this->get('/', function() {
  include 'set-pwa.php';
});

$this->get('/offline', function() {
  include BASE_PATH . 'views/pwa/offline.php';
});