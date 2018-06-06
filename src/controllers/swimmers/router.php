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
		echo $id;
	  require('parentSingleSwimmer.php');
	});
}
else if ($access == "Galas" || $access == "Coach" || $access == "Admin") {
	// Directory
	$this->get(['/', '/filter'], function() {
    global $link;
	  require('swimmerDirectory.php');
	});

	$this->any('ajax/swimmerDirectory', function() {
    global $link;
	  include BASE_PATH . "controllers/ajax/membersList.php";
	});

	// Access Keys
	$this->get('/accesskeys', function() {
    global $link;
	  require('accesskeys.php');
		echo "accesskeys";
	});

	// Access Keys
	$this->get('/accesskeys-csv', function() {
    global $link;
	  require('accesskeysCSV.php');
	});
}
