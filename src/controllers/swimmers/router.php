<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// My Swimmers
	$this->get('/', function() {
    global $link;
	  require('parentSwimmers.php');
	});

	// View a Swimmer
	$this->get('/{id}', function($id) {
    global $link;
		echo $id;
	  require('parentSingleSwimmerView.php');
	});

	// Edit a Swimmer
	$this->get('/edit/{id}', function($id) {
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
		echo "Hello";
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
