<?php

$db = app()->db;

$getUser = $db->prepare("SELECT UserID FROM passwordTokens INNER JOIN users ON users.UserID = passwordTokens.UserID WHERE Token = ? AND users.Tenant = ? ORDER BY TokenID DESC LIMIT 1");
$getUser->execute([
	$token,
	$tenant->getId()
]);

if ($user = $getUser->fetchColumn()) {
	// Present the reset form
	include BASE_PATH . 'views/header.php';
	?>
	<div class="container">
		<h1>Reset Your Password</h1>
		<form method="post" class="needs-validation" novalidate>
      <div class="row">
        <div class="col-sm-6 col-md-8">
    			<div class="form-group">
    		    <label for="password">Enter new password</label>
    		    <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" id="password" name="password" aria-describedby="pwHelp" placeholder="Password" required minlength="8">
    				<small id="pwHelp" class="form-text text-muted">
							You must provide password that is at least 8 characters long with at least one lowercase letter, at least one uppercase letter and at least one number
						</small>
            <div class="invalid-feedback">
							You must provide password that is at least 8 characters long with at least one lowercase letter, at least one uppercase letter and at least one number
						</div>
    		  </div>
    			<div class="form-group">
    		    <label for="confirm-password">Confirm your new password</label>
    		    <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required minlength="8" aria-describedby="pwConfirmHelp">
						<small id="pwConfirmHelp" class="form-text text-muted">Repeat your password</small>
            <div class="invalid-feedback">
							You must provide password that is at least 8 characters long with at least one lowercase letter, at least one uppercase letter and at least one number
						</div>
    		  </div>
					<p>
	    			<button class="btn btn-success" type="submit">
							Save password
						</button>
					</p>
        </div>
      </div>
		</form>
	</div>
	<?php
	$footer = new \SCDS\Footer();
	$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
} else {
	halt(404);
}
