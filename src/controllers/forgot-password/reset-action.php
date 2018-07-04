<?php

use Respect\Validation\Validator as v;

$token = mysqli_real_escape_string($link, $token);

$sql = "SELECT `UserID` FROM `passwordTokens` WHERE `Token` = '$token';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$user = $row['UserID'];
	$sql = "SELECT `Token` FROM `passwordTokens` WHERE `UserID` = '$user' ORDER BY `TokenID` DESC LIMIT 1;";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$latestToken = $row['Token'];
	if ($token == $latestToken) {
		if ((isset($_POST['password1']) && isset($_POST['password2'])) && ($_POST['password1'] == $_POST['password2']) && v::stringType()->length(8, null)->validate($_POST['password1'])) {
			// Set the password
			$newHash = password_hash($_POST['password1'], PASSWORD_BCRYPT);
      $sql = "UPDATE `users` SET `Password` = '$newHash' WHERE `UserID` = '$user';";
      mysqli_query($link, $sql);

			// Remove token from db
			$sql = "DELETE FROM `passwordTokens` WHERE `UserID` = '$user';";
      mysqli_query($link, $sql);

			// Display success
			include BASE_PATH . 'views/header.php';
			?>
			<div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-6 col-md-5 col-lg4">
            <div class="alert alert-success">
              <strong>We've reset your Password</strong>
              <p class="mb-2">You can now, <a href="<? echo autoUrl(""); ?>" class="alert-link">login with your new password</a>.</p>
              <p class="mb-0">Thank you for using this service.</p>
            </div>
          </div>
        </div>
      </div>
			<?php
			include BASE_PATH . 'views/footer.php';
		}
		else {
			// Return as password error
			include BASE_PATH . 'views/header.php';
			?>
			<div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-6 col-md-5 col-lg4">
            <div class="alert alert-danger">
              <strong>You failed to supply both passwords, the passwords did not match or the password was not 8 characters or more</strong>
              <p class="mb-2">Please, <a href="<? echo autoUrl("resetpassword/auth/" . htmlspecialchars($token)); ?>" class="alert-link">try again</a>.</p>
              <p class="mb-0">We're sorry for any inconvenience caused.</p>
            </div>
          </div>
        </div>
      </div>
			<?php
			include BASE_PATH . 'views/footer.php';
		}
	} else {
		halt(404);
	}
}
else {
	halt(404);
}
