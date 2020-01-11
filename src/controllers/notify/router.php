<?php

global $db;

$rep = false;
if ($_SESSION['AccessLevel'] == 'Parent') {
	$getSquadCount = $db->prepare("SELECT COUNT(*) FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad AND squadReps.User = ?");
	$getSquadCount->execute([
		$_SESSION['UserID']
	]);
	if ($getSquadCount->fetchColumn() > 0) {
		$rep = true;
	}

	$getListCount = $db->prepare("SELECT COUNT(*) FROM `targetedLists` INNER JOIN listSenders ON listSenders.List = targetedLists.ID WHERE listSenders.User = ?");
	$getListCount->execute([
		$_SESSION['UserID']
	]);
	if ($getListCount->fetchColumn() > 0) {
		$rep = true;
	}
}

$access = $_SESSION['AccessLevel'];

if ($access != "Admin" && $access != "Coach" && $access != "Galas" && !$rep) {
	$this->get('/', function() {
		global $link;
		include 'Help.php';
	});
}

if ($access == "Admin" || $access == "Coach" || $access == "Galas" || $rep) {
	$this->get('/', function() {
		global $link;
		include 'Home.php';
	});

  $this->group(['/new', '/newemail'], function() {

  	$this->get('/', function() {
  		global $link;
  		include 'Email.php';
  	});

  	$this->post('/', function() {
  		global $link;
  		include 'EmailQueuer.php';
  	});

    $this->get('/individual/{user}?:int/', function($user = null) {
  		global $link;
  		include 'EmailIndividual.php';
  	});

  	$this->post('/individual/{user}?:int/', function($user = null) {
  		global $link;
  		include 'EmailQueuerIndividual.php';
  	});

	});
	
	$this->get('/reply-to', function() {
		include 'ReplyTo.php';
	});

	$this->post('/reply-to', function() {
		include 'ReplyToPost.php';
	});

  if ($_SESSION['AccessLevel'] == "Admin") {
  	$this->get('/pending', function() {
  		global $link;
  		include 'EmailList.php';
  	});

  	$this->get('/email/{id}:int', function($id) {
  		global $link;
  		include 'EmailID.php';
  	});
  }

  $this->group('/history', function() {
		global $link;

    $this->get(['/', '/page/{page}:int'], function($page = null) {
			global $link;
			include 'MessageHistory.php';
		});
  });

	if (!$rep) {

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
	}

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
