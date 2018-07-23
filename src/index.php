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
  if ($statusCode == 400) {
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

$route->group('/auth/saml', function() {
  $this->any(['', 'index.php', 'index.php/*'], function() {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    include 'saml/www/index.php';
  });

  $this->any(['logout.php', 'logout.php/*'], function() {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    include 'saml/www/logout.php';
  });

  $this->any(['module.php', 'module.php/*'], function() {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    include 'saml/www/module.php';
  });

  $this->any(['errorreport.php', 'errorreport.php/*'], function() {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    include 'saml/www/errorreport.php';
  });

  $this->any(['authmemcookie.php', 'authmemcookie.php/*'], function() {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    include 'saml/www/authmemcookie.php';
  });

  $this->get('resources/*', function() {
    include 'saml/www/resources/' . $this[0];
  });

  $this->group('/admin', function() {
    $this->any(['', 'index.php', 'index.php/*'], function() {
      $_SERVER['PATH_INFO'] = '/' . $this[0];
      include 'saml/www/admin/index.php';
    });

    $this->any(['hostnames.php', 'hostnames.php/*'], function() {
      $_SERVER['PATH_INFO'] = '/' . $this[0];
      include 'saml/www/admin/hostnames.php';
    });

    $this->any(['metadata-converter.php', 'metadata-converter.php/*'], function() {
      $_SERVER['PATH_INFO'] = '/' . $this[0];
      include 'saml/www/admin/metadata-converter.php';
    });

    $this->any(['phpinfo.php', 'phpinfo.php/*'], function() {
      $_SERVER['PATH_INFO'] = '/' . $this[0];
      include 'saml/www/admin/phpinfo.php';
    });

    $this->any(['sandbox.php', 'sandbox.php/*'], function() {
      $_SERVER['PATH_INFO'] = '/' . $this[0];
      include 'saml/www/admin/sandbox.php';
    });
  });

  $this->any(['/saml2/idp/{file}', '/saml2/idp/{file}/*'], function($file) {
    $_SERVER['PATH_INFO'] = '/' . $this[0];
    echo 'saml/www/saml2/idp/' . $file;
    include 'saml/www/saml2/idp/' . $file;
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

  $route->post('/', function() {
    global $link;
  	include 'controllers/login-go.php';
  });

  // Register
  $route->get('/register', function() {
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

  $route->group('/renewal', function() {
    global $link;

    include 'controllers/renewal/router.php';
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

  $route->any(['/test', '/x-text'], function() {
    global $link;

    pre(json_decode('{"PaymentType":"SquadFees","Members":[{"Member":"1","MemberName":"Christopher Heppell","FeeName":"Non Affiliated","Fee":"0.00"},{"Member":"2","MemberName":"Lauren Heppell","FeeName":"B2","Fee":"65.00"}]}'));
    pre(json_decode('{"PaymentType":"ExtraFees","Members":[{"Member":"2","MemberName":"Lauren Heppell","FeeName":"CrossFit (1 Session)","Fee":"7.50"}]}'));
  });

  // Global Catch All 404
  $route->any('/*', function() {
    global $link;
    include 'views/404.php';
  });
}

$route->end();
