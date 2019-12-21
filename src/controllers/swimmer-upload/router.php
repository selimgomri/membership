<?php

if ($_SESSION['AccessLevel'] == 'Admin') {
  $this->get('/', function() {
    require 'upload.php';
  });

  $this->post('/', function() {
    require 'upload-post.php';
  });
}