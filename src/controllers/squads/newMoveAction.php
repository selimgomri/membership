<?php

global $db;
use Respect\Validation\Validator as v;

$errorState = false;
$errorMessage = "";

$newSquad = $_POST['newSquad'];
$movingDate = $_POST['movingDate'];

if (!v::intVal()->validate($newSquad) || $newSquad == 0) {
	$errorState = true;
	$errorMessage .= "<li>A new squad was not supplied</li>";
}

if ($movingDate == "" || !v::date()->validate($movingDate)) {
	$errorState = true;
	$errorMessage .= "<li>A moving date was not supplied or was malformed</li>";
}

if (strtotime($movingDate) < strtotime('+9 days')) {
	$errorState = true;
	$errorMessage .= "<li>10 days notice must be given before a squad move</li>";
}

if (!$errorState) {
  try {
    $insert = $db->prepare("INSERT INTO `moves` (`MemberID`, `SquadID`, `MovingDate`) VALUES (?, ?, ?)");
    $insert->execute([
      $id,
      $newSquad,
      $movingDate
    ]);

		// Notify the parent
		$sql = "INSERT INTO `notify` (`UserID`, `Status`, `Subject`, `Message`, `ForceSend`, `EmailType`) VALUES (?, ?, ?, ?, ?, ?)";
		$notify_query = $db->prepare($sql);

		$sql = "SELECT `SquadName`, `MForename`, `MSurname`, `SquadFee`, SquadCoC, `SquadTimetable`, `users`.`UserID` FROM (((`members` INNER JOIN `users` ON users.UserID = members.UserID) INNER JOIN `moves` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON moves.SquadID = squads.SquadID) WHERE members.MemberID = ?";
		$email_info = $db->prepare($sql);
		$email_info->execute([$id]);
		$email_info = $email_info->fetch(PDO::FETCH_ASSOC);

		if ($email_info) {
			$swimmer = htmlspecialchars($email_info['MForename'] . ' ' . $email_info['MSurname']);
			$parent = $email_info['UserID'];
			$squad = htmlspecialchars($email_info['SquadName']);
			$squad_fee = number_format($email_info['SquadFee'], 2, '.', ',');

			$subject = $swimmer . " is moving to " . $squad . " Squad";
			$message = '<p>We\'re very excited to let you know what ' . $swimmer . ' will be moving to ' . $squad . ' Squad on ' . date("l j F Y", strtotime($movingDate)) . '.</p>';
			$message .= '<p>The Squad Fee you will pay will be &pound;' . $squad_fee . '*.</p>';
			$message .= '<p>As you pay by Direct Debit, you won\'t need to take any action. We\'ll automatically update your monthly fees.</p>';
			if ($email_info['SquadTimetable'] != "" && $email_info['SquadTimetable'] != null) {
			  $message .= '<p>You can get the <a href="' . $email_info['SquadTimetable'] . '" target="_blank">timetable for ' . $squad . ' Squad on our website</a>.</p>';
      }
      if ($email_info['SquadCoC'] != "" && $email_info['SquadCoC'] != null) {
        $message .= '<p>The terms and conditions for ' . $squad . ' Squad are as follows;</p>';
        $message .= '<div class="cell">';
			  $message .= getPostContent($email_info['SquadCoC']);
        $message .= '</div>';
        $message .= '<p>You must abide by the above code of conduct if you take your place in this squad as per the Membership Terms and Conditions. This new code of conduct may be different to that for your current squad, so please read it carefully.</p>';
      }
			$message .= '<hr><p>If you do not think ' . $swimmer . ' will be able to take up their place in ' . $squad . ' Squad, please reply to this email as soon as possible. We must however warn you that we may not be able keep ' . $swimmer . ' in their current squad if it would prevent us from moving up swimmers in our lower squads.</p>';
			$message .= '<p>Kind Regards,<br>The ' . CLUB_NAME . ' Team</p>';
      $message .= '<p class="small text-muted">* Discounts may apply if you have multiple swimmers.</p>';

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

	} catch (Exception $e) {
		$errorState = true;
		$errorMessage .= '<li>A database error occured.</li>';
	}
}

if ($errorState) {
	$_SESSION['ErrorState'] = '
	<div class="alert alert-danger">
	<strong>An error occured and we could not add the squad move</strong>
	<ul class="mb-0">' . $errorMessage . '
	</ul></div>';

	header("Location: " . autoUrl("squads/moves/new/" . $id));
}
