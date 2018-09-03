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

// ** DISABLE PROBABILITY BASED SESSION GARBAGE COLLECTION**
// Causes issues for users
ini_set('session.gc_probability', 0);

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
ini_set('session.name', "CLSASC_SessionId");

session_start([
    'cookie_lifetime' => 172800,
    'gc_maxlifetime' => 172800,
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
  else if ($statusCode == 900) {
    // Unavailable for Regulatory Reasons
    include "views/900.php";
  }
  else if ($statusCode == 901) {
    // Unavailable due to GDPR
    include "views/901.php";
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

use Symfony\Component\DomCrawler\Crawler;
use GeoIp2\Database\Reader;

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

/* check connection */
if (mysqli_connect_errno()) {
  halt(500);
}

require_once "database.php";

if (empty($_SESSION['LoggedIn']) && isset($_COOKIE['CLSASC_AutoLogin']) && $_COOKIE['CLSASC_AutoLogin'] != "") {
  $sql = "SELECT `UserID`, `Time` FROM `userLogins` WHERE `Hash` = ? AND `Time` >= ? AND `HashActive` = ?";

  $data = [
    $_COOKIE['CLSASC_AutoLogin'],
    date('Y-m-d H:i:s', strtotime("120 days ago")),
    1
  ];

  try {
    $query = $db->prepare($sql);
    $query->execute($data);
  } catch (PDOException $e) {
    //halt(500);
  }

  $row = $query->fetchAll(PDO::FETCH_ASSOC);
  if (sizeof($row) == 1) {
    $user = $row[0]['UserID'];
    $utc = new DateTimeZone("UTC");
    $time = new DateTime($row[0]['Time'], $utc);

    $sql = "SELECT * FROM `users` WHERE `UserID` = ?";

    try {
      $query = $db->prepare($sql);
      $query->execute([$user]);
    } catch (PDOException $e) {
      //halt(500);
    }

    $row = $query->fetchAll(PDO::FETCH_ASSOC);

    $row = $row[0];

    $_SESSION['Username'] = $row['Username'];
    $_SESSION['EmailAddress'] = $row['EmailAddress'];
    $_SESSION['Forename'] = $row['Forename'];
    $_SESSION['Surname'] = $row['Surname'];
    $_SESSION['UserID'] = $user;
    $_SESSION['AccessLevel'] = $row['AccessLevel'];
    $_SESSION['LoggedIn'] = 1;

    $hash = hash('sha512', time() . $_SESSION['UserID'] . random_bytes(64));

    $sql = "UPDATE `userLogins` SET `Hash` = ? WHERE `Hash` = ?";
    try {
      $query = $db->prepare($sql);
      $query->execute([$hash, $_COOKIE['CLSASC_AutoLogin']]);
    } catch (PDOException $e) {
      halt(500);
    }

    $expiry_time = ($time->format('U'))+60*60*24*120;

    setcookie("CLSASC_AutoLogin", $hash, $expiry_time , "/", app('request')->hostname, true, true);
  }
}

//halt(901);

// Password Reset via Link
$route->get('/email/auth/{id}:int/{auth}', function($id, $auth) {
  global $link;
  require('controllers/myaccount/EmailUpdate.php');
});

$route->get('/notify/unsubscribe/{userid}/{email}/{list}', function($userid, $email, $list) {
  global $link;
  include 'controllers/notify/UnsubscribeHandlerAsk.php';
});

$route->get('/notify/unsubscribe/{userid}/{email}/{list}/do', function($userid, $email, $list) {
  global $link;
  include 'controllers/notify/UnsubscribeHandler.php';
});

$route->get('/timeconverter', function() {
  global $link;
  include 'controllers/conversionsystem/testing.php';
});

$route->post('/timeconverter', function() {
  global $link;
  include 'controllers/conversionsystem/PostTesting.php';
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

  // Confirm Email via Link
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
} else if (user_needs_registration($_SESSION['UserID'])) {
  $route->group('/renewal', function() {
    global $link;

    include 'controllers/renewal/router.php';
  });

  $route->group('/registration', function() {
    global $link;

    include 'controllers/registration/router.php';
  });

  $route->any(['/', '/*'], function() {
    header("Location: " . autoUrl("registration"));
  });
} else {
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
    include 'controllers/myaccount/router.php';
  });

  $route->group('/swimmers', function() {
    global $link;

    include 'controllers/swimmers/router.php';
  });

  $route->group('/squads', function() {
    global $link;

    include 'controllers/squads/router.php';
  });

  $route->group(['/posts', '/pages'], function() {
    global $link;

    include 'controllers/posts/router.php';
  });

  $route->group('/registration', function() {
    global $link;

    include 'controllers/registration/router.php';
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

  $route->any('test', function() {
    global $link;
    global $db;

    include BASE_PATH . 'views/header.php';



    include BASE_PATH . 'views/footer.php';
  });
}

// Global Catch All 404
$route->any('/', function() {
  header("Location: " . autoUrl(""));
});

// Global Catch All 404
$route->any('/*', function() {
  global $link;
  include 'views/404.php';
});

$route->end();
