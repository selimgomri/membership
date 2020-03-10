<?php

$this->get('/', function() {
  include 'controllers/pwa/set-pwa.php';
});

$this->get('/offline', function() {
  include BASE_PATH . 'views/pwa/offline.php';
});