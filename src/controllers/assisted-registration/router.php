<?php

if ($_SESSION['AccessLevel'] != 'Parent') {
  $this->get('/', function() {
    include 'welcome.php';
  });

  $this->post('/', function() {
    include 'welcomePost.php';
  });

  if (isset($_SESSION['AssRegUserEmail']) && !isset($_SESSION['AssRegUser']) && !isset($_SESSION['AssRegComplete'])) {
    $this->get('/start', function() {
      include 'begin.php';
    });

    $this->post('/start', function() {
      include 'beginPost.php';
    });

    $this->get('/*', function() {
      header("Location: " . autoUrl("assisted-registration/start"));
    });
  } else if (isset($_SESSION['AssRegUser']) && !isset($_SESSION['AssRegComplete'])) {
    $this->get('/select-swimmers', function() {
      include 'select.php';
    });

    $this->post('/select-swimmers', function() {
      include 'selectPost.php';
    });

    $this->get('/*', function() {
      header("Location: " . autoUrl("assisted-registration/select-swimmers"));
    });
  } else if (isset($_SESSION['AssRegUser']) && isset($_SESSION['AssRegComplete']) && $_SESSION['AssRegComplete']) {
    $this->get('/complete', function() {
      include 'complete.php';
    });

    $this->get('/*', function() {
      header("Location: " . autoUrl("assisted-registration/complete"));
    });
  }

  $this->get('/complete', function() {
    header("Location: " . autoUrl("assisted-registration"));
  });
}