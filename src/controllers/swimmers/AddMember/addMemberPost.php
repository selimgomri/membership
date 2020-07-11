<?php

if (!SCDS\CSRF::verify()) {
	halt(403);
}

$db = app()->db;
$tenant = app()->tenant;

$added = $action = false;

$forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $cat = $cp = $sql = $transfer = "";
$getASA = false;

if ((!empty($_POST['forename'])) && (!empty($_POST['surname'])) && (!empty($_POST['datebirth'])) && (!empty($_POST['sex'])) && (!empty($_POST['squad']))) {
  $forename = trim(ucwords($_POST['forename']));
	$surname = trim(ucwords($_POST['surname']));
	$dateOfBirth = trim($_POST['datebirth']);
	$sex = $_POST['sex'];
	if ((!empty($_POST['middlenames']))) {
		$middlenames = trim(ucwords($_POST['middlenames']));
	}
	if ((!empty($_POST['asa']))) {
		$asaNumber = trim($_POST['asa']);
	} else {
		$getASA = true;
	}
	if ($asaNumber == "" || $asaNumber == null) {
		$getASA = true;
	}
	$cat = (int) $_POST['cat'];
	if ($cat != 0 && $cat != 1 && $cat != 2 && $cat != 3) {
		halt(500);
	}
	
	if (isset($_POST['clubpays']) && bool($_POST['clubpays'])) {
		$cp = 1;
	} else {
		$cp = 0;
	}

	if (isset($_POST['transfer']) && bool($_POST['transfer'])) {
		$transfer = 1;
	} else {
		$transfer = 0;
	}

	$accessKey = generateRandomString(6);

  try {
    $insert = $db->prepare("INSERT INTO `members` (MForename, MMiddleNames, MSurname, DateOfBirth, ASANumber, Gender, AccessKey, ASACategory, ClubPays, OtherNotes, RRTransfer, Tenant) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([
      $forename,
      $middlenames,
      $surname,
      $dateOfBirth,
      $asaNumber,
      $sex,
      $accessKey,
      $cat,
      $cp,
			"",
			$transfer,
			$tenant->getId(),
    ]);

		$last_id = $db->lastInsertId();
		
		// If squad, add to squad

  	if ($getASA) {
  		$swimEnglandTemp = app()->tenant->getKey('ASA_CLUB_CODE') . $last_id;
      $addTempSwimEnglandCode = $db->prepare("UPDATE `members` SET `ASANumber` = ? WHERE `MemberID` = ?");
      $addTempSwimEnglandCode->execute([$swimEnglandTemp, $last_id]);
  	}

    $action = true;

    try {
  		$getAdmins = $db->prepare("SELECT `UserID` FROM `users` INNER JOIN `permissions` ON users.UserID = `permissions`.`User` WHERE Tenant = ? AND `Permission` = 'Admin' AND `UserID` != ?");
  		$getAdmins->execute([
				$tenant->getId(),
				$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
  		$notify = $db->prepare("INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'NewMember')");
    	$subject = "New Club Member";
    	$message = '<p>' . htmlentities(getUserName($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) . ' has added a new member, ' . htmlentities($forename . ' ' . $surname) . ' to our online membership system.</p><p>We have sent you this email to ensure you\'re aware of this.</p>';
    	while ($row = $getAdmins->fetch(PDO::FETCH_ASSOC)) {
    		try {
    			$notify->execute([$row['UserID'], $subject, $message]);
    		} catch (PDOException $e) {
    			//halt(500);
    		}
    	}
    } catch (PDOException $e) {
  	}

  } catch (Exception $e) {
	  reportError($e);
    $action = false;
  }
}

if ($action) {
	$_SESSION['TENANT-' . app()->tenant->getId()]['SwimmerAdded'] = true;
	header("Location: " . autoUrl("members/" . $last_id));
} else {
	$_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>We were not able to add the new swimmer</strong>
			Please try again
		</p>
	</div>';
	header("Location: " . autoUrl("members/new"));
}
