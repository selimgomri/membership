<?php
$access = $_SESSION['AccessLevel'];

include "functions.php";

if ($access == "Committee" || $access == "Admin" || $access == "Galas") {
	// User Directory
	$this->get(['/', '/filter/{id}:int'], function($id) {
		// Check docs for route - this is a GET
		global $link;
		include 'userDirectory.php';
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		include 'user.php';
	});
}
