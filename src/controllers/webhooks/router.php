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
	require 'notifyhandler.php';
});