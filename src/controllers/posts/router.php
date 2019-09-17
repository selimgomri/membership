<?php

use Respect\Validation\Validator as v;

if ($_SESSION['AccessLevel'] != "Parent" && $_SESSION['AccessLevel'] != "Coach") {
	$this->get('/new', function() {
		global $link;
		include 'NewPost.php';
	});

	$this->post('/new', function() {
		global $link;
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
		global $link;
		$int = false;
		if (v::intVal()->validate($id)) {
			$int = true;
		}
		include 'EditPostServer.php';
	});
}

$this->get('/', function() {
	global $link;

	if ($_SESSION['AccessLevel'] == "Parent") {
		header("Location: " . autoUrl(""));
	} else {
		include 'PostList.php';
	}
});

$this->get(['/{id}:int', '/*/{id}:int'], function($id) {
	global $link;
	$int = true;
	include 'Post.php';
});

$this->get(['/*'], function() {
	global $link;
	$int = false;
	$id = ltrim($this[0], '/');
	include 'Post.php';
});

$this->get(['/', '/{page}:int'], function($page = null) {
	global $link;

	include 'PeopleList.php';
});

$this->get(['/{id}'], function($id) {
	global $link;
	$people = true;
	$int = false;
	include 'People.php';
});