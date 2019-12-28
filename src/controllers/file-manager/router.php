<?php

if ($_SESSION['AccessLevel'] != "Parent") {
  $this->get('/', function() {
    include 'home.php';
  });

  $this->get('/upload', function() {
    include 'upload.php';
  });

  $this->post('/upload', function() {
    include 'upload-post.php';
  });
}