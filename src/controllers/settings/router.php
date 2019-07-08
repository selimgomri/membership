<?php

$this->get('/', function() {
  include 'home.php';
});

$this->group('/variables', function() {
    $this->get('/', function() {
      include 'variables.php';
    });

    $this->post('/', function() {
      include 'variables-post.php';
    });
});

$this->group('/codes-of-conduct', function() {
  $this->get('/', function() {
    header("Location: " . autoUrl("settings/codes-of-conduct/parent"));
  });

  $this->group('/parent', function() {
    $this->get('/', function() {
      include 'ParentCodeOfConduct.php';
    });

    $this->post('/', function() {
      include 'ParentCodeOfConductPost.php';
    });
  });
});

$this->group('/fees', function() {
  $this->get('/', function() {
    include 'fees/home.php';
  });

  $this->group('/membership-fees', function() {
    $this->get('/', function() {
      include 'fees/membership-fees.php';
    });

    $this->post('/', function() {
      include 'fees/membership-fees-post.php';
    });
  });

  $this->group('/swim-england-fees', function() {
    $this->get('/', function() {
      include 'fees/asa-fees.php';
    });

    $this->post('/', function() {
      include 'fees/asa-fees-post.php';
    });
  });
});

$this->get('/welcome-pack', function() {
  include 'WelcomePackSettings.php';
});

$this->get('/welcome-pack/preview', function() {
  include BASE_PATH . 'controllers/registration/welcome-pack/PDF.php';
});

$this->post('/welcome-pack', function() {
  include 'WelcomePackSettings.php';
});