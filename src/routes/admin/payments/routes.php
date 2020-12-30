<?php

/**
 * Code for payment routes
 */

$this->get('/', function () {
  include BASE_PATH . 'controllers/global-admin/payments/home.php';
});


$this->group('/products', function () {
  $this->get('/', function () {
    include BASE_PATH . 'controllers/global-admin/payments/products/home.php';
  });

  $this->group('/new', function () {
    $this->get('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/products/new.php';
    });

    $this->post('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/products/new-post.php';
    });
  });

  $this->group('/{id}:int', function ($id) {
    $this->get('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/products/view.php';
    });

    $this->post('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/products/view-post.php';
    });
  });
});

$this->group('/tax-rates', function () {
  $this->get('/', function () {
    include BASE_PATH . 'controllers/global-admin/payments/tax-rates/home.php';
  });

  $this->group('/new', function () {
    $this->get('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/tax-rates/new.php';
    });

    $this->post('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/tax-rates/new-post.php';
    });
  });

  $this->group('/{id}:int', function ($id) {
    $this->get('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/tax-rates/view.php';
    });

    $this->post('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/tax-rates/view-post.php';
    });
  });
});

$this->group('/subscriptions', function () {
  $this->get('/', function () {
    include BASE_PATH . 'controllers/global-admin/payments/subscriptions/home.php';
  });

  $this->group('/new', function () {
    $this->get('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/subscriptions/new.php';
    });

    $this->post('/', function () {
      include BASE_PATH . 'controllers/global-admin/payments/subscriptions/new-post.php';
    });
  });

  $this->group('/{id}:int', function ($id) {
    $this->get('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/subscriptions/view.php';
    });

    $this->post('/', function ($id) {
      include BASE_PATH . 'controllers/global-admin/payments/subscriptions/view-post.php';
    });
  });
});
