<?php


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
  if (@is_array($error = @error_get_last())) {
    return (@call_user_func_array('ErrorHandler', $error));
  };

  return (TRUE);
};

register_shutdown_function('ShutdownHandler');

// ----------------------------------------------------------------------------------------------------
// - Error Handler
// ----------------------------------------------------------------------------------------------------
function ErrorHandler($type, $message, $file, $line)
{
  $_ERRORS = array(
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

  if (!@is_string($name = @array_search($type, @array_flip($_ERRORS)))) {
    $name = 'E_UNKNOWN';
  };

  return (print(@sprintf("%s Error in file \xBB%s\xAB at line %d: %s\n", $name, ($file), $line, $message)));
};

$old_error_handler = set_error_handler("ErrorHandler");


// Do not reveal PHP when sending mail
ini_set('expose_php', 'Off');

$time_start = microtime(true);

$executionStartTime = microtime();

require 'common.php';

$_SERVER['SERVER_PORT'] = 443;

if (env('COOKIE_PREFIX')) {
  define('COOKIE_PREFIX', env('COOKIE_PREFIX'));
} else {
  define('COOKIE_PREFIX', 'SCDS_MEMBERSHIP_SYSTEMS_');
}

if (env('COOKIE_PATH')) {
  define('COOKIE_PATH', env('COOKIE_PATH'));
} else {
  define('COOKIE_PATH', '/');
}

if (env('CACHE_DIR')) {
  define('CACHE_DIR', env('CACHE_DIR'));
} else {
  define('CACHE_DIR', BASE_PATH . 'cache/');
}

use Symfony\Component\DomCrawler\Crawler;
use GeoIp2\Database\Reader;

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

//halt(901);

/*
if (!(sizeof($_SESSION) > 0)) {
  $_SESSION['TARGET_URL'] = app('request')->curl;
}
*/

function currentUrl()
{
  $uri = ltrim($_SERVER["REQUEST_URI"], '/');
  $url = autoUrl($uri);
  if (mb_substr($url, -1) != '/') {
    $url = $url . '/';
  }
  return $url;
}

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
  'cookie_domain'       => $_SERVER['HTTP_HOST'],
  'cookie_path'         => COOKIE_PATH,
]);

function halt(int $statusCode, $throwException = true)
{
  try {
    ob_get_clean();
  } catch (Exception | Error $e) {
    // Can't clean ob
  }

  try {
    if ($statusCode == 000) {
      require "views/000.php";
    } else if ($statusCode == 200) {
      require "views/200.php";
    } else if ($statusCode == 400) {
      require "views/400.php";
    } else if ($statusCode == 401) {
      require "views/401.php";
    } else if ($statusCode == 403) {
      require "views/403.php";
    } else if ($statusCode == 404) {
      require "views/404.php";
    } else if ($statusCode == 503) {
      require "views/503.php";
    } else if ($statusCode == 0) {
      require "views/000.php";
    } else if ($statusCode == 900) {
      // Unavailable for Regulatory Reasons
      require "views/900.php";
    } else if ($statusCode == 901) {
      // Unavailable due to GDPR
      require "views/901.php";
    } else if ($statusCode == 902) {
      // Unavailable due to no GC API Key
      require "views/902.php";
    } else if ($statusCode == 999) {
      // Show last report 500 error
      require 'views/LastResort500.php';
    } else {
      require "views/500.php";
    }
  } catch (Exception | Error $e) {
    // Show emergency plain error page
    ob_get_clean();

    require 'views/LastResort500.php';
  }

  if ($throwException) {
    throw new \SCDS\HaltException('Status ' . $statusCode);
  }
}

$db = null;
try {
  $db = new PDO("mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4", env('DB_USER'), env('DB_PASS'));
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  halt(500);
}

$path = realpath(BASE_PATH . '../');
$headInfo = explode(' ', file_get_contents($path . '/.git/HEAD'));
if ($headInfo[0] == 'ref:') {
  $HEAD_hash = file_get_contents($path . '/.git/' . trim($headInfo[1]));
  define('SOFTWARE_VERSION', $HEAD_hash);
}

require_once "database.php";

if (!isset($_SESSION['PWA']) && isset($_COOKIE[COOKIE_PREFIX . 'PWA'])) {
  $_SESSION['PWA'] = $_COOKIE[COOKIE_PREFIX . 'PWA'];
}

if (!isset($_SESSION['Browser'])) {
  $browser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

  $_SESSION['Browser']['Name'] = $browser->browser->name;
  $_SESSION['Browser']['OS'] = $browser->os->toString();
  $_SESSION['Browser']['Version'] = $browser->browser->version->value;
}

// Make db available
$db = null;
try {
  $db = new PDO("mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME') . ";charset=utf8mb4", env('DB_USER'), env('DB_PASS'));
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  halt(500);
}

app()->db = $db;

// System info stuff
// $systemInfo = new \SystemInfo($db);
// app()->system = $systemInfo;

// Load vars
// include BASE_PATH . 'includes/GetVars.php';

// User login if required and make user var available

$route->use(function () {
  // Make req available
  $req = app('request');

  header("Feature-Policy: fullscreen 'self' https://youtube.com");
  header("Referrer-Policy: strict-origin-when-cross-origin");
  header("Content-Security-Policy: block-all-mixed-content");
  // Prevent framing of the membership system
  header("X-Frame-Options: DENY");
  // Prevent MIME sniffing
  header("X-Content-Type-Options: nosniff");
});

$route->group('/{club}:int', function ($club) {

  if ($club) {
    // Get the club
    $clubObject = Tenant::fromId($club);

    if (!$clubObject) {
      $this->any(['/', '/*'], function ($club) {
        include 'views/root/errors/no-club.php';
      });
    } else {
      app()->club = $club;
      app()->tenant = $clubObject;

      include BASE_PATH . 'routes/club/routes.php';
    }
  }
});

$route->group('/{club}:([a-z]{4})', function ($club) {

  if ($club) {
    // Get the club
    $clubObject = Tenant::fromCode($club);

    if (!$clubObject) {
      $this->any(['/', '/*'], function ($club) {
        include 'views/root/errors/no-club.php';
      });
    } else {
      app()->club = $club;
      app()->tenant = $clubObject;

      include BASE_PATH . 'routes/club/routes.php';
    }
  }
});

$route->group('/', function () {
  include 'routes/root/routes.php';
});


try {
  ob_get_clean();
  $route->end();
  echo ob_get_clean();
} catch (\SCDS\HaltException $e) {
  // Do nothing, just stops execution
} catch (\SCDS\CSRFValidityException $e) {
  // Deals with any uncaught CSRF problems
  ob_get_clean();
  halt(403, false);
} catch (Exception $e) {
  // This catches any uncaught exceptions.
  ob_get_clean();
  halt(500, false);
} catch (Error $e) {
  // This catches any fatal or recoverable errors.
  // ob_get_clean();
  // halt(500, false);
  pre($e);
} finally {
  // Any actions which must always happen at end
}

// Close SQL Database Connections
$db = null;
