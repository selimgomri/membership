<?php
$access = $_SESSION['AccessLevel'];

// Gala Entry Time Sheet
$this->get('/competitions/{id}:int/timesheet', function($id) {
  global $link;
  include "export/TimeSheet.php";
});

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
		include BASE_PATH . "controllers/ajax/galaForm.php";
	});

	$this->post('/entergala', function() {
		global $link;
		include 'galaentriesaction.php';
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

	$this->post('/entries/{id}', function($id) {
		global $link;
		include 'entriesSingleaction.php';
	});
}
else if ($access == "Galas" || $access == "Committee" || $access == "Admin" || $access == "Coach") {
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
	$this->get('/entries/{id}:int', function($id) {
		global $link;
		include 'singleentry.php';
	});

	$this->post('/entries/{id}:int', function($id) {
		global $link;
		include 'entriesSingleaction.php';
	});

	// Gala Entries
	$this->get('/entries', function() {
		global $link;
		include 'allEntries.php';
	});

	$this->get('/ajax/entries', function() {
		global $link;
		require BASE_PATH . 'controllers/ajax/GalaEntries.php';
	});

	$this->post('/ajax/entryProcessed', function() {
		global $link;
		include BASE_PATH . 'controllers/ajax/galaEntriesProcessed.php';
	});

	$this->get('/entries/{id}:int', function($id) {
		global $link;
		include 'singleentry.php';
	});

	$this->get('/entries/{id}:int/manualtime', function($id) {
		global $link;
		include 'AddManualTime.php';
	});

	$this->post('/entries/{id}:int/manualtime', function($id) {
		global $link;
		include 'AddManualTimePost.php';
	});
}
