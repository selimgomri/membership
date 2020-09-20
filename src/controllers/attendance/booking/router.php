<?php

$this->get('/', function () {
  include 'home.php';
});

$this->get('/book', function () {
  include 'new-or-existing-handler.php';
});

$this->post('/book', function () {
  include 'book-session/book-session-post.php';
});

$this->post('/cancel', function () {
  include 'book-session/cancel-booking-post.php';
});

$this->post('/require-booking', function () {
  include 'require-booking/require-booking-post.php';
});