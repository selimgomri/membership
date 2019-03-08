<?

use Respect\Validation\Validator as v;

$target = $_POST['report_url'];
$usr_message = htmlspecialchars($_POST['Message']);

$_SESSION['ErrorReportStatus'] = false;

if (v::url()->validate($target)) {

	$message = "<p>An error has been reported on the following page: " . $target . ".</p>";
	$message .= "<p>The user said: " . $usr_message . "</p>";
	$message .= "<p>Reported on " . date("l j F Y") . ".</p>";
	$message .= "<p>Sent Automatically by CLS ASC.</p>";

	notifySend("", "Website Error Report", $message, "Website Admin Team", "web@chesterlestreetasc.co.uk", ["Email" => "issues@web.service.chesterlestreetasc.co.uk", "Name" => "Chester-le-Street ASC"]);

	$_SESSION['ErrorReportStatus'] = true;
	$_SESSION['ErrorReportTarget'] = $target;

}

header("Location: " . autoUrl("reportanissue"));
