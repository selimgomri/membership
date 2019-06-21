<?php

$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// Renewal Home
	$this->get('/', function() {
		global $link;
		include 'parent/home.php';
	});

	$this->get(['/go'], function() {
		$redirect = true;
		global $link;
		include 'parent/AutoRoute.php';
	});

	$this->get(['/go/*'], function() {
		global $link;
		include 'parent/AutoRoute.php';
	});

	$this->post(['/go', '/go/*'], function() {
		global $link;
		include 'parent/AutoRoutePost.php';
	});

	$this->group('/payments', function() {
		$this->get(['/setup', '/setup/{stage}:int'], function($stage = 0) {
			global $link;
			$renewal_trap = true;
			require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';
			if ($stage == 0) {
				require(BASE_PATH . 'controllers/payments/setup/start.php');
			}
			else if ($stage == 1) {
				require(BASE_PATH . 'controllers/payments/setup/date.php');
			}
			else if ($stage == 2) {
				require(BASE_PATH . 'controllers/payments/setup/initiate.php');
			}
			else if ($stage == 3) {
				require(BASE_PATH . 'controllers/payments/setup/redirect.php');
			}
		});

		$this->post('/setup/1', function() {
			global $link;
			include BASE_PATH . 'controllers/payments/setup/datepost.php';
		});
	});

	$this->group('/emergencycontacts', function() {
		$this->get('/', function() {
			global $link;
			$renewal_trap = true;
			include BASE_PATH . 'controllers/emergencycontacts/parents/index.php';
		});

		$this->get('/edit/{id}:int', function($id) {
			global $link;
			$renewal_trap = true;
			require('controllers/emergencycontacts/parents/edit.php');
		});

		$this->post('/edit/{id}:int', function($id) {
			global $link;
			$renewal_trap = true;
			require('controllers/emergencycontacts/parents/editUpdate.php');
		});

		$this->get('/new', function() {
			global $link;
			$renewal_trap = true;
			require('controllers/emergencycontacts/parents/new.php');
		});

		$this->post('/new', function() {
			global $link;
			$renewal_trap = true;
			require('controllers/emergencycontacts/parents/newAction.php');
		});

		$this->get('/{id}:int/delete', function($id) {
			global $link;
			$renewal_trap = true;
			require('controllers/emergencycontacts/parents/delete.php');
		});

	});
}

if ($access == "Admin") {
	$this->get('/', function() {
		include 'admin/home.php';
	});

	$this->get('/new', function() {
		global $link;
		include 'admin/new.php';
	});

	$this->post('/new', function() {
		global $link;
		include 'admin/newPost.php';
	});

	$this->get('/{id}:int/edit', function($id) {
		global $link;
		include 'admin/edit.php';
	});

	$this->post('/{id}:int/edit', function($id) {
		global $link;
		include 'admin/editPost.php';
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		include 'admin/list.php';
	});
}