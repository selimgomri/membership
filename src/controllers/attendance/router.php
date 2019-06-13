<?php
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  // Attendance Home
  $this->get('/', function() {
    global $link;
    include 'indexView.php';
	});

  $this->group('/venues', function() {
    $this->get('/', function() {
      include 'Venues.php';
  	});

    $this->get('/new', function() {
      include 'NewVenue.php';
  	});

    $this->post('/new', function() {
      include 'NewVenuePost.php';
  	});

    $this->get('/{id}:int', function($id) {
      include 'EditVenue.php';
  	});

    $this->post('/{id}:int', function($id) {
      include 'EditVenuePost.php';
  	});
	});

  // Registers
  $this->get(['/register', '/register/{squad}:int/{session}:int'], function($squad = null, $session = null) {
    global $link;
    include 'register.php';
	});

  $this->get('/ajax/register/sessions', function() {
    global $link;
    include BASE_PATH . 'controllers/ajax/registerSessions.php';
	});

  $this->post(['/register', '/register/{squad}:int/{session}:int'], function() {
    global $link;
    include 'POST/register.php';
	});

  // Sessions
  $this->group('/sessions', function() {
    global $link;

    $this->get('/', function() {
      include "sessions.php";
  	});

    $this->get('/{id}:int', function($id) {
      global $link;
      include "sessionViews/editEndDate.php";
  	});

    $this->post('/ajax/handler', function() {
      global $link;
      include BASE_PATH . "controllers/ajax/sessions.php";
  	});

    $this->post('/ajax/endDateHandler', function() {
      global $link;
      include BASE_PATH . "controllers/ajax/sessionsEndDate.php";
  	});
  });

  // History
  $this->group('/history', function() {
    global $link;

    $this->get('/', function() {
      global $link;
      include "historyViews/history.php";
  	});

    $this->get('/squads', function() {
      include "historyViews/squads.php";
  	});

    $this->get('/squads/{id}:int', function($id) {
      global $link;
      include "historyViews/squadHistory.php";
  	});

    $this->get('/swimmers', function() {
      global $link;
      include "historyViews/swimmers.php";
  	});

    $this->post('/ajax/swimmers', function() {
      global $link;
      include BASE_PATH . "controllers/ajax/swimmerHistory.php";
  	});

    $this->get('/swimmers/{id}:int', function($id) {
      global $link;
      include "historyViews/swimmerHistory.php";
  	});
  });
}

?>
