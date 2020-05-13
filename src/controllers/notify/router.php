<?php

$db = app()->db;

$rep = false;
if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') {
	$getSquadCount = $db->prepare("SELECT COUNT(*) FROM squads INNER JOIN squadReps ON squads.SquadID = squadReps.Squad AND squadReps.User = ?");
	$getSquadCount->execute([
		$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
	]);
	if ($getSquadCount->fetchColumn() > 0) {
		$rep = true;
	}

	$getListCount = $db->prepare("SELECT COUNT(*) FROM `targetedLists` INNER JOIN listSenders ON listSenders.List = targetedLists.ID WHERE listSenders.User = ?");
	$getListCount->execute([
		$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
	]);
	if ($getListCount->fetchColumn() > 0) {
		$rep = true;
	}
}

$access = $_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'];

if ($access != "Admin" && $access != "Coach" && $access != "Galas" && !$rep) {
	$this->get('/', function() {
		
		include 'Help.php';
	});
}

if ($access == "Admin" || $access == "Coach" || $access == "Galas" || $rep) {
	$this->get('/', function() {
		
		include 'Home.php';
	});

  $this->group(['/new', '/newemail'], function() {

  	$this->get('/', function() {
  		
  		include 'Email.php';
  	});

  	$this->post('/', function() {
  		
  		include 'EmailQueuer.php';
  	});

    $this->get('/individual/{user}?:int/', function($user = null) {
  		
  		include 'EmailIndividual.php';
  	});

  	$this->post('/individual/{user}?:int/', function($user = null) {
  		
  		include 'EmailQueuerIndividual.php';
  	});

	});
	
	$this->get('/reply-to', function() {
		include 'ReplyTo.php';
	});

	$this->post('/reply-to', function() {
		include 'ReplyToPost.php';
	});

  if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") {
  	$this->get('/pending', function() {
  		
  		include 'EmailList.php';
  	});

  	$this->get('/email/{id}:int', function($id) {
  		
  		include 'EmailID.php';
  	});
  }

  $this->group('/history', function() {
		

    $this->get('/', function() {
			
			include 'MessageHistory.php';
		});
  });

	if (!$rep) {

		$this->group('/lists', function() {
			

			$this->get('/', function() {
				
				include 'ListOfLists.php';
			});

			$this->get('/new', function() {
				
				include 'NewList.php';
			});

			$this->post('/new', function() {
				
				include 'NewListServer.php';
			});

			$this->get('/{id}:int', function($id) {
				
				include 'ListIndividual.php';
			});

			$this->post('ajax/{id}:int', function($id) {
				
				include 'ListIndividualServer.php';
			});

			$this->get('/{id}:int/edit', function($id) {
				include 'EditList.php';
			});

			$this->post('/{id}:int/edit', function($id) {
				
				include 'EditListServer.php';
			});

			$this->get('/{id}:int/delete', function($id) {
				
				include 'DeleteList.php';
			});
		});
	}

	if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") {
		$this->get('/sms', function() {
			$db = app()->db;
			include 'SMSList.php';
		});

		$this->post('/sms/ajax', function() {
			$db = app()->db;
			include 'SMSListFetch.php';
		});
	}

}
