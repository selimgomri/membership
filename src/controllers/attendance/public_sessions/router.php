<?php

$this->get('/', function() {
  include 'sessions.php';
});

$this->group('/booking', function () {
  include BASE_PATH . 'controllers/attendance/booking/router.php';
});
