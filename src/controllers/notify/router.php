<?php

global $db;

$access = $_SESSION['AccessLevel'];

if ($access != "Admin" && $access != "Coach" && $access != "Galas") {
$this->get('/', function() {
	global $link;
	include 'Help.php';
});
}

if ($access == "Admin" || $access == "Coach" || $access == "Galas") {
	$this->get('/', function() {
		global $link;
		include 'Home.php';
	});

	$this->get('/newemail', function() {
		global $link;
		include 'Email.php';
	});

	$this->post('/newemail', function() {
		global $link;
		include 'EmailQueuer.php';
	});

	$this->get('/email', function() {
		global $link;
		include 'EmailList.php';
	});

	$this->get('/email/{id}:int', function($id) {
		global $link;
		include 'EmailID.php';
	});

  $this->group('/history', function() {
		global $link;

    $this->get(['/', '/page/{page}:int'], function($page = null) {
			global $link;
			include 'MessageHistory.php';
		});
  });

	$this->group('/lists', function() {
		global $link;

		$this->get('/', function() {
			global $link;
			include 'ListOfLists.php';
		});

		$this->get('/new', function() {
			global $link;
			include 'NewList.php';
		});

		$this->post('/new', function() {
			global $link;
			include 'NewListServer.php';
		});

		$this->get('/{id}:int', function($id) {
			global $link;
			include 'ListIndividual.php';
		});

		$this->post('ajax/{id}:int', function($id) {
			global $link;
			include 'ListIndividualServer.php';
		});

		$this->get('/{id}:int/edit', function($id) {
			global $link;
			include 'EditList.php';
		});

		$this->post('/{id}:int/edit', function($id) {
			global $link;
			include 'EditListServer.php';
		});

		$this->get('/{id}:int/delete', function($id) {
			global $link;
			include 'DeleteList.php';
		});

	});

	if ($_SESSION['AccessLevel'] == "Admin") {
		$this->get('/sms', function() {
			global $link, $db;
			include 'SMSList.php';
		});

		$this->post('/sms/ajax', function() {
			global $link, $db;
			include 'SMSListFetch.php';
		});
	}

}
