<?php

$this->get('/', function () {
  if (!isset($_SESSION['OnboardingSessionId'])) {
    include 'init.php';
  } else {
    include 'start.php';
  }
});

$this->get('/start-task', function () {
  include 'task-handler.php';
});

$this->post('/start-task', function () {
  include 'post-task-handler.php';
});

$this->group('/emergency-contacts', function () {
  $this->get('/list', function() {
    include 'tasks/emergency_contacts/list.php';
  });

  $this->post('/new', function() {
    include 'tasks/emergency_contacts/new.php';
  });

  $this->post('/edit', function() {
    include 'tasks/emergency_contacts/edit.php';
  });

  $this->post('/delete', function() {
    include 'tasks/emergency_contacts/delete.php';
  });
});

$this->get('/error', function () {
});

$this->get('/wrong-account', function () {
  include 'logged-in.php';
});
