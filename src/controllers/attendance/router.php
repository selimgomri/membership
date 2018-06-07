<?php
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {
  // Attendance Home
  $this->get('/', function() {
    global $link;
    include 'indexView.php';
	});

  // Registers
  $this->get('/register', function() {
    global $link;
    include 'register.php';
	});

  $this->get('/ajax/register/sessions', function() {
    global $link;
    include BASE_PATH . 'controllers/ajax/registerSessions.php';
	});

  $this->get('/register', function() {
    global $link;
    include 'POST/register.php';
	});

  // Sessions
  $this->group('/sessions', function() {
    global $link;

    $this->get('/', function() {
      global $link;
      include "historyViews/history.php";
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
      global $link;
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

    $this->get('/swimmers/{id}:int', function($id) {
      global $link;
      include "historyViews/swimmerHistory.php";
  	});
  });
}

?>
