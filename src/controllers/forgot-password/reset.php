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
		    <label for="password">Enter new password</label>
		    <input type="password" class="form-control" id="password" name="password" aria-describedby="pwHelp" placeholder="Password" required minlength="8">
				<small id="pwHelp" class="form-text text-muted">Passwords must be 8 characters or longer</small>
        <div class="invalid-feedback">
          Please enter a password with at least 8 characters
        </div>
		  </div>
			<div class="form-group">
		    <label for="confirm-password">Confirm your new password</label>
		    <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required minlength="8">
        <div class="invalid-feedback">
          Please enter a password with at least 8 characters
        </div>
		  </div>
			<button class="btn btn-dark" type="submit">Reset my password</button>
		</form>
	</div>
  <script src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>
	<?php
	include BASE_PATH . 'views/footer.php';
} else {
	halt(404);
}
