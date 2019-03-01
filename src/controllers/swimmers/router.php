<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// My Swimmers
	$this->get('/', function() {
    global $link, $userID;
	  require('parentSwimmers.php');
	});

	// View a Swimmer
	$this->get('/{id}:int', function($id) {
    global $link;
	  require('parentSingleSwimmerView.php');
	});

  // Swimmer Membership Card
	$this->get('/{id}:int/membershipcard', function($id) {
    global $link;
	  require('Card.php');
	});

  // Swimmer is leaving
	$this->get('/{id}:int/leaveclub', function($id) {
	  require('Leave.php');
	});

  // Swimmer is leaving
	$this->get('/{id}:int/leaveclub/{key}', function($id, $key) {
	  require('LeaveDo.php');
	});

	// Edit a Swimmer
	$this->get('/edit/{id}:int', function($id) {
    global $link;
	  require('parentSingleSwimmer.php');
	});

	// Edit a Swimmer
	$this->post('/edit/{id}:int', function($id) {
    global $link;
	  require 'parentSingleSwimmerPost.php';
	});
}
else if ($access == "Galas" || $access == "Coach" || $access == "Admin") {
	// Directory
	$this->get('/', function() {
    global $link;
	  require('swimmerDirectory.php');
	});

	if ($access == "Admin") {
		$this->get('/orphaned', function() {
	    global $link;
		  require('swimmerOrphaned.php');
		});
	}

	$this->post('/ajax/swimmerDirectory', function() {
    global $link;
	  include BASE_PATH . "controllers/ajax/membersList.php";
	});

	// Individual Swimmers
	$this->get('/{id}:int', function($id) {
    global $link;
	  require('singleSwimmerView.php');
	});

  // Swimmer Membership Card
	$this->get('/{id}:int/membershipcard', function($id) {
    global $link;
	  require('Card.php');
	});

	// Access Keys
	$this->get('/accesskeys', function() {
    global $link;
	  require('accesskeys.php');
	});

	// Access Keys
	$this->get('/accesskeys-csv', function() {
    global $link;
	  require('accesskeysCSV.php');
	});
}

if ($access == "Admin") {
	// Edit Individual Swimmers
	$this->get('/edit/{id}:int', function($id) {
    global $link;
	  require('singleSwimmerEdit.php');
	});

	$this->post('/edit/{id}:int', function($id) {
    global $link;
	  require('singleSwimmerEdit.php');
	});
}

if ($access != "Parent") {
	$this->get('/addmember', function() {
    global $link;
	  include 'AddMember/SelectType.php';
	});

  /*
	$this->get('/new/family', function() {
    global $link;
	  include 'AddMember/ActivateFamilyMode.php';
	});

	if (isset($_SESSION['Swimmers-FamilyMode'])) {
		$this->get('/family/exit', function() {
	    global $link;
		  include 'AddMember/ExitFamilyMode.php';
		});
	}
  */

	$this->get('/new', function() {
    global $link;
	  require('AddMember/addMember.php');
	});

	$this->post('/new', function() {
    global $link;
	  require('AddMember/addMemberPost.php');
	});

	$this->get('/parenthelp/{id}:int', function($id) {
		global $link;
		include 'parentSetupHelp.php';
	});

  $this->post('/parenthelp/{id}:int', function($id) {
		global $link;
		include 'parentSetupHelpPost.php';
	});
}

// View Medical Notes
$this->get('/{id}:int/medical', function($id) {
	global $link;
	include 'medicalDetails.php';
});

// View Medical Notes
$this->post('/{id}:int/medical', function($id) {
	global $link;
	include 'medicalDetailsPost.php';
});
