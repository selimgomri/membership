<?php

use Respect\Validation\Validator as v;

if ($_SESSION['AccessLevel'] != "Parent" && $_SESSION['AccessLevel'] != "Coach") {
	$this->get('/new', function() {
		
		include 'NewPost.php';
	});

	$this->post('/new', function() {
		
		include 'NewPostServer.php';
	});

	$this->get(['/{id}:int/edit', '/{string}/edit'], function($id) {
		$int = false;
		if (v::intVal()->validate($id)) {
			$int = true;
		}
		include 'EditPost.php';
	});

	$this->post(['/{id}:int/edit', '/{string}/edit'], function($id) {
		
		$int = false;
		if (v::intVal()->validate($id)) {
			$int = true;
		}
		include 'EditPostServer.php';
	});
}

$this->get('/', function() {
	if ($_SESSION['AccessLevel'] == "Parent") {
		header("Location: " . autoUrl(""));
	} else {
		include 'PostList.php';
	}
});

$this->get('/{id}:int', function($id) {
	$int = true;
	include 'Post.php';
});


$this->get('/{id}:int/print.pdf', function($club, $void, $id) {
	$int = true;
	include 'PrintPost.php';
});

$this->get(['/*'], function() {
	$int = false;
	$id = ltrim($this[0], '/');
	include 'Post.php';
});
