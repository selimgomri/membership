<?php

$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {
	// Renewal Home
	$this->get('/', function() {
		global $link;
		include 'parent/home.php';
	});

	$this->get(['/go', '/go/*'], function() {
		global $link;
		include 'parent/AutoRoute.php';
	});

	$this->post(['/go', '/go/*'], function() {
		global $link;
		include 'parent/AutoRoutePost.php';
	});
}

if ($access == "Admin") {
	$this->get('/', function() {
		global $link;
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
