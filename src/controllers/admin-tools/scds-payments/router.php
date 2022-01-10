<?php

$this->get('/', function() {
  if (app()->user->hasPermission('SCDSPaymentsManager')) {
    include 'redirect.php';
  } else {
    include 'no-access.php';
  }
});

$this->group('/setup-direct-debit', function() {
  $this->get('/', function() {
    include 'direct-debit-setup.php';
  });

  $this->get('/success', function() {
    include 'direct-debit-setup-success.php';
  });
});