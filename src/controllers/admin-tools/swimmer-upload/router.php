<?php

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Admin') {
  $this->get('/', function() {
    require 'upload.php';
  });

  $this->post('/', function() {
    require 'upload-post.php';
  });
}

$this->get('/sheffield', function() {
  require 'sheffield-upload.php';
});

$this->post('/sheffield', function() {
  require 'sheffield-upload-post.php';
});