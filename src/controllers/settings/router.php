<?php

$this->get('/welcome-pack', function() {
  include 'WelcomePackSettings.php';
});

$this->get('/welcome-pack/preview', function() {
  include BASE_PATH . 'controllers/registration/welcome-pack/PDF.php';
});

$this->post('/welcome-pack', function() {
  include 'WelcomePackSettings.php';
});