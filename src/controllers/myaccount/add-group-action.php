<?php

global $db;
use Respect\Validation\Validator as v;

$sql = "SELECT * FROM `familyIdentifiers` WHERE `ID` = ? AND `ACS` = ?";

$fid = trim(str_replace(["FAM", "fam"], "", $_POST['fam']));
$acs = trim($_POST['sec']);

try {
	$query = $db->prepare($sql);
	$query->execute([$fid, $acs]);
} catch (PDOException $e) {
	halt(500);
}

$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
	// Incorrect Details, Try Again
	$_SESSION['ErrorState'] = [
		'FAM' => $fid,
		'ACS' => $acs
	];
	header("Location: " . app('request')->curl);
} else  {
	$sql = "UPDATE `members` INNER JOIN familyMembers ON members.MemberID = familyMembers.MemberID SET members.UserID = ? WHERE familyMembers.FamilyID = ?";

	try {
  	$query = $db->prepare($sql);
  	$query->execute([$_SESSION['UserID'], $row['ID']]);
  } catch (PDOException $e) {
  	halt(500);
  }

	$_SESSION['Success'] = true;

	header("Location: " . app('request')->curl);
}
