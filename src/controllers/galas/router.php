<?php
$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// Gala Home
	$this->get('/', function() {
		// Check docs for route - this is a GET
		global $link;
		include 'parentHome.php';
	});

	// Enter a gala
	$this->get('/entergala', function() {
		global $link;
		include 'galaentries.php';
	});

	$this->get('/ajax/entryForm', function() {
		global $link;
		include BASE_DIR . 'controllers/ajax/galaForm.php';
	});

	// Gala Entries
	$this->get('/entries', function() {
		global $link;
		include 'parententries.php';
	});

	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'singleentry.php';
	});
}
else if ($access == "Galas" || $access == "Committee" || $access == "Admin") {
	// Gala Home
	$this->get(['/', '/competitions'], function() {
		global $link;
		include 'listGalas.php';
	});

	// Add a gala
	$this->get('/addgala', function() {
		global $link;
		include 'addGala.php';
	});

	$this->post('/addgala', function() {
		global $link;
		include 'addGalaAction.php';
	});

	// View Competitions
	$this->get('/competitions/{id}:int', function($id) {
		global $link;
		include "competitionSingle.php";
	});

	$this->post('/competitions/{id}:int', function($id) {
		global $link;
		include "competitionSingleAction.php";
	});

	// Gala Entries
	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'singleentry.php';
	});

	// Gala Entries
	$this->get(['/entries', 'entries/filter'], function() {
		global $link;
		include 'allEntries.php';
	});

	$this->any('/ajax/entries', function() {
		global $link;
		try {
			require BASE_DIR . 'controllers/ajax/GalaEntries.php';
		} catch (Exception $e) {
			halt(404);
		}
	});

	$this->post('/ajax/entryProcessed', function() {
		global $link;
		include BASE_DIR . 'controllers/ajax/galaEntriesProcessed.php';
	});

	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'singleentry.php';
	});
}
