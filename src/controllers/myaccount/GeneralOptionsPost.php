<?

use Respect\Validation\Validator as v;
global $db;

$twofa = false;
if (filter_var(getUserOption($_SESSION['UserID'], "Is2FA"), FILTER_VALIDATE_BOOLEAN)) {
	$twofa = true;
}

if ($_POST['2FA'] == "1") {
  setUserOption($_SESSION['UserID'], "Is2FA", "1");
} else {
  setUserOption($_SESSION['UserID'], "Is2FA", "0");
}

if ($twofa != ($_POST['2FA'] == "1")) {
  $_SESSION['OptionsUpdate'] = true;
}

header("Location: " . app('request')->curl);
