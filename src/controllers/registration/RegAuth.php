<?

$id = mysqli_real_escape_string($link, $id);
$auth = mysqli_real_escape_string($link, $token);

$sql = "SELECT * FROM `newUsers` WHERE `AuthCode` = '$auth' AND `ID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$array = json_decode($row['UserJSON']);

pre($array);

$username 			= $array->Username;
$forename	 			= $array->Forename;
$surname 				= $array->Surname;
$email 					= $array->EmailAddress;
$emailAuth 			= $array->EmailComms;
$mobile 				= $array->Mobile;
$smsAuth 				= $array->MobileComms;
$hashedPassword	= $array->Password;

// Registration may be allowed
// Success
$sql = "INSERT INTO `users`
(`UserID`, `Username`, `Password`, `AccessLevel`, `EmailAddress`, `EmailComms`, `Forename`, `Surname`, `Mobile`, `MobileComms`)
VALUES
(NULL, '$username', '$hashedPassword', 'Parent', '$email', '$emailAuth', '$forename', '$surname', '$mobile', '$smsAuth');";
mysqli_query($link, $sql);
// Check it went in
$query = "SELECT * FROM users WHERE Username = '$username' AND Password = '$hashedPassword' LIMIT 0, 30 ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);
$count = mysqli_num_rows($result);

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

	header("Location: " . autoUrl("register"));
}
