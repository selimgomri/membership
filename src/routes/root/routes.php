<?php

$this->get('/', function() {
  include BASE_PATH . 'views/root/home.php';
});

$this->group('/login', function() {
  include BASE_PATH . 'routes/login/routes.php';
});

$this->group('/register', function() {
  include BASE_PATH . 'routes/register/routes.php';
});

$this->group('/account', function() {
  include BASE_PATH . 'routes/login/routes.php';
});

$this->group('/services', function() {
  include BASE_PATH . 'routes/services/routes.php';
});

$this->group('/shared-services', function() {
  include BASE_PATH . 'controllers/shared-services/routes.php';
});

$this->group(['/tenants', '/clubs'], function() {
  include 'tenants.php';
});

$this->get('/public/*', function() {
  $filename = $this[0];
  require BASE_PATH . 'controllers/PublicFileLoader.php';
});

$this->group('/db', function () {
  // Handle database migrations
  include BASE_PATH . 'controllers/migrations/router.php';
});

$this->any('/*', function() {
  include BASE_PATH . 'views/root/errors/404.php';
});