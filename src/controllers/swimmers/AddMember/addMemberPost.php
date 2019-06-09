<?php

global $db;

$added = $action = false;

$forename = $middlenames = $surname = $dateOfBirth = $asaNumber = $sex = $squad = $cat = $cp = $sql = "";
$getASA = false;

if ((!empty($_POST['forename'])) && (!empty($_POST['surname'])) && (!empty($_POST['datebirth'])) && (!empty($_POST['sex'])) && (!empty($_POST['squad']))) {
  $forename = trim(ucwords($_POST['forename']));
	$surname = trim(ucwords($_POST['surname']));
	$dateOfBirth = trim($_POST['datebirth']);
	$sex = $_POST['sex'];
	$squad = $_POST['squad'];
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
	$cat = $_POST['cat'];
	if ($cat != 1 && $cat != 2 && $cat != 3) {
		halt(500);
	}
	if ($_POST['clubpays'] == 1) {
		$cp = 1;
	} else {
		$cp = 0;
	}

	$accessKey = generateRandomString(6);

  try {
    $insert = $db->prepare("INSERT INTO `members` (MForename, MMiddleNames, MSurname, DateOfBirth, ASANumber, Gender, SquadID, AccessKey, ASACategory, ClubPays, OtherNotes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([
      $forename,
      $middlenames,
      $surname,
      $dateOfBirth,
      $asaNumber,
      $sex,
      $squad,
      $accessKey,
      $cat,
      $cp,
      ""
    ]);

  	$last_id = $db->lastInsertId();

  	if ($getASA) {
  		$swimEnglandTemp = env('ASA_CLUB_CODE') . $last_id;
      $addTempSwimEnglandCode = $db->prepare("UPDATE `members` SET `ASANumber` = ? WHERE `MemberID` = ?");
      $addTempSwimEnglandCode->execute([$swimEnglandTemp, $last_id]);
  	}

    $action = true;

    try {
  		$getAdmins = $db->prepare("SELECT `UserID` FROM `users` WHERE `AccessLevel` = ? AND `UserID` != ?");
  		$getAdmins->execute(["Admin", $_SESSION['UserID']]);
  		$notify = $db->prepare("INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'NewMember')");
    	$subject = "New Club Member";
    	$message = '<p>' . htmlentities(getUserName($_SESSION['UserID'])) . ' has added a new member, ' . htmlentities($forename . ' ' . $surname) . ' to our online membership system.</p><p>We have sent you this email to ensure you\'re aware of this.</p>';
    	while ($row = $getAdmins->fetch(PDO::FETCH_ASSOC)) {
    		try {
    			$notify->execute([$row['UserID'], $subject, $message]);
    		} catch (PDOException $e) {
    			//halt(500);
    		}
    	}
    } catch (PDOException $e) {
  		halt(500);
  	}

  } catch (Exception $e) {
    $action = false;
  }
}

if ($action) {
	header("Location: " . autoUrl("swimmers/parenthelp/" . $last_id));
} else {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>We were not able to add the new swimmer</strong>
			Please try again
		</p>
	</div>';
	//header("Location: " . currentUrl());
}
