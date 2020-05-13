<?php

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Parent") {
	// Renewal Home
	$this->get('/', function() {
		
		include 'parent/home.php';
	});

	$this->get(['/go'], function() {
		$redirect = true;
		
		include 'parent/AutoRoute.php';
	});

	$this->get(['/go/*'], function() {
		
		include 'parent/AutoRoute.php';
	});

	$this->post(['/go', '/go/*'], function() {
		
		include 'parent/AutoRoutePost.php';
	});

	$this->group('/payments', function() {
		$this->get(['/setup', '/setup/{stage}:int'], function($stage = 0) {
			
			$renewal_trap = true;
			require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';
			if ($stage == 0) {
				require(BASE_PATH . 'controllers/payments/setup/start.php');
			} else if ($stage == 1) {
				require(BASE_PATH . 'controllers/payments/setup/date.php');
			} else if ($stage == 2) {
				require(BASE_PATH . 'controllers/payments/setup/initiate.php');
			} else if ($stage == 3) {
				require(BASE_PATH . 'controllers/payments/setup/redirect.php');
			} else if ($stage == 4) {
				require(BASE_PATH . 'controllers/payments/setup/status.php');
			}
		});

		$this->post('/setup/1', function() {
			
			include BASE_PATH . 'controllers/payments/setup/datepost.php';
		});
	});

	$this->group('/emergencycontacts', function() {
		$this->get('/', function() {
			
			$renewal_trap = true;
			include BASE_PATH . 'controllers/emergencycontacts/parents/index.php';
		});

		$this->get('/edit/{id}:int', function($id) {
			
			$renewal_trap = true;
			require BASE_PATH . 'controllers/emergencycontacts/parents/edit.php';
		});

		$this->post('/edit/{id}:int', function($id) {
			
			$renewal_trap = true;
			require BASE_PATH . 'controllers/emergencycontacts/parents/editUpdate.php';
		});

		$this->get('/new', function() {
			$renewal_trap = true;
			require BASE_PATH . 'controllers/emergencycontacts/parents/new.php';
		});

		$this->post('/new', function() {
			$renewal_trap = true;
			require BASE_PATH . 'controllers/emergencycontacts/parents/newAction.php';
		});

		$this->get('/{id}:int/delete', function($id) {
			
			$renewal_trap = true;
			require BASE_PATH . 'controllers/emergencycontacts/parents/delete.php';
		});

	});
}

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") {
	$this->get('/', function() {
		include 'admin/home.php';
	});

	$this->get('/new', function() {
		
		include 'admin/new.php';
	});

	$this->post('/new', function() {
		
		include 'admin/newPost.php';
	});

	$this->get('/{id}:int/edit', function($id) {
		
		include 'admin/edit.php';
	});

	$this->post('/{id}:int/edit', function($id) {
		
		include 'admin/editPost.php';
	});

	$this->get('/{id}:int', function($id) {
		
		include 'admin/list.php';
	});
}