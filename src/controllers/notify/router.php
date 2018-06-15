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

	$this->get('/email', function() {
		global $link;
		include 'EmailList.php';
	});

	$this->get('/email/{id}:int', function($id) {
		global $link;
		include 'EmailID.php';
	});

	$this->get('/emailtest', function() {
		global $link;
		notifySend("chris.heppell@chesterlestreetasc.co.uk", "test", "<p>Hello</p>");
	});
}
