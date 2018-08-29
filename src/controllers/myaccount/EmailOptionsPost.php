<?

use Respect\Validation\Validator as v;
global $db;

$sql = "SELECT `EmailAddress`, `EmailComms` FROM `users` WHERE `UserID` = ?";
try {
	$query = $db->prepare($sql);
	$query->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
	halt(500);
}
$row = $query->fetch(PDO::FETCH_ASSOC);

$email_comms = 0;
$email_comms_update = false;
if ($_POST['EmailComms']) {
	$email_comms = 1;
}

if ($email_comms != $row['EmailComms']) {
	$email_comms_update = true;
}

$sql = "UPDATE `users` SET `EmailComms` = ? WHERE `UserID` = ?";
try {
	$db->prepare($sql)->execute([$email_comms, $_SESSION['UserID']]);
} catch (Exception $e) {
	halt(500);
}

if ($_POST['EmailAddress'] != $row['EmailAddress']) {
	$authCode = hash('sha256', random_bytes(64) . time());
  $sql = "INSERT INTO `newUsers` (`AuthCode`, `UserJSON`, `Type`) VALUES (?, ?,
  'EmailUpdate');";
}

header("Location: " . app('request')->curl);
