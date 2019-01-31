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
			require('setup/date.php');
		}
		else if ($stage == 2) {
			require('setup/initiate.php');
		}
		else if ($stage == 3) {
			require('setup/redirect.php');
		}
	});

	$this->post('/setup/1', function() {
		global $link;
		include 'setup/datepost.php';
	});

	$this->get('/mandates', function() {
		global $link;
		include 'mybanks.php';
	});

	$this->get('/mandates/makedefault/{id}:int', function($id) {
		global $link;
		include 'setup/makedefault.php';
	});

	$this->get(['/currentfees', '/fees'], function() {
		global $link;
		include 'parent/currentfees.php';
	});

	$this->get('/transactions', function() {
		global $link;
		include 'parent/transactions.php';
	});

  $this->get('/statement/latest', function() {
		include 'parent/LatestStatement.php';
	});

	$this->get('/statement/{PaymentID}', function($PaymentID) {
		global $link;
		include 'admin/history/statement.php';
	});
}

if ($access == "Parent" || $access == "Admin") {
	$this->get('/mandates/{mandate}', function($mandate) {
	  global $link;
		include 'mandatePDFs.php';
	});
}

if ($access == "Coach") {
	$this->get('/history/{type}/{year}:int/{month}:int', function($type, $year, $month) {
		global $link;
		include 'admin/history/feestatus.php';
	});

	$this->get('/history/{type}/{year}:int/{month}:int/csv', function($type, $year, $month) {
		include BASE_PATH . 'controllers/squads/CSVSquadExport.php';
	});

	$this->get('/history/{type}/{year}:int/{month}:int/json', function($type, $year, $month) {
		include 'admin/history/JSONSquadExport.php';
	});
}

if ($access == "Admin") {
	$this->get('/', function() {
		global $link;
		include 'admin.php';
	});

	$this->get('/current', function() {
		global $link;
		include 'users/ListOfUsers.php';
	});

	$this->post('/current/ajax', function() {
		global $link;
		include 'users/ListOfUsersAjaxBackend.php';
	});

	$this->get('/current/{id}', function($id) {
		global $link;
		include 'users/current.php';
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

	$this->group('/history', function() {
		global $link;

		$this->get('/', function() {
			global $link;
			include 'admin/history/home.php';
		});

		$this->get('/users', function() {
			global $link;
			include 'admin/history/UserList.php';
		});

		$this->get('/users/{id}:int', function($id) {
			global $link;
			include 'admin/history/users.php';
		});

		$this->get('/{year}:int/{month}:int', function($year, $month) {
			global $link;
			include 'admin/history/month.php';
		});

		$this->get('/statement/{PaymentID}', function($PaymentID) {
			global $link;
			include 'admin/history/statement.php';
		});

    $this->get('/statement/{PaymentID}/markpaid/{token}', function($PaymentID, $token) {
			global $link;
			include 'admin/history/StatementMarkPaid.php';
		});

		$this->get('/{type}/{year}:int/{month}:int', function($type, $year, $month) {
			global $link;
			include 'admin/history/feestatus.php';
		});

		$this->get('/{type}/{year}:int/{month}:int/csv', function($type, $year, $month) {
			include BASE_PATH . 'controllers/squads/CSVSquadExport.php';
		});

		$this->get('/{type}/{year}:int/{month}:int/json', function($type, $year, $month) {
			include 'admin/history/JSONSquadExport.php';
		});

	});

  $this->group('/extrafees', function() {
		global $link;

    $this->get('/', function() {
  		global $link;
  		include 'admin/ExtrasList.php';
  	});

    $this->get('/new', function() {
  		global $link;
  		include 'admin/NewExtra.php';
  	});

    $this->post('/new', function() {
  		global $link;
  		include 'admin/NewExtraServer.php';
  	});

    $this->get('/{id}', function($id) {
  		global $link;
  		include 'admin/ExtraIndividual.php';
  	});

		$this->get('/{id}/edit', function($id) {
  		global $link;
  		include 'admin/EditExtra.php';
  	});

		$this->post('/{id}/edit', function($id) {
  		global $link;
  		include 'admin/EditExtraServer.php';
  	});

    $this->get('/{id}/delete', function($id) {
  		global $link;
  		include 'admin/ExtraDelete.php';
  	});

    $this->post('ajax/{id}:int', function($id) {
  		global $link;
  		include 'admin/ExtraIndividualServer.php';
  	});

	});

	$this->get('/testpay', function() {
		global $link;
		include 'testpay.php';
	});

	/*

	// Unavailable in this integration

	$this->get('/newrefund', function() {
		global $link;
		include 'admin/ManualRefund.php';
	});

	$this->post('/newrefund', function() {
		global $link;
		include 'admin/ManualRefundDo.php';
	});

	*/

	$this->get('/galas', function() {
		global $link;
		include 'galas/Home.php';
	});
}

if ($access == "Galas") {
	$this->get('/', function() {
		global $link;
		include 'galas/Home.php';
	});
}

if ($access == "Galas" || $access == "Admin") {
	$this->get('/galas/{id}:int', function($id) {
		global $link;
		include 'galas/EntryCharge.php';
	});

	$this->post('/galas/{id}:int', function($id) {
		global $link;
		include 'galas/EntryChargeAction.php';
	});
}
