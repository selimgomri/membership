<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

$this->get('/', function() {
  require 'squadList.php';
});

$this->get('/{id}:int', function($id) {
  require 'squad.php';
});

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {

	$this->get('/', function() {
		global $link;
		require 'squadList.php';
	});

	$this->get('/{id}:int/edit', function($id) {
		global $link;
		require 'EditSquad.php';
	});

	$this->post('/{id}:int/edit', function($id) {
		global $link;
		require 'EditSquadPost.php';
	});

	$this->get('/moves', function() {
		require 'moves.php';
	});

}

if ($access == "Admin") {
	// Add a squad
	$this->get('/new', function() {
		global $link;
		require 'AddSquad.php';
	});

	$this->post('/new', function() {
		global $link;
		require 'AddSquadPost.php';
	});
}
