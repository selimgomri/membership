<?php

$access = $_SESSION['AccessLevel'];

if ($access == "Parent") {

	$this->get('/', function() {
		global $link;
		include 'user.php';
	});

	$this->get(['setup', '/setup/{stage}:int'], function($stage = 0) {
		global $link;
		require 'GoCardlessSetup.php';
		if ($stage == 0) {
			require('setup/start.php');
		}
		else if ($stage == 1) {
			require('setup/initiate.php');
		}
		else if ($stage == 2) {
			require('setup/redirect.php');
		}
	});

	$this->get('/mandates', function() {
		global $link;
		include 'mybanks.php';
	});

	$this->get('/testpay', function() {
	  global $link;
		include 'testpay.php';
	});
}

if ($access == "Parent" || $access == "Admin") {
	$this->get('/mandates/{mandate}', function($mandate) {
	  global $link;
		include 'mandatePDFs.php';
	});
}

if ($access == "Admin") {
	$this->get('/', function() {
		global $link;
		include 'admin.php';
	});

	$this->get('/fees', function() {
		global $link;
		include 'adminViewMonthlyFees.php';
	});

	$this->get('/newcharge', function() {
		global $link;
		include 'admin/ManualCharge.php';
	});

	$this->post('/newcharge', function() {
		global $link;
		include 'admin/ManualChargeDo.php';
	});

	$this->get('/newrefund', function() {
		global $link;
		include 'admin/ManualRefund.php';
	});

	$this->post('/newrefund', function() {
		global $link;
		include 'admin/ManualRefundDo.php';
	});
}
