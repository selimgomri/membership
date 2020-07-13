<?php

$this->get('/', function () {
  include 'categories.php';
});

$this->group('/new', function () {
  $this->get('/', function () {
    include 'new.php';
  });

  $this->post('/', function () {
    include 'new-post.php';
  });
});

$this->get('/{id}:uuid', function ($id) {
  include 'edit.php';
});

$this->post('/{id}:uuid', function ($id) {
  include 'edit-post.php';
});
