<?php
$access = $_SESSION['AccessLevel'];

include "functions.php";

if ($access == "Committee" || $access == "Admin" || $access == "Galas") {
	// User Directory
	$this->get(['/', '/filter'], function($id = null) {
		// Check docs for route - this is a GET
		global $link;
		include 'userDirectory.php';
	});

	$this->get('/ajax/userList', function($id = null) {
		// Check docs for route - this is a GET
		global $link;
		include BASE_PATH . "controllers/ajax/userList.php";
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		include 'user.php';
	});
}
