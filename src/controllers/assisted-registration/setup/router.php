<?php

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser'])) {
  if ($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage'] == 1 || $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage'] == 2) {
    $this->get('/get-started', function() {
      include 'getStarted.php';
    });

    $this->get('/confirm-details', function() {
      include 'confirmDetails.php';
    });

    $this->post('/confirm-details', function() {
      include 'confirmDetailsPost.php';
    });
  } else if ($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegStage'] == 3) {
    $this->get('/go-to-onboarding', function() {
      include 'goToOnboarding.php';
    });
  }
}

$this->get('/{id}:int/{password}', function($id, $password) {
  include 'beginRegistration.php';
});