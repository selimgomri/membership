<?php

global $db;

$sql = "SELECT `EmailAddress`, `EmailComms` FROM `users` WHERE `UserID` = ?";
try {
	$query = $db->prepare($sql);
	$query->execute([$_SESSION['UserID']]);
} catch (Exception $e) {
	halt(500);
}

$row = $query->fetch(PDO::FETCH_ASSOC);
//$current_email = $query->fetchColumn();

$emailChecked;
if ($row['EmailComms']) {
	$emailChecked = " checked ";
}

$pagetitle = "Email Options";
include BASE_PATH . "views/header.php";
  $userID = $_SESSION['UserID'];
?>
<div class="container">
  <h1>Manage Email Options</h1>
  <p class="lead">Manage your email address and email options.</p>

	<div class="my-3 p-3 bg-white rounded shadow">
		<form method="post">
			<div class="form-group">
		    <label for="EmailAddress">Your Email address</label>
		    <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" placeholder="name@example.com" value="<?=htmlentities($row['EmailAddress'])?>">
		  </div>
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" value="1" id="EmailComms" aria-describedby="EmailCommsHelp" name="EmailComms" <?php echo $emailChecked; ?> >
					<label class="custom-control-label" for="EmailComms">Receive Squad Updates by Email</label>
					<small id="EmailCommsHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
				</div>
			</div>
			<p class="mb-0">
				<button type="submit" class="btn btn-secondary">Update Details</button>
			</p>
		</form>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
