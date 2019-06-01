<?php

$this->get('/', function() {
	global $link;
	require('parents/index.php');
});

$this->get('/edit/{id}:int', function($id) {
	global $link;
	require('parents/edit.php');
});

$this->post('/edit/{id}:int', function($id) {
	global $link;
	require('parents/editUpdate.php');
});

$this->get('/new', function() {
	global $link;
	require('parents/new.php');
});

$this->post('/new', function() {
	global $link;
	require('parents/newAction.php');
});

$this->get('/{id}:int/delete', function($id) {
	global $link;
	require('parents/delete.php');
});
