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
  	require('login.php');
	} else {
		require('dashboard.php');
	}
});

$route->post('/', function() {
	echo pre(app('request')->server); //$_SERVER
	echo "<br>";
	echo app('request')->path; // uri path
	echo "<br>";
	echo app('request')->hostname;
	echo "<br>";
	echo app('request')->servername;
	echo "<br>";
	echo app('request')->port;
	echo "<br>";
	echo app('request')->protocol; // http or https
	echo "<br>";
	echo app('request')->url; // domain url
	echo "<br>";
	echo app('request')->curl; // current url
	echo "<br>";
	echo app('request')->extension; // get url extension
	echo "<br>";
	echo pre(app('request')->headers); // all http headers
	echo "<br>";
	echo app('request')->method; // Request method
	echo "<br>";
	echo pre(app('request')->query); // $_GET
	echo "<br>";
	echo pre(app('request')->body); // $_POST and php://input
	echo "<br>";
	echo pre(app('request')->args); // all route args
	echo "<br>";
	echo pre(app('request')->files); // $_FILES
	echo "<br>";
	echo pre(app('request')->cookies); // $_COOKIE
	echo "<br>";
	echo app('request')->ajax; // check if request is sent by ajax or not
	echo "<br>";
	echo app('request')->ip(); // get client IP
	echo "<br>";
	echo app('request')->browser(); // get client browser
	echo "<br>";
	echo app('request')->platform(); // get client platform
	echo "<br>";
	echo app('request')->isMobile(); // check if client opened from mobile or tablet
	echo "<br>";

	echo $_POST['username'];
	include 'login-go.php';
});

// Register
$route->get('/register', function() {
  require('register.php');
});

// Locked Out Password Reset
$route->get('/resetpassword', function() {
  require('forgot-password.php');
});

$route->post('/resetpassword', function() {
  require('forgot-password-action.php');
});

$route->end();
