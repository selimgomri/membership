<?php
define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_PATH', __DIR__ . DS, TRUE);
//Show errors
//===================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//===================================

require BASE_PATH.'vendor/autoload.php';
require "config.php";
require "helperclasses/Database.php";
//$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//define("LINK", mysqli_connect($dbhost, $dbuser, $dbpass, $dbname), true);
//$link = LINK;

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//$database = new Database($dbhost, $dbuser, $dbpass, $dbname);
//$database->connect();
//$link = $database->getConnection();

require_once "database.php";

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

// Home
$route->get('/', function() {
  global $link;
	if (empty($_SESSION['LoggedIn']) || empty($_SESSION['Username'])) {
  	require('controllers/login.php');
	} else {
		require('controllers/dashboard.php');
	}
});

$route->post('/', function() {
  global $link;
	include 'controllers/login-go.php';
});

// Register
$route->get('/register', function() {
  global $link;
  require('controllers/register.php');
});

// Locked Out Password Reset
$route->get('/resetpassword', function() {
  global $link;
  require('controllers/forgot-password.php');
});

$route->post('/resetpassword', function() {
  global $link;
  require('controllers/forgot-password-action.php');
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

$route->end();
