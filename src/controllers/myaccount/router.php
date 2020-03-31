<?php

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

// $this->get('/default-access-level', function() {
// 	include 'default-mode.php';
// });

// $this->post('/default-access-level', function() {
// 	include 'default-mode-post.php';
// });

if ($_SESSION['AccessLevel'] == "Parent") {

	// Add swimmer
	$this->get('/add-member', function() {
		global $link;
		require 'add-swimmer.php';
	});

	// Add swimmer
	$this->get('/add-member/auto/{asa}/{acs}', function($asa, $acs) {
		global $link;
		require 'auto-add-swimmer.php';
	});

	$this->post('/add-member', function() {
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

$this->group('/email', function() {

  $this->group('/cc', function() {

    if (isset($_SESSION['VerifyEmailSent']) && bool($_SESSION['VerifyEmailSent'])) {
      $this->get('/new', function() {
      	include 'CC/CCEmailSent.php';
      });
    } else {
      $this->post('/new', function() {
      	include 'CC/NewCCVerify.php';
			});
			
			$this->get('/new', function() {
      	header("Location: " . autoUrl("my-account/email"));
      });
    }

    $this->get('/{id}:int/delete', function($id) {
    	include 'CC/DeleteCC.php';
    });

  });

  $this->get('/', function() {
  	global $link;
  	include 'EmailOptions.php';
  });

  $this->post('/', function() {
  	global $link;
  	include 'EmailOptionsPost.php';
  });

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

$this->group('/linked-accounts', function() {
  $this->get('/', function() {
  	include 'linked-accounts/Home.php';
  });

  $this->get('/new', function() {
  	include 'linked-accounts/New.php';
  });

  $this->post('/new', function() {
  	include 'linked-accounts/NewPost.php';
	});

	$this->get('/{id}:int/switch', function($account) {
  	include 'linked-accounts/Switch.php';
  });
	
	$this->get('/{id}:int/delete', function($id) {
  	include 'linked-accounts/Delete.php';
  });
});

$this->group('/address', function() {
  $this->get('/', function() {
  	include 'address.php';
  });

  $this->post('/', function() {
		include 'address-post.php';
  });
});