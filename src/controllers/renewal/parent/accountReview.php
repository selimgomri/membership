<?php
$db = app()->db;

$userDetails = $db->prepare("SELECT * FROM users WHERE UserID = ?");
$userDetails->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$row = $userDetails->fetch(PDO::FETCH_ASSOC);

$email = $row['EmailAddress'];
$forename = $row['Forename'];
$surname = $row['Surname'];
$userID = $row['UserID'];
$mobile = $row['Mobile'];
if ($row['EmailComms']) {
	$emailChecked = " checked ";
}
if ($row['MobileComms']) {
	$mobileChecked = " checked ";
}

$pagetitle = "Account Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container-xl">
	<h1>Review your account</h1>
	<p class="lead">Check your details are still up to date</p>

	<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
		echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
		unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
	} ?>

	<form method="post" class="needs-validation" novalidate>
		<div class="">
			<h2>Your Details</h2>
			<div class="mb-3">
				<label class="form-label" for="forename">Name</label>
				<input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?= htmlspecialchars($forename) ?>" required>
				<div class="invalid-feedback">
					You must provide your name
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label" for="surname">Surname</label>
				<input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?= htmlspecialchars($surname) ?>" required>
				<div class="invalid-feedback">
					You must provide your surname
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label" for="email-input">Email</label>
				<input type="email" class="form-control" name="email" id="email-input" aria-describedby="email-help" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" disabled>
				<small id="email-help" class="form-text text-muted">You can only change your email address in account settings.</small>
			</div>
			<div class="mb-3">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?= $emailChecked ?>>
					<label class="form-check-label" for="emailContactOK">I would like to receive important news and messages from squad coaches by email</label>
					<small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label" for="mobile">Mobile Number</label>
				<input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?= htmlspecialchars($mobile) ?>" required>
				<small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
			</div>
			<div class="mb-3">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?= $mobileChecked ?>>
					<label class="form-check-label" for="smsContactOK">I would like to receive important text messages</label>
					<small id="smsContactOKHelp" class="form-text text-muted">We'll still use this to contact you in an emergency. Your club may not offer SMS services.</small>
				</div>
			</div>
		</div>

		<div class="mb-3">
			<button type="submit" class="btn btn-success">Save and Continue</button>
		</div>
	</form>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
