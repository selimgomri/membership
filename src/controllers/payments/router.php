<?php

$this->get('/', function() {
	global $link;
	include 'user.php';
});

$this->get('/setup/{stage}:int', function($stage) {
	global $link;
	require 'GoCardlessSetup.php';
	if ($stage == 0) {
		require('setup/initiate.php');
	}
	else if ($stage == 1) {
		require('setup/redirect.php');
	}
});

$this->get('/banks', function() {
	global $link;
	include 'mybanks.php';
});

$this->get('/testpay', function() {
  global $link;
	include 'testpay.php';
});

$this->get('/mandates/{mandate}', function($mandate) {
  global $link;
	include 'mandatePDFs.php';
});
