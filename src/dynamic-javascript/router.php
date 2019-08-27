<?php

$this->before('/js', function () {
  header("content-type: application/x-javascript");
});

$this->get('/gala-checkout.js', function() {
  include 'gala-checkout.php.js';
});

$this->get('/gala-entry-form.js', function() {
  include 'gala-entry-form.php.js';
});

$this->get('/add-payment-card.js', function() {
  include 'add-payment-card.php.js';
});

$this->get('/payment-helpers.js', function() {
  include 'payment-helpers.php.js';
});

$this->get('/*', function() {
  header("content-type: text/html");
  halt(404);
});