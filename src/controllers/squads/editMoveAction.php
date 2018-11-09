<?php

global $db;
use Respect\Validation\Validator as v;

$errorState = false;
$errorMessage = "";

$id = mysqli_real_escape_string($link, $id);
$newSquad = mysqli_real_escape_string($link, $_POST['newSquad']);
$movingDate = mysqli_real_escape_string($link, $_POST['movingDate']);

if (!v::intVal()->validate($newSquad) || $newSquad == 0) {
	$errorState = true;
	$errorMessage .= "<li>A new squad was not supplied</li>";
}

if ($movingDate == "" || !v::date()->validate($movingDate)) {
	$errorState = true;
	$errorMessage .= "<li>A moving date was not supplied or was malformed</li>";
}

if (!$errorState) {
	$sql = "UPDATE `moves` SET `SquadID` = '$newSquad', `MovingDate` = '$movingDate' WHERE `MoveID` = '$id';";

	if (mysqli_query($link, $sql)) {
		$sql = "SELECT `MemberID` FROM `moves` WHERE `MoveID` = ?";
		$member = $db->prepare($sql);
		$member->execute([$id]);
		$member = $member->fetchColumn();

		// Notify the parent
		$sql = "INSERT INTO `notify` (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, ?, ?)";
		$notify_query = $db->prepare($sql);

		$sql = "SELECT `SquadName`, `MForename`, `MSurname`, `SquadFee`, `SquadTimetable`, `users`.`UserID` FROM (((`members` INNER JOIN `users` ON users.UserID = members.UserID) INNER JOIN `moves` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON moves.SquadID = squads.SquadID) WHERE members.MemberID = ?";
		$email_info = $db->prepare($sql);
		$email_info->execute([$member]);
		$email_info = $email_info->fetch(PDO::FETCH_ASSOC);

		if ($email_info) {
			$swimmer = htmlspecialchars($email_info['MForename'] . ' ' . $email_info['MSurname']);
			$parent = $email_info['UserID'];
			$squad = htmlspecialchars($email_info['SquadName']);
			$squad_fee = number_format($email_info['SquadFee'], 2, '.', ',');

			$subject = "Squad Move Update";
			$message = '<p>There has been a change to the squad move for ' . $swimmer . '. They will be moving to ' . $squad . ' Squad on ' . date("l j F Y", strtotime($movingDate)) . '.</p>';
			$message .= '<p>The Squad Fee you will pay will be &pound;' . $squad_fee . '.</p>';
			//$message .= '<p>As you pay by Direct Debit, you won\'t need to take any action. We\'ll automatically update your monthly fees.</p>';
			$message .= '<p>You can get the <a href="' . $email_info['SquadTimetable'] . '" target="_blank">timetable for ' . $squad . ' Squad on our website</a>.</p>';
			$message .= '<hr><p>If you do not think ' . $swimmer . ' will be able to take up their place in ' . $squad . ' Squad, please reply to this email as soon as possible. We must however warn you that we may not be able keep ' . $swimmer . ' in their current squad if it would prevent us from moving up swimmers in our lower squads.</p>';
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

		header("Location: " . autoUrl("squads/moves"));
	} else {
		$errorState = true;
		$errorMessage .= '<li>A database error occured.</li>';
	}
}


if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	<strong>An error occured and we could not edit the squad move</strong>
	<ul class="mb-0">' . $errorMessage . '
	</ul></div>';

	header("Location: " . autoUrl("squads/moves/edit/" . $id));
}
