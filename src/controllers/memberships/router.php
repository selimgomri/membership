<?php

$this->get('/', function() {
  include 'home.php';
});

$this->group('/periods', function() {
  $this->get('/', function() {
    include 'periods/home.php';
  });
});

$this->group('/renewal', function() {
  $this->get('/', function() {
    include 'renewal/home.php';
  });

  $this->group('/new', function() {
    $this->get('/', function() {
      include 'renewal/new.php';
    });

    $this->post('/', function() {
      include 'renewal/new-post.php';
    });
  });

  $this->group('/{id}:uuid', function($id) {
    $this->get('/', function($id) {
      include 'renewal/period.php';
    });

    $this->get('/edit', function($id) {
      include 'renewal/edit.php';
    });

    $this->post('/edit', function($id) {
      include 'renewal/edit-post.php';
    });
  });
});

$this->group('/batches', function() {
  $this->get('/{id}:uuid', function($id) {
    include 'batches/batch.php';
  });

  $this->post('/{id}:uuid', function($id) {
    include 'batches/pay-post.php';
  });
});

$this->group('/years', function() {
  $this->get('/', function() {
    include 'years/home.php';
  });

  $this->group('/new', function() {
    $this->get('/', function() {
      include 'years/new.php';
    });

    $this->post('/', function() {
      include 'years/new-post.php';
    });
  });

  $this->group('/{id}:uuid', function($id) {
    $this->get('/', function($id) {
      include 'years/year.php';
    });

    $this->get('/edit', function($id) {
      include 'years/edit.php';
    });

    $this->post('/edit', function($id) {
      include 'years/edit-post.php';
    });

    $this->get('/new-batch', function($id) {
      include 'years/batches/new.php';
    });

    $this->post('/new-batch', function($id) {
      include 'years/batches/new-post.php';
    });
  });
});