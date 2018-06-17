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

	// Edit a Swimmer
	$this->get('/edit/{id}:int', function($id) {
    global $link;
	  require('parentSingleSwimmer.php');
	});

	// Edit a Swimmer
	$this->post('/edit/{id}:int', function($id) {
    global $link;
	  require('parentSingleSwimmer.php');
	});
}
else if ($access == "Galas" || $access == "Coach" || $access == "Admin") {
	// Directory
	$this->get('/', function() {
    global $link;
	  require('swimmerDirectory.php');
	});

	$this->post('/ajax/swimmerDirectory', function() {
    global $link;
	  include BASE_PATH . "controllers/ajax/membersList.php";
	});

	// Individual Swimmers
	$this->get('/{id}:int', function($id) {
    global $link;
	  require('singleSwimmerView.php');
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
	  require('addMember.php');
	});

	$this->post('/addmember', function() {
    global $link;
	  require('addMember.php');
	});
}
