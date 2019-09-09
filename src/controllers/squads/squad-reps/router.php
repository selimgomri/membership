<?php

$this->get('/', function() {
  include 'page-selector.php';
});

$this->get('/list', function() {
  include 'public-rep-list.php';
});