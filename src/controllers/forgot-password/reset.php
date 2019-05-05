<?php

global $db;

$getUser = $db->prepare("SELECT UserID FROM passwordTokens WHERE Token = ? ORDER BY TokenID DESC LIMIT 1");
$getUser->execute([$token]);

if ($user = $getUser->fetchColumn()) {
	// Present the reset form
	include BASE_PATH . 'views/header.php';
	?>
	<div class="container">
		<h1>Reset Your Password</h1>
		<form class="mb-3" method="post">
			<div class="form-group">
		    <label for="password1">Enter new password</label>
		    <input type="password" class="form-control" id="password1" name="password1" aria-describedby="pwHelp" placeholder="Password">
				<small id="pwHelp" class="form-text text-muted">Passwords must be 8 characters or longer</small>
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
