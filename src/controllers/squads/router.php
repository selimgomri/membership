<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin" || $access == "Coach") {

	$this->get('/', function() {
		global $link;
		require 'squadList.php';
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		require 'squadIndividual.php';
	});

	$this->post('/{id}:int', function($id) {
		global $link;
		require 'squadIndividual.php';
	});

	$this->group('/moves', function() {
		global $link;

		$this->get('/', function() {
			global $link;
			require 'moves.php';
		});

		$this->get('/new/{id}', function($id) {
			global $link;
			require 'newMove.php';
		});

		$this->post('/new/{id}', function($id) {
			global $link;
			require 'newMoveAction.php';
		});

		$this->get('/edit/{id}:int', function($id) {
			global $link;
			require 'editMove.php';
		});

		$this->post('/edit/{id}:int', function($id) {
			global $link;
			require 'editMoveAction.php';
		});

		$this->get('/cancel/{id}:int', function($id) {
			global $link;
			require 'cancelMoveAction.php';
		});
	});

}

if ($access == "Admin") {
	// Add a squad
	$this->get('/addsquad', function() {
		global $link;
		require 'SquadAdd.php';
	});

	$this->post('/addsquad', function() {
		global $link;
		require 'SquadAddAction.php';
	});
}
