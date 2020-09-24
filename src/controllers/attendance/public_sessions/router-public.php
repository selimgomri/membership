<?php

$this->get('/', function() {
  include 'sessions.php';
});

$this->get('/booking/book', function() {
  if (isset(app()->user)) {
    include BASE_PATH . 'controllers/attendance/booking/book-session/book-session.php';
  } else {
    include BASE_PATH . 'controllers/attendance/booking/book-session/book-session-public.php';
  }
});