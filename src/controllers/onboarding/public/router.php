<?php

$this->get('/', function () {
  if (!isset($_SESSION['OnboardingSessionId'])) {
    include 'init.php';
  } else {
    include 'start.php';
  }
});

$this->get('/start-task', function () {
});

$this->get('/error', function () {
});

$this->get('/wrong-account', function () {
  include 'logged-in.php';
});
