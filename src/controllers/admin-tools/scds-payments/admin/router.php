<?php

if (!isset($_SESSION['SCDS-Payments-Admin'])) {
  $this->get(['/', '/*'], function () {
    halt(404);
  });
} else {

  app()->adminCurrentTenant = Tenant::fromId($_SESSION['SCDS-Payments-Admin']['tenant']);
  app()->adminCurrentUser = new User($_SESSION['SCDS-Payments-Admin']['user']);

  $this->get('/', function () {
    include 'home.php';
  });

  $this->get('/exit', function () {
    include 'exit.php';
  });

}
