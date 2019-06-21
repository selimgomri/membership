<?php

if (isset($_SESSION['AssRegGuestUser'])) {
  if ($_SESSION['AssRegStage'] == 1 || $_SESSION['AssRegStage'] == 2) {
    $this->get('/get-started', function() {
      include 'getStarted.php';
    });

    $this->get('/confirm-details', function() {
      include 'confirmDetails.php';
    });

    $this->post('/confirm-details', function() {
      include 'confirmDetailsPost.php';
    });
  } else if ($_SESSION['AssRegStage'] == 3) {
    $this->get('/go-to-onboarding', function() {
      include 'goToOnboarding.php';
    });
  }
}

$this->get('/{id}:int/{password}', function($id, $password) {
  include 'beginRegistration.php';
});