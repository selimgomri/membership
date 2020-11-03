<?php

$this->get('/', function () {
  require BASE_PATH . 'controllers/global-admin/dashboard.php';
});

$this->group('/login', function () {
  $this->get('/', function () {
    require BASE_PATH . 'controllers/global-admin/login/view.php';
  });

  $this->post('/', function () {
    require BASE_PATH . 'controllers/global-admin/login/post.php';
  });

  $this->post('/2fa', function () {
    require BASE_PATH . 'controllers/global-admin/login/post-2fa.php';
  });

  $this->group('/reset-password', function () {
    $this->get('/', function () {
      require BASE_PATH . 'controllers/global-admin/login/reset-password/view.php';
    });
  
    // $this->post('/', function () {
    //   require BASE_PATH . 'controllers/global-admin/login/reset-password/post.php';
    // });
  });
});

$this->group('/register', function() {
  include 'register/routes.php';
});

$this->group('/notify', function() {
  include 'notify/routes.php';
});