<?php

$this->get('/', function() {
  if (app()->user->hasPermission('SCDSPaymentsManager')) {
    include 'redirect.php';
  } else {
    include 'no-access.php';
  }
});