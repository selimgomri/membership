<?php
$access = $_SESSION['AccessLevel'];

// Gala Entry Time Sheet
$this->get(['/{id}:int/timesheet', '/competitions/{id}:int/timesheet'], function($id) {
  global $link;
  include "export/PDFTimesheet.php";
});

$this->get(['/{id}:int/timesheet.csv', '/competitions/{id}:int/timesheet.csv'], function($id) {
  include "export/TimeSheet.php";
});

$this->get('/{id}:int/squad-rep-view', function($id) {
	include 'squad-reps/info.php';
});

$this->get('/{id}:int/squad-rep-view.json', function($id) {
	include 'squad-reps/infoOutput.json.php';
});

$this->get('/{id}:int/squad-rep-view.csv', function($id) {
	include 'squad-reps/info.csv.php';
});

$this->post('/squad-reps/entry-states', function() {
	include 'squad-reps/handle-entry-state.php';
});


if ($access == "Parent") {
	// Gala Home
	$this->get('/', function() {
		// Check docs for route - this is a GET
		global $link;
		include 'parentHome.php';
	});

  // View a gala
	$this->get('/{id}:int', function($id) {
		include 'Gala.php';
	});

	// Enter a gala
	if (isset($_SESSION['SuccessfulGalaEntry'])) {
		$this->get('/entergala', function() {
			include 'GalaEntrySuccess.php';
		});
	} else {
		$this->get('/entergala', function() {
			global $link;
			include 'GalaEntryForm.php';
		});
	}

	$this->get('/ajax/entryForm', function() {
		global $link;
		include BASE_PATH . "controllers/ajax/galaForm.php";
	});

	$this->post('/entergala', function() {
		global $link;
		include 'GalaEntryFormPost.php';
	});

	// Gala Entries
	$this->get('/entries', function() {
		global $link;
		include 'parententries.php';
	});

	$this->get('/entries/{id}', function($id) {
		global $link;
		include 'EditEntry.php';
	});

	$this->post('/entries/{id}', function($id) {
		global $link;
		include 'EditEntryPost.php';
	});

	$this->get('/entries/{id}:int/manual-time', function($id) {
		include 'AddManualTime.php';
	});

	$this->post('/entries/{id}:int/manual-time', function($id) {
		include 'AddManualTimePost.php';
	});

	$this->get('/entries/{id}/veto', function($id) {
		include 'indicate-openness/veto.php';
	});

	$this->get('/entries/{id}/veto/do', function($id) {
		global $link;
		include 'indicate-openness/veto-do.php';
	});

	if (env('STRIPE')) {
		$this->group('/pay-for-entries', function() {
			$this->get('/', function() {
				include 'pay/welcome.php';
			});

			$this->post('/', function() {
				include 'pay/welcome-post.php';
			});

			$this->post('/switch-method', function() {
				include 'pay/switch-method.php';
			});

			$this->get('/checkout', function() {
				include 'pay/checkout-page.php';
			});

			$this->get('/checkout/new', function() {
				include 'pay/checkout-page.php';
			});

			$this->get('/complete', function() {
				$newMethod = false;
				include 'pay/complete.php';
			});

			$this->get('/complete/new', function() {
				$newMethod = true;
				include 'pay/complete.php';
			});

			$this->get('/success', function() {
				include 'pay/success.php';
			});
		});
	}
} else if ($access == "Galas" || $access == "Committee" || $access == "Admin" || $access == "Coach") {
	// Gala Home
	$this->get(['/', '/competitions'], function() {
		global $link;
		include 'listGalas.php';
	});

	// Add a gala
	$this->get('/addgala', function() {
		global $link;
		include 'addGala.php';
	});

	$this->post('/addgala', function() {
		global $link;
		include 'addGalaAction.php';
	});

	$this->get('/{id}:int', function($id) {
		include 'Gala.php';
	});

	$this->get('/{id}:int/export.pdf', function($id) {
		include 'gala.pdf.php';
	});

	// View Competitions
	$this->get(['/{id}:int/edit', '/competitions/{id}:int/edit'], function($id) {
		global $link;
		include "competitionSingle.php";
	});

	$this->post(['/{id}:int/edit', '/competitions/{id}:int/edit'], function($id) {
		include "CompetitionSinglePost.php";
	});

	// Gala Entries
	$this->get('/entries/{id}:int', function($id) {
		global $link;
		include 'EditEntry.php';
	});

	$this->post('/entries/{id}:int', function($id) {
		global $link;
		include 'EditEntryPost.php';
	});

	// Gala Entries
	$this->get('/entries', function() {
		global $link;
		include 'allEntries.php';
	});

	$this->get('/ajax/entries', function() {
		global $link;
		require BASE_PATH . 'controllers/ajax/GalaEntries.php';
	});

	$this->post('/ajax/entryProcessed', function() {
		global $link;
		include BASE_PATH . 'controllers/ajax/galaEntriesProcessed.php';
	});

	$this->get('/entries/{id}:int', function($id) {
		global $link;
		include 'singleentry.php';
	});

	$this->get('/entries/{id}:int/manual-time', function($id) {
		include 'AddManualTime.php';
	});

	$this->post('/entries/{id}:int/manual-time', function($id) {
		include 'AddManualTimePost.php';
	});
}

if ($access == "Galas" || $access == "Admin") {

	$this->get('/charges-and-refunds', function() {
		include BASE_PATH . 'controllers/payments/galas/Home.php';
	});

	$this->get('/{id}:int/charges', function($id) {
		include BASE_PATH . 'controllers/payments/galas/EntryCharge.php';
	});

	$this->post('/{id}:int/charges', function($id) {
		include BASE_PATH . 'controllers/payments/galas/EntryChargeAction.php';
	});

	$this->get('/{id}:int/refunds', function($id) {
		include BASE_PATH . 'controllers/payments/galas/RefundCharge.php';
	});

	$this->post('/{id}:int/refunds', function($id) {
		include BASE_PATH . 'controllers/payments/galas/RefundChargePost.php';
	});


	$this->get('/{id}:int/sessions', function($id) {
		include 'indicate-openness/gala-sessions.php';
	});

	$this->get('/{id}:int/sessions/{session}:int/delete', function($id, $session) {
		include 'indicate-openness/delete-session.php';
	});

	$this->post('/{id}:int/sessions', function($id) {
		include 'indicate-openness/gala-sessions-post.php';
	});

	$this->get('/{id}:int/select-entries', function($id) {
		include 'indicate-openness/select-swims.php';
	});

	$this->post('/{id}:int/select-entries', function($id) {
		include 'indicate-openness/select-swims-post.php';
	});

	$this->get('/{id}:int/invite-parents', function($id) {
		include 'indicate-openness/invite-parents.php';
	});

	$this->post('/{id}:int/invite-parents', function($id) {
		include 'indicate-openness/invite-parents-post.php';
	});
}

if ($_SESSION['AccessLevel'] == 'Parent') {
	$this->get('/{id}:int/indicate-availability', function($id) {
		include 'indicate-openness/session-select.php';
	});

	$this->post('/{id}:int/indicate-availability', function($id) {
		include 'indicate-openness/session-select-post.php';
	});
}