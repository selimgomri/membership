<?

use Respect\Validation\Validator as v;
global $db;

$twofa = false;
if (filter_var(getUserOption($_SESSION['UserID'], "Is2FA"), FILTER_VALIDATE_BOOLEAN)) {
	$twofa = true;
}

$trackers = true;
if (filter_var(getUserOption($_SESSION['UserID'], "DisableTrackers"), FILTER_VALIDATE_BOOLEAN)) {
	$trackers = false;
}

$betas = true;
if (filter_var(getUserOption($_SESSION['UserID'], "EnableBeta"), FILTER_VALIDATE_BOOLEAN)) {
	$betas = false;
}

if ($_POST['2FA'] == "1") {
  setUserOption($_SESSION['UserID'], "Is2FA", "1");
} else {
  setUserOption($_SESSION['UserID'], "Is2FA", "0");
}

if ($_POST['beta-features'] == "1") {
  setUserOption($_SESSION['UserID'], "EnableBeta", "1");
} else {
  setUserOption($_SESSION['UserID'], "EnableBeta", "0");
}

if ($_POST['tracking-cookies'] == "1") {
  setUserOption($_SESSION['UserID'], "DisableTrackers", "1");
} else {
  setUserOption($_SESSION['UserID'], "DisableTrackers", "0");
}

$_SESSION['DisableTrackers'] = filter_var(getUserOption($_SESSION['UserID'], "DisableTrackers"), FILTER_VALIDATE_BOOLEAN);

if ($twofa != ($_POST['2FA'] == "1")) {
  $_SESSION['OptionsUpdate'] = true;
}

if ($trackers != ($_POST['tracking-cookies'] == "1")) {
  $_SESSION['OptionsUpdate'] = true;
}

header("Location: " . app('request')->curl);
