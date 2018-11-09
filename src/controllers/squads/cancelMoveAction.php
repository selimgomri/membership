<?php

global $db;
use Respect\Validation\Validator as v;

if (!v::intVal()->validate($id)) {
	halt(404);
}

$id = mysqli_real_escape_string($link, $id);
$sql = "DELETE FROM `moves` WHERE `MoveID` = '$id';";

$sqlx = "SELECT `MemberID` FROM `moves` WHERE `MoveID` = ?";
$member = $db->prepare($sqlx);
$member->execute([$id]);
$member = $member->fetchColumn();

// Notify the parent
$sqlx = "INSERT INTO `notify` (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, ?, ?)";
$notify_query = $db->prepare($sqlx);

$sqlx = "SELECT `SquadName`, `MForename`, `MSurname`, `SquadFee`, `SquadTimetable`, `users`.`UserID` FROM (((`members` INNER JOIN `users` ON users.UserID = members.UserID) INNER JOIN `moves` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON moves.SquadID = squads.SquadID) WHERE members.MemberID = ?";
$email_info = $db->prepare($sqlx);
$email_info->execute([$member]);
$email_info = $email_info->fetch(PDO::FETCH_ASSOC);

if ($email_info) {
	$swimmer = htmlspecialchars($email_info['MForename'] . ' ' . $email_info['MSurname']);
	$parent = $email_info['UserID'];
	$squad = htmlspecialchars($email_info['SquadName']);
	$squad_fee = number_format($email_info['SquadFee'], 2, '.', ',');

	$subject = "Squad Move For " . $swimmer . " Cancelled";
	$message = '<p>The squad move for ' . $swimmer . ' to ' . $squad . ' Squad has been cancelled.</p>';
	$message .= '<p>Kind Regards,<br>The ' . CLUB_NAME . ' Team</p>';

	try {
		$notify_query->execute([
			$parent,
			'Queued',
			$subject,
			$message,
			1,
			'SquadMove'
		]);
	} catch (Exception $e) {
		halt(500);
	}
}

if (mysqli_query($link, $sql)) {
	header("Location: " . autoUrl("squads/moves"));
} else {
	halt(500);
}
