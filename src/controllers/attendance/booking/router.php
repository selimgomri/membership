<?php

$this->get('/', function () {
  include 'home.php';
});

$this->get('/book', function () {
  include 'new-or-existing-handler.php';
});

$this->post('/book', function () {
  // include 'book-session/require-booking-post.php';
});

$this->post('/require-booking', function () {
  include 'require-booking/require-booking-post.php';
});