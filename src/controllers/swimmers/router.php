<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

// View a Swimmer
$this->get('/{id}:int/x-mode', function($id) {
	require('NewIndivSwimmer.php');
});

if ($access == "Parent") {
	// My Swimmers
	$this->get('/', function() {
    global $link, $userID;
	  require('parentSwimmers.php');
	});

	// View a Swimmer
	$this->get('/{id}:int', function($id) {
    global $link;
	  require('parentSingleSwimmerView.php');
	});

  // Swimmer Membership Card
	$this->get('/{id}:int/membershipcard', function($id) {
    global $link;
	  require('Card.php');
	});

	global $systemInfo;
	$leavers = $systemInfo->getSystemOption('LeaversSquad');

	if ($leavers != null) {
		// Swimmer is leaving
		$this->get('/{id}:int/leaveclub', function($id) {
			require('Leave.php');
		});

		// Swimmer is leaving
		$this->get('/{id}:int/leaveclub/{key}', function($id, $key) {
			require('LeaveDo.php');
		});
	}

	// Edit a Swimmer
	$this->get('/{id}:int/edit', function($id) {
    global $link;
	  require('parentSingleSwimmer.php');
	});

	// Edit a Swimmer
	$this->post('/{id}:int/edit', function($id) {
    global $link;
	  require 'parentSingleSwimmerPost.php';
	});
}
else if ($access == "Committee" || $access == "Galas" || $access == "Coach" || $access == "Admin") {
	// Directory
	$this->get('/', function() {
    global $link;
	  require('swimmerDirectory.php');
	});

	if ($access == "Admin") {
		$this->get('/orphaned', function() {
	    global $link;
		  require('swimmerOrphaned.php');
		});
	}

	$this->post('/ajax/swimmerDirectory', function() {
    global $link;
	  include BASE_PATH . "controllers/ajax/membersList.php";
	});

	// Individual Swimmers
	$this->get('/{id}:int', function($id) {
    global $link;
	  require('singleSwimmerView.php');
	});

	$this->get('/{swimmer}:int/enter-gala', function($swimmer) {
		require BASE_PATH . 'controllers/galas/GalaEntryForm.php';
	});

	$this->post('/{swimmer}:int/enter-gala', function($swimmer) {
		require BASE_PATH . 'controllers/galas/GalaEntryFormPost.php';
	});

	$this->get('/{swimmer}:int/enter-gala-success', function($swimmer) {
		require BASE_PATH . 'controllers/galas/GalaEntryStaffSuccess.php';
	});

  /*
   * Squad moves
   *
   */
	if ($access != "Committee") {
		$this->get('/{id}:int/new-move', function($id) {
			global $link;
			require BASE_PATH . 'controllers/squads/newMove.php';
		});

		$this->post('/{id}:int/new-move', function($id) {
			global $link;
			require BASE_PATH . 'controllers/squads/newMoveAction.php';
		});

		$this->get('/{id}:int/edit-move', function($id) {
			global $link;
			require BASE_PATH . 'controllers/squads/editMove.php';
		});

		$this->post('/{id}:int/edit-move', function($id) {
			global $link;
			require BASE_PATH . 'controllers/squads/editMoveAction.php';
		});

		$this->get('/{id}:int/move-contract', function($id) {
			require BASE_PATH . 'controllers/squads/SquadMoveContract.php';
		});

		$this->get('/{id}:int/cancel-move', function($id) {
			global $link;
			require BASE_PATH . 'controllers/squads/cancelMoveAction.php';
		});
	}
  /*
   * End of squad moves
   */

  function getSwimmerParent($member) {
    global $db;
    $query = $db->prepare("SELECT UserID FROM members WHERE MemberID = ?");
    $query->execute([$member]);
    return $query->fetchColumn();
  }

  $this->get('/{id}:int/contactparent', function($id) {
		global $link;
    $user = getSwimmerParent($id);
		include BASE_PATH . 'controllers/notify/EmailIndividual.php';
	});

	$this->post('/{id}:int/contactparent', function($id) {
		global $link;
    $user = getSwimmerParent($id);
    $returnToSwimmer = true;
		include BASE_PATH . 'controllers/notify/EmailQueuerIndividual.php';
	});

	if ($access != "Committee") {
		$this->get('/{id}:int/attendance', function($id) {
				global $link;
				include BASE_PATH . "controllers/attendance/historyViews/swimmerHistory.php";
			});

		// Swimmer Membership Card
		$this->get('/{id}:int/membershipcard', function($id) {
			global $link;
			require('Card.php');
		});
		
		// Access Keys
		$this->get('/accesskeys', function() {
			global $link;
			require('accesskeys.php');
		});

		// Access Keys
		$this->get('/accesskeys-csv', function() {
			global $link;
			require('accesskeysCSV.php');
		});
	}
}

if ($access == "Admin") {
	// Edit Individual Swimmers
	$this->get('/{id}:int/edit', function($id) {
    global $link;
	  require('singleSwimmerEdit.php');
	});

	$this->post('/{id}:int/edit', function($id) {
    global $link;
	  require('singleSwimmerEdit.php');
	});
}

if ($access != "Parent" && $access != 'Committee') {
	$this->get('/addmember', function() {
    //global $link;
		//include 'AddMember/SelectType.php';
		header("Location: ". autoUrl("swimmers/new"));
	});

  /*
	$this->get('/new/family', function() {
    global $link;
	  include 'AddMember/ActivateFamilyMode.php';
	});

	if (isset($_SESSION['Swimmers-FamilyMode'])) {
		$this->get('/family/exit', function() {
	    global $link;
		  include 'AddMember/ExitFamilyMode.php';
		});
	}
  */

	$this->get('/new', function() {
    global $link;
	  require('AddMember/addMember.php');
	});

	$this->post('/new', function() {
    global $link;
	  require('AddMember/addMemberPost.php');
	});

	$this->get(['/{id}:int/parenthelp', '/parenthelp/{id}:int'], function($id) {
		global $link;
		include 'parentSetupHelp.php';
	});

  $this->post(['/{id}:int/parenthelp', '/parenthelp/{id}:int'], function($id) {
		global $link;
		include 'parentSetupHelpPost.php';
	});
}

// View Medical Notes
$this->get('/{id}:int/medical', function($id) {
	global $link;
	include 'medicalDetails.php';
});

// View Medical Notes
$this->post('/{id}:int/medical', function($id) {
	global $link;
	include 'medicalDetailsPost.php';
});

if ($_SESSION['AccessLevel'] != "Parent") {
  $this->get('/{swimmer}:int/agreement-to-code-of-conduct/{squad}:int', function($swimmer, $squad) {
  	include 'MarkCodeOfConductCompleted.php';
  });
}

$this->group('/{swimmer}:int/times', function() {
	include 'times/router.php';
});

if ($_SESSION['AccessLevel'] == 'Admin') {
	$this->get('/{id}:int/parent', function($id) {
  	include 'parent.php';
	});
	
	$this->post('/{id}:int/parent', function($id) {
  	include 'parentPost.php';
  });
}