<?php
$access = $_SESSION['AccessLevel'];

include "functions.php";

if (isset($_SESSION['UserSimulation'])) {
	$this->get('/simulate/exit', function() {
		global $link;
		include 'ExitSimulation.php';
	});
}

if ($access == "Committee" || $access == "Admin" || $access == "Galas") {
	// User Directory
	$this->get(['/', '/filter'], function($id = null) {
		global $link;
		include 'userDirectory.php';
	});

	$this->any('/ajax/userList', function() {
		global $link;
		include BASE_PATH . "controllers/ajax/userList.php";
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		include 'user.php';
	});

  $this->get('/{id}:int/new-qualification', function($person) {
		include BASE_PATH . 'controllers/qualifications/admin/NewQualification.php';
	});

  $this->post('/{id}:int/new-qualification', function($person) {
		include BASE_PATH . 'controllers/qualifications/admin/NewQualification.php';
	});

	if (!isset($_SESSION['UserSimulation'])) {
		$this->get('/simulate/{id}:int', function($id) {
			global $link;
			include 'EnterSimulation.php';
		});
	}

	$this->post('/ajax/userSettings/{id}:int', function($id) {
		global $link;
		include 'userSettingsAjax.php';
	});

	$this->post('/ajax/username', function() {
		global $link;
		include 'usernameAjax.php';
	});
}
