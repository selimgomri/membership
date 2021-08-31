<?php

$this->get('/', function () {
  include 'home.php';
});

$this->group('/new', function () {
  $this->get('/', function () {
    include 'new/start.php';
  });

  $this->post('/', function () {
    include 'new/start-post.php';
  });
});

$this->group('/sessions', function () {
  $this->group('/a', function () {
    $this->get('/{id}:uuid', function ($id) {
      include 'admin/session.php';
    });

    $this->post('/{id}:uuid', function () {
      include 'admin/session-post.php';
    });
  });

  $this->get('/{id}:uuid', function ($id) {
    include 'user/session.php';
  });
});
