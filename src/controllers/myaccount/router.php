<?

// My Account
$this->get('/', function() {
	global $link;
	include 'Profile.php';
});

$this->post('/', function() {
	global $link;
	include 'Profile.php';
});

// Manage Password
$this->get('/password', function() {
	global $link;
	require 'change-password.php';
});

$this->post('/password', function() {
	global $link;
	require 'change-password-action.php';
});

if ($_SESSION['AccessLevel'] == "Parent") {

	// Add swimmer
	$this->get('/addswimmer', function() {
		global $link;
		require 'add-swimmer.php';
	});

	// Add swimmer
	$this->get('/addswimmer/auto/{asa}/{acs}', function($asa, $acs) {
		global $link;
		require 'auto-add-swimmer.php';
	});

	$this->post('/addswimmer', function() {
		global $link;
		require 'add-swimmer-action.php';
	});

	// Add swimmer
	$this->get(['/addswimmergroup', '/addswimmergroup/{fam}/{acs}'], function($fam = null, $acs = null) {
		global $link;
		require 'add-group.php';
	});

	$this->post('/addswimmergroup', function() {
		global $link;
		require 'add-group-action.php';
	});

	$this->get(['notifyhistory/', 'notifyhistory/page/{page}:int'], function($page = null) {
		global $link;
		include BASE_PATH . 'controllers/notify/MyMessageHistory.php';
	});
}

$this->get(['loginhistory/', 'loginhistory/page/{page}:int'], function($page = null) {
	global $link;
	include 'LoginHistory.php';
});

$this->get('/email', function() {
	global $link;
	include 'EmailOptions.php';
});

$this->post('/email', function() {
	global $link;
	include 'EmailOptionsPost.php';
});

$this->get('/googleauthenticator', function() {
	include 'EnableGoogleAuthenticator.php';
});

$this->get('/googleauthenticator/setup', function() {
	include 'GoogleAuthenticatorKeyGen.php';
});

$this->get('/googleauthenticator/disable', function() {
	include 'GoogleAuthenticatorDisable.php';
});

$this->post('/googleauthenticator/setup', function() {
	include 'GoogleAuthenticatorKeyVerify.php';
});

$this->group('/general', function() {

  $this->get('/', function() {
  	global $link;
  	include 'GeneralOptions.php';
  });

  $this->post('/', function() {
  	global $link;
  	include 'GeneralOptionsPost.php';
  });

  $this->get('/download-personal-data', function() {
  	include 'GDPR/UserDataDump.php';
  });

  $this->get('/download-member-data/{id}:int', function($id) {
  	include 'GDPR/MemberDataDump.php';
  });

});
