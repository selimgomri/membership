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

	// Gala Entries
	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'singleentry.php';
	});
}
else if ($access == "Galas" || $access == "Committee" || $access == "Admin") {
	// Gala Home
	$this->get('/', function() {
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

	// Gala Entries
	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'singleentry.php';
	});
}
