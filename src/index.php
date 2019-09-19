<?php

/*
// ----------------------------------------------------------------------------------------------------
// - Display Errors
// ----------------------------------------------------------------------------------------------------
ini_set('display_errors', 'On');
ini_set('html_errors', 0);

// ----------------------------------------------------------------------------------------------------
// - Error Reporting
// ----------------------------------------------------------------------------------------------------
error_reporting(-1);

// ----------------------------------------------------------------------------------------------------
// - Shutdown Handler
// ----------------------------------------------------------------------------------------------------
function ShutdownHandler()
{
    if(@is_array($error = @error_get_last()))
    {
        return(@call_user_func_array('ErrorHandler', $error));
    };

    return(TRUE);
};

register_shutdown_function('ShutdownHandler');

// ----------------------------------------------------------------------------------------------------
// - Error Handler
// ----------------------------------------------------------------------------------------------------
function ErrorHandler($type, $message, $file, $line)
{
    $_ERRORS = Array(
        0x0001 => 'E_ERROR',
        0x0002 => 'E_WARNING',
        0x0004 => 'E_PARSE',
        0x0008 => 'E_NOTICE',
        0x0010 => 'E_CORE_ERROR',
        0x0020 => 'E_CORE_WARNING',
        0x0040 => 'E_COMPILE_ERROR',
        0x0080 => 'E_COMPILE_WARNING',
        0x0100 => 'E_USER_ERROR',
        0x0200 => 'E_USER_WARNING',
        0x0400 => 'E_USER_NOTICE',
        0x0800 => 'E_STRICT',
        0x1000 => 'E_RECOVERABLE_ERROR',
        0x2000 => 'E_DEPRECATED',
        0x4000 => 'E_USER_DEPRECATED'
    );

    if(!@is_string($name = @array_search($type, @array_flip($_ERRORS))))
    {
        $name = 'E_UNKNOWN';
    };

    return(print(@sprintf("%s Error in file \xBB%s\xAB at line %d: %s\n", $name, ($file), $line, $message)));
};

$old_error_handler = set_error_handler("ErrorHandler");
*/

// Do not reveal PHP when sending mail
ini_set('mail.add_x_header', 'Off');
ini_set('expose_php', 'Off');

$time_start = microtime(true);

$executionStartTime = microtime();

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS);

$_SERVER['SERVER_PORT'] = 443;

require BASE_PATH .'vendor/autoload.php';
require "helperclasses/ClassLoader.php";

if (env('COOKIE_PREFIX')) {
  define('COOKIE_PREFIX', env('COOKIE_PREFIX'));
} else {
  define('COOKIE_PREFIX', 'SCDS_MEMBERSHIP_SYSTEMS_');
}

use Symfony\Component\DomCrawler\Crawler;
use GeoIp2\Database\Reader;

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

header("Feature-Policy: fullscreen 'self' https://youtube.com");
header("Referrer-Policy: strict-origin-when-cross-origin");
//header("Content-Security-Policy: default-src https:; object-src data: 'unsafe-eval'; script-src * 'unsafe-inline'; style-src https://www.chesterlestreetasc.co.uk https://account.chesterlestreetasc.co.uk https://fonts.googleapis.com 'unsafe-inline'");
//header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('Server: Chester-le-Magic');
header("Content-Security-Policy: block-all-mixed-content");
header('Expect-CT: enforce, max-age=30, report-uri="https://chesterlestreetasc.report-uri.com/r/d/ct/enforce"');

//halt(901);

/*
if (!(sizeof($_SESSION) > 0)) {
  $_SESSION['TARGET_URL'] = app('request')->curl;
}
*/

function currentUrl() {
  $url = app('request')->protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  if (mb_substr($url, -1) != '/') {
    $url = $url . '/';
  }
  return $url;
}

$db = null;
$link = null;

$get_group = '/club/{clubcode}';

if ($custom_domain_mode) {
  $get_group = '/';
}

$config_file = $config;

require $config_file;
require 'default-config-load.php';

// This code is required so cookies work in dev environments
$cookieSecure = 1;
if (app('request')->protocol == 'http') {
  $cookieSecure = 0;
}

session_start([
    //'cookie_lifetime' => 172800,
    'gc_maxlifetime'      => 86400,
    'cookie_httponly'     => 0,
    'gc_probability'      => 1,
    'use_only_cookies'    => 1,
    'cookie_secure'       => $cookieSecure,
    'use_strict_mode'     => 1,
    'sid_length'          => 128,
    'name'                => COOKIE_PREFIX . 'SessionId',
    'cookie_domain'       => $_SERVER['HTTP_HOST']
]);

function halt(int $statusCode, $throwException = true) {
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
  else if ($statusCode == 503) {
    include "views/503.php";
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
  else if ($statusCode == 902) {
    // Unavailable due to no GC API Key
    include "views/902.php";
  }
  else {
    include "views/500.php";
  }

  if ($throwException) {
    throw new \SCDS\HaltException('Status ' . $statusCode);
  }
}
//$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//define("LINK", mysqli_connect($dbhost, $dbuser, $dbpass, $dbname));
//$link = LINK;

$link = mysqli_connect(env('DB_HOST'), env('DB_USER'), env('DB_PASS'), env('DB_NAME'));
$db = null;
try {
  $db = new PDO("mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4", env('DB_USER'), env('DB_PASS'));
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  halt(500);
}

/* check connection */
if (mysqli_connect_errno()) {
  halt(500);
}

$path = realpath(BASE_PATH . '../');
$headInfo = explode(' ', file_get_contents($path . '/.git/HEAD'));
if ($headInfo[0] == 'ref:') {
  $HEAD_hash = file_get_contents($path . '/.git/' . trim($headInfo[1]));
  define('SOFTWARE_VERSION', $HEAD_hash);
}

$systemInfo = new \SystemInfo($db);

include BASE_PATH . 'includes/GetVars.php';

require_once "database.php";

$currentUser = null;
if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] && isset($_SESSION['UserID']) && $_SESSION['UserID'] != null) {
  $currentUser = new User($_SESSION['UserID'], $db);
}

if (!isset($_SESSION['PWA']) && isset($_COOKIE[COOKIE_PREFIX . 'PWA'])) {
  $_SESSION['PWA'] = $_COOKIE[COOKIE_PREFIX . 'PWA'];
}

if (empty($_SESSION['LoggedIn']) && isset($_COOKIE[COOKIE_PREFIX . 'AutoLogin']) && $_COOKIE[COOKIE_PREFIX . 'AutoLogin'] != "") {
  $sql = "SELECT `UserID`, `Time` FROM `userLogins` WHERE `Hash` = ? AND `Time` >= ? AND `HashActive` = ?";

  $data = [
    $_COOKIE[COOKIE_PREFIX . 'AutoLogin'],
    date('Y-m-d H:i:s', strtotime("120 days ago")),
    1
  ];

  try {
    $query = $db->prepare($sql);
    $query->execute($data);
  } catch (PDOException $e) {
    //halt(500);
  }

  $row = $query->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    $user = $row['UserID'];
    $utc = new DateTimeZone("UTC");
    $time = new DateTime($row['Time'], $utc);

    try {
      $login = new \CLSASC\Membership\Login($db);
      $login->setUser($user);
      $login->stayLoggedIn();
      $login->preventWarningEmail();
      $login->reLogin();
      global $currentUser;
      $currentUser = $login->login();
    } catch (Exception $e) {
      halt(403);
    }

    $hash = hash('sha512', time() . $_SESSION['UserID'] . random_bytes(64));

    $sql = "UPDATE `userLogins` SET `Hash` = ? WHERE `Hash` = ?";
    try {
      $query = $db->prepare($sql);
      $query->execute([$hash, $_COOKIE[COOKIE_PREFIX . 'AutoLogin']]);
    } catch (PDOException $e) {
      halt(500);
    }

    $expiry_time = ($time->format('U'))+60*60*24*120;

    $secure = true;
    if (app('request')->protocol == 'http') {
      $secure = false;
    }
    setcookie(COOKIE_PREFIX . "AutoLogin", $hash, $expiry_time , "/", app('request')->hostname, $secure, false);
  }
}

if (!isset($_SESSION['Browser'])) {
  $browser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

  $_SESSION['Browser']['Name'] = $browser->browser->name;
  $_SESSION['Browser']['OS'] = $browser->os->toString();
  $_SESSION['Browser']['Version'] = $browser->browser->version->value;
}

$currentUser = null;
if (isset($_SESSION['UserID'])) {
  $currentUser = new User($_SESSION['UserID'], $db);
}

if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] && !isset($_SESSION['DisableTrackers'])) {
  $_SESSION['DisableTrackers'] = filter_var(getUserOption($_SESSION['UserID'], "DisableTrackers"), FILTER_VALIDATE_BOOLEAN);
}

$route->group($get_group, function($clubcode = "CLSE") {
  //$_SESSION['ClubCode'] = mb_strtolower($code);

  $this->get('/auth/cookie/redirect', function() {
    //$target = urldecode($target);
    setcookie(COOKIE_PREFIX . "SeenAccount", true, 0, "/", ('request')->hostname, true, false);
    header("Location: https://www.chesterlestreetasc.co.uk");
  });

  // PWA Stuff
  $this->get('/manifest.webmanifest', function() {
    include 'controllers/pwa/manifest.php';
  });

  $this->get('/pwa', function() {
    include 'controllers/pwa/set-pwa.php';
  });

  $this->group('/js', function() {
    include 'dynamic-javascript/router.php';
  });

  $this->get('/setup', function() {
    include 'controllers/db/system-setup.php';
  });

  $this->group('/sessions', function() {
    include 'controllers/attendance/public_sessions/router.php';
  });

  // Password Reset via Link
  $this->get('/email/auth/{id}:int/{auth}', function($id, $auth) {
    global $link;
    require('controllers/myaccount/EmailUpdate.php');
  });

  // Link Accounts
  $this->get('/linked-accounts/auth/{id}:int/{key}', function($id, $key) {
    include 'controllers/myaccount/linked-accounts/NewConfirm.php';
  });

  $this->get('/notify/unsubscribe/{userid}/{email}/{list}', function($userid, $email, $list) {
    global $link;
    include 'controllers/notify/UnsubscribeHandlerAsk.php';
  });

  $this->get('/notify/unsubscribe/{userid}/{email}/{list}/do', function($userid, $email, $list) {
    global $link;
    include 'controllers/notify/UnsubscribeHandler.php';
  });

  $this->get('/timeconverter', function() {
    global $link;
    include 'controllers/conversionsystem/testing.php';
  });

  $this->get('/robots.txt', function() {
    header("Content-Type: text/plain");
    echo "User-agent: *\r\nDisallow: /webhooks/\r\nDisallow: /webhooks\r\nDisallow: /css\r\nDisallow: /js\r\nDisallow: /public\r\nDisallow: /files";
  });

  $this->get(['/help-and-support', '/help-and-support/*'], function() {
    include BASE_PATH . 'controllers/help/help-documentation.php';
  });

  $this->get('/public/*/viewer', function() {
    $filename = $this[0];
    $type = 'public';
    require BASE_PATH . 'controllers/public/Viewer.php';
  });

  $this->get('/public/*', function() {
    $filename = $this[0];
    require BASE_PATH . 'controllers/PublicFileLoader.php';
  });

  $this->post('/timeconverter', function() {
    global $link;
    include 'controllers/conversionsystem/PostTesting.php';
  });

  $this->get('/reportanissue', function() {
    global $link;
    include 'controllers/help/ReportIssueHandler.php';
  });
  $this->post('/reportanissue', function() {
    global $link;
    include 'controllers/help/ReportIssuePost.php';
  });

  $this->group('/ajax', function() {
    global $link;
    include 'controllers/public/router.php';
  });

  $this->group('/about', function() {
    global $link;
    include 'controllers/about/router.php';
  });

  $this->get('/privacy', function() {
    include 'controllers/posts/privacy.php';
  });

  $this->get('/cc/{id}/{hash}/unsubscribe', function($id, $hash) {
    include 'controllers/notify/CCUnsubscribe.php';
  });

  $this->group('/services', function() {
    $this->get('/barcode-generator', function() {
      include 'controllers/barcode-generation-system/gen.php';
    });

    $this->get('/qr/{number}:int/{sizeurl}?:int', function($number, $size_url) {
      include 'controllers/barcode-generation-system/qr-safe.php';
    });

    $this->get('/qr-generator', function() {
      include 'controllers/barcode-generation-system/qr.php';
    });

    include 'controllers/services/router.php';
  });

  if (isset($_SESSION['TWO_FACTOR']) && $_SESSION['TWO_FACTOR']) {
    $this->group('/2fa', function() {
      $this->get('/', function() {
        include BASE_PATH . 'views/TwoFactorCodeInput.php';
      });

      $this->post('/', function() {
        include BASE_PATH . 'controllers/2fa/SubmitCode.php';
      });

      $this->get('/exit', function() {
        $_SESSION = [];
        unset($_SESSION);
        header("Location: " . autoUrl("login"));
      });

      $this->get('/resend', function() {
        include BASE_PATH . 'controllers/2fa/ResendCode.php';
      });
    });

    $this->get(['/', '/*'], function() {
      $_SESSION['TWO_FACTOR'] = true;
      header("Location: " . autoUrl("2fa"));
    });
  }

  $this->group('/oauth2', function() {

    $this->any('/authorize', function() {
      include 'controllers/oauth/AuthorizeController.php';
    });

    $this->any('/token', function() {
      include 'controllers/oauth/TokenController.php';
    });

    $this->get('/userinfo', function() {
      include 'controllers/oauth/UserDetails.php';
    });
  });

  if (empty($_SESSION['LoggedIn'])) {
    $this->post(['/'], function() {
      global $link;
    	include 'controllers/login-go.php';
    });

    // Home
    $this->get('/', function() {
      include "views/Welcome.php";
    });

    $this->get('/login', function() {
      if (empty($_SESSION['TARGET_URL'])) {
        $_SESSION['TARGET_URL'] = "";
      }
      include "views/Login.php";
    });

    // Register
    $this->get(['/register', '/register/family', '/register/family/{fam}:int/{acs}:key'], function($fam = null, $acs = null) {
      global $link;
      require('controllers/registration/register.php');
    });

    $this->group(['/register/ac'], function() {
      include 'controllers/registration/join-from-trial/router.php';
    });

    $this->post('/register', function() {
      global $link;
      require('controllers/registration/registration.php');
    });

    // Confirm Email via Link
    $this->get('/register/auth/{id}:int/new-user/{token}', function($id, $token) {
      global $link;
      require('controllers/registration/RegAuth.php');
    });

    $this->group('/assisted-registration', function() {
      include 'controllers/assisted-registration/setup/router.php';
    });

    // Password Reset via Link
    $this->get('/verify-cc-email/auth/{id}:int/{hash}', function($id, $hash) {
      require('controllers/myaccount/CC/verify.php');
    });

    // Locked Out Password Reset
    $this->get('/resetpassword', function() {
      global $link;
      require('controllers/forgot-password/request.php');
    });

    $this->post('/resetpassword', function() {
      global $link;
      require('controllers/forgot-password/request-action.php');
    });

    // Password Reset via Link
    $this->get('/resetpassword/auth/{token}', function($token) {
      global $link;
      require('controllers/forgot-password/reset.php');
    });

    $this->post('/resetpassword/auth/{token}', function($token) {
      global $link;
      require('controllers/forgot-password/reset-action.php');
    });

    $this->group('/payments/webhooks', function() {
      global $link;
      require('controllers/payments/webhooks.php');
    });

    $this->any('/payments/stripe/webhooks', function() {
      require 'controllers/payments/stripe/webhooks.php' ;
    });

    $this->group('/webhooks', function() {
      global $link;
      require('controllers/webhooks/router.php');
    });

    $this->get('/notify', function() {
      global $link;
      include 'controllers/notify/Help.php';
    });

/*
    $this->get('/files/*', function() {
      $filename = $this[0];
      if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Use your normal account Email Address and Password"');
        halt(401);
      } else if (verifyUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
        require BASE_PATH . 'controllers/FileLoader.php';
      } else {
        header('WWW-Authenticate: Basic realm="Use your normal account Email Address and Password"');
        halt(401);
      }
    });
    */

    // Global Catch All send to login
    $this->any('/*', function() {
      $_SESSION['TARGET_URL'] = app('request')->path;
      header("Location: " . autoUrl("login"));
    });
  } else if (user_needs_registration($_SESSION['UserID'])) {
    $this->group('/renewal', function() {
      global $link;

      include 'controllers/renewal/router.php';
    });

    $this->group('/registration', function() {
      include 'controllers/registration/router.php';
    });

    $this->group('/users', function() {
      include 'controllers/users/router.php';
    });

    $this->any(['/', '/*'], function() {
      header("Location: " . autoUrl("registration"));
    });
  } else {
    // Home

    if ($_SESSION['AccessLevel'] == "Parent") {
      $this->get('/', function() {
        global $link;
      	include 'controllers/ParentDashboard.php';
      });
    } else {
      $this->get('/', function() {
        global $link;
      	include 'controllers/NewDashboard.php';
      });
    }

    $this->get('/login', function() {
      header("Location: " . autoUrl(""));
    });

    $this->group(['/my-account', '/myaccount'], function() {
      global $link;
      include 'controllers/myaccount/router.php';
    });

    $this->group('/swimmers', function() {
      global $link;

      include 'controllers/swimmers/router.php';
    });

    $this->group('/squads', function() {
      global $link;

      include 'controllers/squads/router.php';
    });

    $this->group('/squad-reps', function() {
      include 'controllers/squads/squad-reps/router.php';
    });

    if ($_SESSION['AccessLevel'] != "Parent") {
      $this->group('/trials', function() {
        include 'controllers/trials/router.php';
      });
    }

    $this->group(['/posts', '/pages'], function() {
      global $link;

      include 'controllers/posts/router.php';
    });

    $this->group('/people', function() {
      global $link;
      $people = true;
      include 'controllers/posts/router.php';
    });

    $this->group('/registration', function() {
      global $link;

      include 'controllers/registration/router.php';
    });

    $this->group(['/attendance', '/registers'], function() {
      global $link;

      include 'controllers/attendance/router.php';
    });

    $this->group('/users', function() {
      include 'controllers/users/router.php';
    });

    $this->group('/galas', function() {
      global $link;

      include 'controllers/galas/router.php';
    });

    $this->group('/family', function() {
      global $db;
      require('controllers/family/router.php');
    });

    $this->group('/renewal', function() {
      global $link;

      include 'controllers/renewal/router.php';
    });

    $this->group('/registration', function() {
      global $link;

      include 'controllers/registration/router.php';
    });

    $this->group('/payments', function() {
      global $link;

      include 'controllers/payments/router.php';
    });

    $this->group('/form-agreement', function() {
      include 'controllers/forms/router.php';
    });

    $this->group('/notify', function() {
      global $link;
      include 'controllers/notify/router.php';
    });

    $this->group(['/emergency-contacts', '/emergencycontacts'], function() {
      global $link;

      include 'controllers/emergencycontacts/router.php';
    });

    $this->group('/webhooks', function() {
      global $link;

      $this->group('/payments', function() {
        global $link;

        include 'controllers/payments/webhooks.php';
      });
    });

    $this->group('/qualifications', function() {
      include 'controllers/qualifications/router.php';
    });

    $this->group('/assisted-registration', function() {
      include 'controllers/assisted-registration/router.php';
    });

    $this->group('/admin', function() {
      include 'controllers/qualifications/AdminRouter.php';
    });

    $this->group('/resources', function() {
      include 'controllers/resources/router.php';
    });

    // Log out
    $this->any(['/logout', '/logout.php'], function() {
      global $link;
      require('controllers/logout.php');
    });

    /*$this->any('test', function() {
      global $link;
      global $db;

      include BASE_PATH . 'views/header.php';

      echo myMonthlyFeeTable($link, 1);

      include BASE_PATH . 'views/footer.php';
    });*/

    if ($_SESSION['AccessLevel'] == "Admin") {
      $this->group('/settings', function() {
        include BASE_PATH . 'controllers/settings/router.php';
      });

      if (bool(env('IS_CLS'))) {
        $this->get('/about:php', function() {
          echo phpinfo();
        });

        $this->get('/about:session', function() {
          pre($_SESSION);
        });

        $this->get('/about:server', function() {
          pre($_SERVER);
          pre($_ENV);
        });

        $this->get('/about:cookies', function() {
          pre($_COOKIE);
        });

        $this->get('/about:stopcodes/{code}:int', function($code) {
          halt((int) $code);
        });

        $this->get('/pdf-test', function() {
          include 'controllers/PDFTest.php';
        });

        /*
        $this->get('/test', function() {
          //use \Twilio\Rest\Client;

          try {
            // Your Account SID and Auth Token from twilio.com/console
            $account_sid = env('TWILIO_AC_SID');
            $auth_token = env('TWILIO_AC_AUTH_TOKEN');
            // In production, these should be environment variables. E.g.:
            // $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

            // A Twilio number you own with SMS capabilities
            $twilio_number = env('TWILIO_NUMBER');

            $client = new Twilio\Rest\Client($account_sid, $auth_token);
            $client->messages->create(
              // Where to send a text message (your cell phone?)
              '+447577002981',
              [
                'from' => $twilio_number,
                'body' => 'I sent this message in under 10 minutes!'
              ]
            );
          } catch (Exception $e) {
            pre($e);
          }
        });
        */

        $this->get('/test', function() {
          global $db;
          $fees = \SCDS\Membership\ClubMembership::create($db, 12, false);
          pre($fees->getFeeItems());
          pre($fees->getFee());
        });
  
      }

      $this->get('/update', function() {
        try {
          $old_path = getcwd();
          chdir(BASE_PATH);
          $output = shell_exec('bash ' . BASE_PATH . 'update.sh');
          chdir($old_path);
          pre($output);
        } catch (Exception $e) {
          halt(500);
        }
      });

      $this->group('/db', function() {
        // Handle database migrations
        include 'controllers/migrations/router.php';
      });
    }
  }

  $this->get('/files/*/viewer', function() {
    $filename = $this[0];
    $type = 'files';
    require BASE_PATH . 'controllers/public/Viewer.php';
  });

  $this->get('/files/*', function() {
    $filename = $this[0];
    require BASE_PATH . 'controllers/FileLoader.php';
  });

  // Global Catch All 404
  $this->any('/', function() {
    header("Location: " . autoUrl(""));
  });

  // Global Catch All 404
  $this->any('/*', function() {
    global $link;
    include 'views/404.php';
  });

});

try {
  $route->end();
} catch (\SCDS\HaltException $e) {
  // Do nothing, just stops execution
} catch (\SCDS\CSRFValidityException $e) {
  // Deals with any uncaught SCRF problems
  halt(403, false);
} catch (Exception $e) {
  // This catches any uncaught exceptions.
  halt(500, false);
} finally {
  // Any actions which must always happen at end
}

// Close SQL Database Connections
mysqli_close($link);
$db = null;
