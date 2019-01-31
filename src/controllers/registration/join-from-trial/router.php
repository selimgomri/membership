<?php

/**
 * Begin user data collection
 */

if ($_SESSION['AC-Registration']['Stage'] == 'UserDetails') {
  $this->get('/user-details', function() {
    include 'CollectUserDetails.php';
  });

  $this->post('/user-details', function() {
    include 'CollectUserDetailsPost.php';
  });
}

/**
 * Present users with splash screen
 */

$this->get('/{hash}', function($hash) {
  include 'BeginRegistration.php';
});

$this->post('/{hash}', function($hash) {
  include 'BeginRegistrationPost.php';
});
