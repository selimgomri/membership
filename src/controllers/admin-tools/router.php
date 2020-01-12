<?php

/**
 * Admin tools routes
 */

if ($_SESSION['AccessLevel'] == 'Admin') {
  $this->get('/', function () {
    include 'home.php';
  });

  $this->group('/reports', function () {
    $this->get('/', function () {
      include 'reports/home.php';
    });

    $this->get('/membership-data-export.csv', function () {
      include 'reports/membership-data.php';
    });

    $this->get('/pending-payments-data-export.csv', function () {
      include BASE_PATH . 'controllers/payments/admin/reports/pending-payments.csv.php';
    });
  });

  $this->group('/editors', function () {
    $this->get('/member-se-categories', function () {
      include 'editors/membership-cat/editor.php';
    });
  });
}