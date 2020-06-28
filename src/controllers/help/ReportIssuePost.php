<?php

$currentUser = app()->user;
use Respect\Validation\Validator as v;

$target = $_POST['report_url'];
$usr_message = htmlspecialchars($_POST['Message']);

$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorReportStatus'] = false;

if (v::url()->validate($target)) {

	$message = "<p>An error has been reported on the following page: " . $target . ".</p>";
	if ($currentUser) {
		$message .= "<p>The user was " . htmlspecialchars($currentUser->getFirstName() . ' ' . $currentUser->getLastName()) . ", " . htmlspecialchars($currentUser->getEmail()) . ".</p>";
	}
	$message .= "<p>The user said: " . $usr_message . "</p>";
	$message .= "<p>Reported on " . date("l j F Y") . ".</p>";
	$message .= "<p>Sent Automatically by Swimming Club Data Systems.</p>";

	notifySend("", "Website Error Report", $message, "Website Admin Team", "web@chesterlestreetasc.co.uk", ["Email" => "report-an-issue@" . getenv('EMAIL_DOMAIN'), "Name" => "Error Reports at " . app()->tenant->getKey('CLUB_NAME')]);

	$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorReportStatus'] = true;
	$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorReportTarget'] = $target;

}

header("Location: " . autoUrl("reportanissue"));
