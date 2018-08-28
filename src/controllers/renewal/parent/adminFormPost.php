<?

$userID = mysqli_real_escape_string($link, $_SESSION['UserID']);
$partial_reg = isPartialRegistration();

$id = [];

$status = true;
$statusMessage = "";

// Verify Data Consent
if ($_POST['data-agree'] != 1) {
	$status = false;
	$statusMessage .= "<li>You did not give your consent to our use of your
	data</li>";
}

$sql;
if ($partial_reg) {
	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth FROM `members` WHERE `UserID` = '$userID' AND members.RR =
	1 ORDER BY `MForename` ASC, `MSurname` ASC;";
} else {
	$sql = "SELECT members.MemberID, members.MForename, members.MSurname,
	members.DateOfBirth FROM `members` WHERE `UserID` = '$userID' ORDER BY
	`MForename` ASC, `MSurname` ASC;";
}
$result = mysqli_query($link, $sql);

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$id[$i] = $row['MemberID'];
	$name[$i] = $row['MForename'] . " " . $row['MSurname'];
	$age[$i] = date_diff(date_create($row['DateOfBirth']),
	date_create('today'))->y;
}

// Verify that all swimmers have agreed to Ts and Cs of Membership
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	if ($_POST[$id[$i] . '-tc-confirm'] != 1) {
		$status = false;
		$statusMessage .= "<li>" . $name[$i] . " did not agree to the Terms and
		Conditions of Membership</li>";
	}

	if ($age[$i] < 12) {
		if ($_POST[$id[$i] . '-pg-understanding'] != 1) {
			$status = false;
			$statusMessage .= "<li>You did not state that you had explained the Terms
			and Conditions of Membership to " . $name[$i] . "</li>";
		}
	}
}

// Add or Update Photography Permissions
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	if ($age[$i] < 18) {
		if (isset($_POST[$id[$i] . '-photo-web']) || isset($_POST[$id[$i] .
		'-photo-soc']) || isset($_POST[$id[$i] . '-photo-nb']) ||
		isset($_POST[$id[$i] . '-photo-film']) || isset($_POST[$id[$i] .
		'-photo-pro'])) {
	    setupPhotoPermissions($id[$i]);
	  }
	  // Web Photo Permissions
	  $photo[0] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-web']) || $_POST[$id[$i] . '-photo-web'] !=
	  1) {
	    $photo[0] = 0;
	  }
	  $sql = "UPDATE `memberPhotography` SET `Website` = '$photo[0]' WHERE `MemberID` =
	  '$id[$i]';";
	  mysqli_query($link, $sql);

	  // Social Media Photo Permissions
	  $photo[1] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-soc']) || $_POST[$id[$i] . '-photo-soc'] !=
	  1) {
	    $photo[1] = 0;
	  }
	  $sql = "UPDATE `memberPhotography` SET `Social` = '$photo[1]' WHERE `MemberID` =
	  '$id[$i]';";
	  mysqli_query($link, $sql);

	  // Notice Board Photo Permissions
	  $photo[2] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-nb']) || $_POST[$id[$i] . '-photo-nb'] != 1) {
	    $photo[2] = 0;
	  }
	  $sql = "UPDATE `memberPhotography` SET `Noticeboard` = '$photo[2]' WHERE
	  `MemberID` = '$id[$i]';";
	  mysqli_query($link, $sql);

	  // Filming in Training Permissions
	  $photo[3] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-film']) || $_POST[$id[$i] . '-photo-film'] != 1) {
	    $photo[3] = 0;
	  }
	  $sql = "UPDATE `memberPhotography` SET `FilmTraining` = '$photo[3]' WHERE
	  `MemberID` = '$id[$i]';";
	  mysqli_query($link, $sql);

	  // Pro Photographer Photo Permissions
	  $photo[4] = 1;
	  if (!isset($_POST[$id[$i] . '-photo-pro']) || $_POST[$id[$i] . '-photo-pro'] !=
	  1) {
	    $photo[4] = 0;
	  }
	  $sql = "UPDATE `memberPhotography` SET `ProPhoto` = '$photo[4]' WHERE
	  `MemberID` = '$id[$i]';";
	  mysqli_query($link, $sql);
	}
}

// Verify Medical Permissions
for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	if ($age[$i] < 18) {
		if ($_POST[$id[$i] . '-med'] != 1) {
			$status = false;
			$statusMessage .= "<li>You did not complete the medical declaration for " .
			$name[$i] . ". You cannot continue without doing this</li>";
		}
	}
}

if ($status) {
	// Update the database with current renewal state
	$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
	$renewal = mysqli_real_escape_string($link, $renewal);

	$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1 WHERE
	`RenewalID` = '$renewal' AND `UserID` = '$user';";
	mysqli_query($link, $sql);
	header("Location: " . app('request')->curl);
} else {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>There was a problem with the information you submitted</strong>
	<ul class=\"mb-0\">" . $statusMessage . "</ul>
	<p class=\"mb-0\">Please try again. You cannot renew your membership or
	register if you cannot agree to the terms and conditions on this
	page.</p></div>";
	header("Location: " . app('request')->curl);
}
