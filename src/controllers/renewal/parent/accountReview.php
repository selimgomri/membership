<?php

global $db;

$userID = $_SESSION['UserID'];

$query = "SELECT * FROM users WHERE UserID = '$userID' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$email = $row['EmailAddress'];
$forename = $row['Forename'];
$surname = $row['Surname'];
$access = $row['AccessLevel'];
$userID = $row['UserID'];
$mobile = $row['Mobile'];
$emailComms = $row['EmailComms'];
$mobileComms = $row['MobileComms'];
if ($emailComms==1) {
	$emailChecked = " checked ";
}
if ($mobileComms==1) {
	$mobileChecked = " checked ";
}

$pagetitle = "Account Review";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<h1>Review your account</h1>
	<p class="lead">Check your details are still up to date</p>

	<? if (isset($_SESSION['ErrorState'])) {
		echo $_SESSION['ErrorState'];
		unset($_SESSION['ErrorState']);
	} ?>

	<form method="post">
		<div class="my-3 p-3 bg-white rounded shadow">
		  <h2>Your Details</h2>
		  <p class="border-bottom border-gray pb-2">What we know about you.</p>
	    <div class="form-group">
	        <label for="forename">Name</label>
	        <input type="text" class="form-control" name="forename" id="forename" placeholder="Forename" value="<?php echo $forename ?>">
	     </div>
	     <div class="form-group">
	        <label for="surname">Surname</label>
	        <input type="text" class="form-control" name="surname" id="surname" placeholder="Surname" value="<?php echo $surname ?>">
	    </div>
	     <div class="form-group">
	        <label for="email">Email</label>
	        <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" value="<?php echo $email ?>" disabled>
	    </div>
	    <div class="form-group">
	      <div class="custom-control custom-checkbox">
	        <input type="checkbox" class="custom-control-input" value="1" id="emailContactOK" aria-describedby="emailContactOKHelp" name="emailContactOK" <?php echo $emailChecked ?>>
	        <label class="custom-control-label" for="emailContactOK">Check this to receive news by email</label>
	        <small id="emailContactOKHelp" class="form-text text-muted">You'll still receive emails relating to your account if you don't receive news</small>
	      </div>
	    </div>
	    <div class="form-group">
	      <label for="mobile">Mobile Number</label>
	      <input type="tel" class="form-control" name="mobile" id="mobile" aria-describedby="mobileHelp" placeholder="Mobile Number" value="<?php echo $mobile ?>">
	      <small id="mobileHelp" class="form-text text-muted">If you don't have a mobile, use your landline number.</small>
	    </div>
	    <div class="form-group mb-0">
	      <div class="custom-control custom-checkbox">
	        <input type="checkbox" class="custom-control-input" value="1" id="smsContactOK" aria-describedby="smsContactOKHelp" name="smsContactOK" <?php echo $mobileChecked ?>>
	        <label class="custom-control-label" for="smsContactOK">Check this if you would like to receive text messages</label>
	        <small id="smsContactOKHelp" class="form-text text-muted">We'll still use this to contact you in an emergency</small>
	      </div>
	    </div>
	  </div>

		<div class="mb-3">
			<button type="submit" class="btn btn-success">Save and Continue</button>
		</div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
