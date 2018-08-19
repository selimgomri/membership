<?

global $db;

$rr = 0;

$id = mysqli_real_escape_string($link, $id);
$auth = mysqli_real_escape_string($link, $token);

$sql = "SELECT * FROM `newUsers` WHERE `AuthCode` = '$auth' AND `ID` = '$id' AND `Type` = 'Registration';";
$result = mysqli_query($link, $sql);

$status = "nf";

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$array = json_decode($row['UserJSON']);

//pre($array);

$username 			= $array->Username;
$forename	 			= $array->Forename;
$surname 				= $array->Surname;
$email 					= $array->EmailAddress;
$emailAuth 			= $array->EmailComms;
$mobile 				= $array->Mobile;
$smsAuth 				= $array->MobileComms;
$hashedPassword	= $array->Password;

$fam = false;
$fam_id = null;
if (isset($array->FamilyIdentifier) || $array->FamilyIdentifier != null) {
	$fam = true;
	$fam_id = $array->FamilyIdentifier;
	if ($array->RequiresRegistraion) {
		//$rr = 1;
	}
}

// Registration may be allowed
// Success
$sql = "INSERT INTO `users`
(`UserID`, `Username`, `Password`, `AccessLevel`, `EmailAddress`, `EmailComms`, `Forename`, `Surname`, `Mobile`, `MobileComms`, `RR`)
VALUES
(NULL, '$username', '$hashedPassword', 'Parent', '$email', '$emailAuth', '$forename', '$surname', '$mobile', '$smsAuth', '$rr');";
mysqli_query($link, $sql);
$user_id = mysqli_insert_id($link);
// Check it went in
$query = "SELECT * FROM users WHERE Username = '$username' AND Password = '$hashedPassword' LIMIT 0, 30 ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);
$count = mysqli_num_rows($result);

if ($fam) {
	$sql = "UPDATE `members` INNER JOIN familyMembers ON members.MemberID = familyMembers.MemberID SET members.UserID = ? WHERE familyMembers.FamilyID = ?";

	try {
  	$query = $db->prepare($sql);
  	$query->execute([$user_id, $fam_id]);
  } catch (PDOException $e) {
  	halt(500);
  }
}

if ($count == 1) {
	$_SESSION['RegistrationGoVerify'] = '
  <div class="alert alert-success mb-0">
    <p class="mb-0">
      <strong>
        Hello ' . $forename . '! You\'ve successfully verified your email address.
      </strong>
    </p>

    <p>
      You can now head to the homepage to login.
    </p>

		<p class="mb-0">
			<a class="alert-link" href="' . autoUrl("") . '">
      	Login to your account
			</a>
    </p>
  </div>
  ';

	$sql = "DELETE FROM `newUsers` WHERE `AuthCode` = '$auth' AND `ID` = '$id';";
	mysqli_query($link, $sql);
} else if ($status == "nf") {
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

header("Location: " . autoUrl("register"));
