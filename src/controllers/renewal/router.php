<?php

$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// Renewal Home
	$this->get('/', function() {
		global $link;
		include BASE_PATH . 'controllers/renewal/parent/home.php';
	});

	$this->get(['/go', '/go/*'], function() {
		global $link;
		include BASE_PATH . 'controllers/renewal/parent/AutoRoute.php';
	});

	$this->post(['/go', '/go/*'], function() {
		global $link;
		include BASE_PATH . 'controllers/renewal/parent/AutoRoutePost.php';
	});
}

if ($access == "Admin") {
	$this->get('/', function() {
		global $link;
		include BASE_PATH . 'controllers/renewal/admin/home.php';
	});

	$this->get('/{id}:int', function($id) {
		global $link;
		include BASE_PATH . 'controllers/renewal/admin/list.php';
	});
}
