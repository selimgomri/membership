<?php

$this->get('/register/now/{id}:int', function($id) {
	$db = app()->db;
	include 'now/ConfirmLogout.php';
});

$this->get('/register/now/{id}:int/go', function($id) {
	$db = app()->db;
	include 'now/Logout.php';
});

$this->get('/register/later/{id}:int', function($id) {
	$db = app()->db;
	include 'later/SignupSheet.php';
});
