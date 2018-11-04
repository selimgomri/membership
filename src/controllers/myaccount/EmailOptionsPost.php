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

// Normal Emails
$email_comms = false;
$email_comms_update = false;
if ($_POST['EmailComms']) {
	$email_comms = true;
}

if ($email_comms != $row['EmailComms']) {
	$email_comms_update = true;
	$_SESSION['OptionsUpdate'] = true;
  $sql = "UPDATE `users` SET `EmailComms` = ? WHERE `UserID` = ?";
  try {
  	$db->prepare($sql)->execute([$email_comms, $_SESSION['UserID']]);
  } catch (Exception $e) {
  	halt(500);
  }
}

updateSubscription($_POST['SecurityComms'], 'Security');
updateSubscription($_POST['PaymentComms'], 'Payments');
if ($_SESSION['AccessLevel'] == "Admin") {
	updateSubscription($_POST['NewMemberComms'], 'NewMember');
}

if ($_POST['EmailAddress'] != $row['EmailAddress']) {
	if (v::email()->validate($_POST['EmailAddress'])) {
		$authCode = hash('sha256', random_bytes(64) . time());

		$user_details = [
			'User'		   => $_SESSION['UserID'],
			'OldEmail'   => $row['EmailAddress'],
			'NewEmail'	 => $_POST['EmailAddress']
		];
		$user_details = json_encode($user_details);

	  $sql = 'INSERT INTO `newUsers` (`AuthCode`, `UserJSON`, `Type`) VALUES (?, ?, ?)';
		try {
			$db->prepare($sql)->execute([$authCode, $user_details, 'EmailUpdate']);
		} catch (Exception $e) {
			halt(500);
		}
		$id = $db->lastInsertId();

		$name = getUserName($_SESSION['UserID']);

		$verifyLink = "email/auth/" . $id . "/" . $authCode;
	  // PHP Email
	  $subject = "Confirm your new email address";
	  $to = $email;
	  $sContent = '<p class="small">Hello ' . $name . '</p>
	  <p>We\'ve received a request to update the email address associated with your account.</p>
	  <p>We need you to verify your email address by following this link - <a
	  href="' . autoUrl($verifyLink) . '" target="_blank">' .
	  autoUrl($verifyLink) . '</a></p>
	  <p>You will need to use your email address, ' . $email . ' to sign in.</p>
	  <p>If you did not make a change to your email address, please ignore this email and consider reseting your password.</p>
	  <p>For help, send an email to <a
	  href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>/</p>
	  ';
	  notifySend($to, $subject, $sContent, $name, $_POST['EmailAddress'], ["Email" => "support@chesterlestreetasc.co.uk", "Name" => "Chester-le-Street ASC Security"]);
		$_SESSION['EmailUpdate'] = true;
		$_SESSION['EmailUpdateNew'] = $_POST['EmailAddress'];
	} else {
		$_SESSION['EmailUpdate'] = false;
	}
}

header("Location: " . app('request')->curl);
