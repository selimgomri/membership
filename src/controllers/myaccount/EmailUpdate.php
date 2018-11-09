<?

global $db;

$sql = "SELECT * FROM `newUsers` WHERE `AuthCode` = ? AND `ID` = ? AND `Type` = ?";
try {
	$query = $db->prepare($sql);
	$query->execute([$auth, $id, 'EmailUpdate']);
	$id = $db->lastInsertId();
} catch (PDOException $e) {
	halt(500);
}

$rows = $query->fetchAll(PDO::FETCH_ASSOC);
$row = $rows[0];

$found = false;
if ($rows) {
	$found = true;
}

if ($found) {

	$array = json_decode($row['UserJSON']);

	//pre($array);

	$user 					= $array->User;
	$oldEmail 			= $array->OldEmail;
	$newEmail 			= $array->NewEmail;

	$sql = "UPDATE `users` SET `EmailAddress` = ? WHERE `UserID` = ?";
	try {
		$db->prepare($sql)->execute([$newEmail, $user]);
	} catch (PDOException $e) {
		halt(500);
	}

	$subject = "Your Email Address has been Changed";
	$message = '
	<p>Your ' . CLUB_NAME . ' Account Email Address has been changed from ' . $oldEmail . ' to ' . $newEmail . '.</p>
	<p>If this was you then you, then please ignore this email. If it was not you, please head to ' . autoUrl("") . ' and reset your password urgently.</p>
	<p>Kind Regards, <br>The ' . CLUB_NAME . ' Team</p>
	';
	$to = "";
	$name = getUserName($user);
	$from = [
		"Email" => "support@chesterlestreetasc.co.uk",
		"Name" => CLUB_NAME . " Security"
	];
	notifySend($to, $subject, $message, $name, $oldEmail, $from);

	$_SESSION['RegistrationGoVerify'] = '
  <div class="alert alert-success mb-0">
    <p class="mb-0">
      <strong>
        Hello ' . $name . '! You\'ve successfully verified your new email address.
      </strong>
    </p>

    <p class="mb-0">
      We\'ve now updated it for you.
    </p>
  </div>
  ';

	$sql = "DELETE FROM `newUsers` WHERE `AuthCode` = ? AND `ID` = ?;";
	try {
		$db->prepare($sql)->execute([$authCode, $id]);
	} catch (PDOException $e) {
		halt(500);
	}

} else {

	$_SESSION['RegistrationGoVerify'] = '
	<div class="alert alert-warning mb-0">
    <p class="mb-0">
      <strong>
        We could not find your details.
      </strong>
    </p>

    <p>
      This error may occur if your email provider has already inspected this link. Try logging in to see if this is the case.
    </p>

		<p class="mb-0">
			<a class="alert-link" href="' . autoUrl("") . '">
      	Login to your account
			</a>
    </p>
  </div>
	';
}

header("Location: " . autoUrl("/x"));
