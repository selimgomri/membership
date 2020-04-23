<?php
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  // Attendance Home
  $this->get('/', function() {
    
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
  $this->get('/register', function($squad = null, $session = null) {
    include 'register.php';
	});

  $this->get('/ajax/register/sessions', function() {
    
    include BASE_PATH . 'controllers/ajax/registerSessions.php';
	});

  $this->post('/register', function() {
    include 'POST/register.php';
	});

  // Sessions
  $this->group('/sessions', function() {
    

    $this->get('/', function() {
      include "sessions.php";
  	});

    $this->get('/{id}:int', function($id) {
      
      include "sessionViews/editEndDate.php";
  	});

    $this->post('/ajax/handler', function() {
      
      include BASE_PATH . "controllers/ajax/sessions.php";
  	});

    $this->post('/ajax/endDateHandler', function() {
      
      include BASE_PATH . "controllers/ajax/sessionsEndDate.php";
  	});
  });

  // History
  $this->group('/history', function() {
    

    $this->get('/', function() {
      
      include "historyViews/history.php";
  	});

    $this->get('/squads', function() {
      include "historyViews/squads.php";
  	});

    $this->get('/squads/{id}:int', function($id) {
      
      include "historyViews/squadHistory.php";
  	});

    $this->get('/swimmers', function() {
      
      include "historyViews/swimmers.php";
  	});

    $this->post('/ajax/swimmers', function() {
      
      include BASE_PATH . "controllers/ajax/swimmerHistory.php";
  	});

    $this->get('/swimmers/{id}:int', function($id) {
      
      include "historyViews/swimmerHistory.php";
  	});
  });
}

?>
