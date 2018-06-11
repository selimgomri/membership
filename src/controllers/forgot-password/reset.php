<?php

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
		// Present the reset form
		include BASE_PATH . 'views/header.php';
		?>
		<div class="container">
			<h1>Reset Your Password</h1>
			<form class="mb-3" method="post">
				<div class="form-group">
			    <label for="password1">Enter new password</label>
			    <input type="password" class="form-control" id="password1" name="password1" placeholder="Password">
			  </div>
				<div class="form-group">
			    <label for="password2">Confirm your new password</label>
			    <input type="password" class="form-control" id="password2" name="password2" placeholder="Confirm Password">
			  </div>
				<button class="btn btn-dark" type="submit">Reset my password</button>
			</form>
		</div>
		<?php
		include BASE_PATH . 'views/footer.php';
	} else {
		halt(404);
	}
}
else {
	halt(404);
}
