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

// My Account
$route->get('/myaccount', function() {
  require('controllers/myaccount/index.php');
});

$route->end();
