<?php

$this->any('/sumpayments', function() {
	global $link;
	require 'sumpayments.php';
});

$this->any('/chargeusers', function() {
	global $link;
	require 'chargeusers.php';
});

$this->any('/notifysend', function() {
	global $link;
	//echo "Service Suspended";
	require 'notifyhandler.php';
});

$this->any('/updatesquadmembers', function() {
	global $db, $link;
	require 'squadmemberupdate.php';
});

$this->any('/updateregisterweeks', function() {
	global $db, $link;
	require 'newWeek.php';
});

$this->any('/timeupdate', function() {
	global $db, $link;
	require 'getTimesNew.php';
});

/*$this->any('/timeupdatenew', function() {
	global $db, $link;
	require 'getTimesNew.php';
});*/
