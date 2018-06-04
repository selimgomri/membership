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
//$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
define("LINK", mysqli_connect($dbhost, $dbuser, $dbpass, $dbname), true);
$link = LINK;

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

require_once "database.php";

$app            = System\App::instance();
$app->request   = System\Request::instance();
$app->route     = System\Route::instance($app->request);

$route          = $app->route;

// Home
$route->get('/', function() {
	if (empty($_SESSION['LoggedIn']) || empty($_SESSION['Username'])) {
  	require('controllers/login.php');
	} else {
		require('controllers/dashboard.php');
	}
});

$route->post('/', function() {
	include 'login-go.php';
});

// Register
$route->get('/register', function() {
  require('controllers/register.php');
});

// Locked Out Password Reset
$route->get('/resetpassword', function() {
  require('controllers/forgot-password.php');
});

$route->post('/resetpassword', function() {
  require('controllers/forgot-password-action.php');
});

$route->group('/myaccount', function() {

	// My Account
	$this->get('/', function() {
	  require('controllers/myaccount/index.php');
	});

	// Manage Password
	$this->get('/password', function() {
	  require('controllers/myaccount/change-password.php');
	});

	$this->post('/password', function() {
	  require('controllers/myaccount/change-password-action.php');
	});

	// Add swimmer
	$this->get('/addswimmer', function() {
	  require('controllers/myaccount/add-swimmer.php');
	});

	$this->post('/addswimmer', function() {
	  require('controllers/myaccount/add-swimmer-action.php');
	});

});

$route->end();
