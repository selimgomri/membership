<?php
$executionStartTime = microtime();

$_SERVER['SERVER_PORT'] = 443;

// Do not reveal PHP when sending mail
ini_set('mail.add_x_header', 'Off');
ini_set('expose_php', 'Off');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('session.gc_maxlifetime', 2419200);

define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_PATH', __DIR__ . DS, TRUE);
//Show errors
//===================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//===================================

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
ini_set('session.cookie_secure', 1);

// Use strict mode
ini_set('session.use_strict_mode', 1);

// Session ID length
ini_set('session.sid_length', 128);

// SessionName
ini_set('session.name', "clsascMembershipID");

session_start([
    'cookie_lifetime' => 2419200,
    'gc_maxlifetime' => 2419200,
]);

require BASE_PATH.'vendor/autoload.php';
require "config.php";
require "helperclasses/ClassLoader.php";

function halt(int $statusCode) {
  if ($statusCode == 200) {
    include "views/200.php";
  }
  else if ($statusCode == 400) {
    include "views/400.php";
  }
  else if ($statusCode == 401) {
    include "views/401.php";
  }
  else if ($statusCode == 403) {
    include "views/403.php";
  }
  else if ($statusCode == 404) {
    include "views/404.php";
  }
  else if ($statusCode == 502) {
    include "views/502.php";
  }
  else if ($statusCode == 0) {
    include "views/000.php";
  }
  else {
    include "views/500.php";
  }
  exit();
}

//$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//define("LINK", mysqli_connect($dbhost, $dbuser, $dbpass, $dbname), true);
//$link = LINK;

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$db = new PDO("mysql:host=" . $dbhost . ";dbname=" . $dbname . "", $dbuser, $dbpass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* check connection */
if (mysqli_connect_errno()) {
  halt(500);
}

require_once "database.php";

use Symfony\Component\DomCrawler\Crawler;

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

$route->any('/notify/unsubscribe/{email}', function($email) {
  global $link;
  include 'controllers/notify/UnsubscribeHandler.php';
});

$route->group('/ajax', function() {
  global $link;
  include 'controllers/public/router.php';
});

$route->group('/services', function() {

  $this->get('/barcode-generator', function() {
    include 'controllers/barcode-generation-system/gen.php';
  });

  $this->get('/qr-generator', function() {
    include 'controllers/barcode-generation-system/qr.php';
  });
});

if (empty($_SESSION['LoggedIn'])) {
  // Home
  $route->get('/', function() {
    global $link;
  	include 'controllers/login.php';
  });

  $route->get('/login', function() {
    header("Location: " . autoUrl(""));
  });

  $route->any(['/'], function() {
    global $link;
  	include 'controllers/login-go.php';
  });

  // Register
  $route->get(['/register', '/register/family', '/register/family/{fam}:int/{acs}:key'], function($fam = null, $acs = null) {
    global $link;
    require('controllers/registration/register.php');
  });

  $route->post('/register', function() {
    global $link;
    require('controllers/registration/registration.php');
  });

  // Password Reset via Link
  $route->get('/register/auth/{id}:int/new-user/{token}', function($id, $token) {
    global $link;
    require('controllers/registration/RegAuth.php');
  });

  // Locked Out Password Reset
  $route->get('/resetpassword', function() {
    global $link;
    require('controllers/forgot-password/request.php');
  });

  $route->post('/resetpassword', function() {
    global $link;
    require('controllers/forgot-password/request-action.php');
  });

  // Password Reset via Link
  $route->get('/resetpassword/auth/{token}', function($token) {
    global $link;
    require('controllers/forgot-password/reset.php');
  });

  $route->post('/resetpassword/auth/{token}', function($token) {
    global $link;
    require('controllers/forgot-password/reset-action.php');
  });

  $route->group('/payments/webhooks', function() {
    global $link;
    require('controllers/payments/webhooks.php');
  });

  $route->group('/webhooks', function() {
    global $link;
    require('controllers/webhooks/router.php');
  });

  $route->get('/notify', function() {
    global $link;
    include 'controllers/notify/Help.php';
  });

  // Global Catch All send to login
  $route->any('/*', function() {
    global $link;
    include 'controllers/login.php';
  });
}
else {
  // Home
  $route->get('/', function() {
    global $link;
  	include 'controllers/dashboard.php';
  });

  $route->get('/login', function() {
    header("Location: " . autoUrl(""));
  });

  $route->group('/myaccount', function() {
    global $link;

  	// My Account
  	$this->get('/', function() {
      global $link;
  	  require('controllers/myaccount/index.php');
  	});

    $this->post('/', function() {
      global $link;
  	  require('controllers/myaccount/index.php');
  	});

  	// Manage Password
  	$this->get('/password', function() {
      global $link;
  	  require('controllers/myaccount/change-password.php');
  	});

  	$this->post('/password', function() {
      global $link;
  	  require('controllers/myaccount/change-password-action.php');
  	});

  	if ($_SESSION['AccessLevel'] == "Parent") {

  	// Add swimmer
  	$this->get('/addswimmer', function() {
      global $link;
  	  require('controllers/myaccount/add-swimmer.php');
  	});

    // Add swimmer
  	$this->get('/addswimmer/auto/{asa}/{acs}', function($asa, $acs) {
      global $link;
  	  require('controllers/myaccount/auto-add-swimmer.php');
  	});

  	$this->post('/addswimmer', function() {
      global $link;
  	  require('controllers/myaccount/add-swimmer-action.php');
  	});

  	$this->get(['notify/history/', 'notify/history/page/{page}:int'], function($page = null) {
			global $link;
			include 'controllers/notify/MyMessageHistory.php';
		});

		}

  });

  $route->group('/swimmers', function() {
    global $link;

    include 'controllers/swimmers/router.php';
  });

  $route->group('/squads', function() {
    global $link;

    include 'controllers/squads/router.php';
  });

  $route->group('/attendance', function() {
    global $link;

    include 'controllers/attendance/router.php';
  });

  $route->group('/users', function() {
    global $link;

    include 'controllers/users/router.php';
  });

  $route->group('/galas', function() {
    global $link;

    include 'controllers/galas/router.php';
  });

  $route->group('/family', function() {
    global $db;
    require('controllers/family/router.php');
  });

  $route->group('/renewal', function() {
    global $link;

    include 'controllers/renewal/router.php';
  });

  $route->group('/registration', function() {
    global $link;

    include 'controllers/registration/router.php';
  });

  $route->group('/payments', function() {
    global $link;

    include 'controllers/payments/router.php';
  });

  $route->group('/notify', function() {
    global $link;

    include 'controllers/notify/router.php';
  });

  $route->group('/emergencycontacts', function() {
    global $link;

    include 'controllers/emergencycontacts/router.php';
  });

  $route->group('/webhooks', function() {
    global $link;

    $this->group('/payments', function() {
      global $link;

      include 'controllers/payments/webhooks.php';
    });
  });

  // Log out
  $route->any(['/logout', '/logout.php'], function() {
    global $link;
    require('controllers/logout.php');
  });

  $route->group('/oauth2', function() {
    global $link;

    $this->get('/auth', function() {
      global $link;
      include 'controllers/oauth/code.php';
    });
  });

  $route->any('asa/{asa}/{ev}/{course}', function($asa, $ev, $course) {
    global $link;

    include BASE_PATH . 'views/header.php'; ?>
    <div class="container">
      <h1>Times</h1>
      <table class="table table-sm">
        <? echo pre(getTimesInFull($asa, $ev, $course)); ?>
      </table>
    </div>
    <? include BASE_PATH . 'views/footer.php';
  });

  // Global Catch All 404
  $route->any('/*', function() {
    global $link;
    include 'views/404.php';
  });
}

$route->end();
