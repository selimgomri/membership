<?php

$this->any('/sumpayments', function() {
	
	require 'sumpayments.php';
});

$this->any('/chargeusers', function() {
	
	require 'chargeusers.php';
});

$this->any('/retrypayments', function() {
	require 'retry-payments.php';
});

$this->any('/notifysend', function() {
	
  $db = app()->db;
	//echo "Service Suspended";
	require 'SingleEmailHandler.php';
});

$this->any('/newnotifysend', function() {
	require 'notifyhandler.php';
});

$this->any('/updatesquadmembers', function() {
	$db = app()->db;;
	require 'squadmemberupdate.php';
});

$this->any('/updateregisterweeks', function() {
	$db = app()->db;;
	require 'newWeek.php';
});

$this->any('/timeupdate', function() {
	$db = app()->db;;
	require 'getTimesNew.php';
});

/*$this->any('/timeupdatenew', function() {
	$db = app()->db;;
	require 'getTimesNew.php';
});*/
