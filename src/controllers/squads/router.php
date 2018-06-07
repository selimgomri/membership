<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {

	$this->get('/', function() {
		global $link;
		require('squadList.php');
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		require('squadIndividual.php');
	});

}

if ($access == "Admin") {
	// Add a squad
	$this->get('/addsquad', function() {
		global $link;
		require('SquadAdd.php');
	});

	$this->post('/addsquad', function() {
		global $link;
		require('SquadAddAction.php');
	});
}
