<?php

$access = $_SESSION['AccessLevel'];

if ($access == "Admin") {
	$this->get('/', function() {
		global $link;
		include 'Home.php';
	});

	$this->get('/newemail', function() {
		global $link;
		include 'Email.php';
	});

	$this->post('/newemail', function() {
		global $link;
		include 'EmailQueuer.php';
	});

	$this->get('/email/{id}:int', function($id) {
		global $link;
		include 'EmailID.php';
	});
}
