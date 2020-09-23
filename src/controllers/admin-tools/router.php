<?php

/**
 * Admin tools routes
 */

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') {
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

    $this->get('/photography-permissions-export.csv', function () {
      include 'reports/photo-permissions.php';
    });

    $this->get('/pending-payments-data-export.csv', function () {
      include BASE_PATH . 'controllers/payments/admin/reports/pending-payments.csv.php';
    });
  });

  $this->group('/editors', function () {
    $this->get('/member-se-categories', function () {
      include 'editors/membership-cat/editor.php';
    });

    $this->get('/', function () {
      include 'editors/home.php';
    });

    $this->group('/squad-membership', function () {
      $this->get('/', function () {
        include 'editors/member-squad-editor/editor.php';
      });

      $this->post('/add-remove', function () {
        include 'editors/member-squad-editor/add-remove.php';
      });
    });
  });

  $this->group('/galas', function () {
    $this->get('/', function () {
      include 'galas/home.php';
    });

    $this->group('/sdif', function () {
      // $this->get('/', function () {
      //   include 'galas/sdif/home.php';
      // });
  
      $this->get('/upload', function () {
        include 'galas/sdif/upload.php';
      });

      $this->post('/upload', function () {
        include 'galas/sdif/uploadPost.php';
      });
    });
  });

  $this->group('/member-upload', function () {
    include 'swimmer-upload/router.php';
  });
}