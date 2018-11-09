<?php

ignore_user_abort(true);
set_time_limit(0);

$sql = "SELECT `EmailID`, `notify`.`UserID`, `EmailType`, `notify`.`ForceSend`, `Forename`, `Surname`, `EmailAddress`, notify.Subject AS PlainSub, notify.Message AS PlainMess, notifyHistory.Subject AS NotifySub, notifyHistory.Message AS NotifyMess FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID LEFT JOIN notifyHistory ON notify.MessageID = notifyHistory.ID WHERE `Status` = 'Queued' LIMIT 8;";
$result = mysqli_query($link, $sql);

// Completed It PDO Object
$completed = $db->prepare("UPDATE `notify` SET `Status` = ? WHERE `EmailID` = ?");

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  pre($row);
	$emailid = $row['EmailID'];
	if (isSubscribed($row['UserID'], $row['EmailType']) || $row['ForceSend'] == 1) {
		//$to = $row['EmailAddress'];
    $to = $row['Forename'] . " " . $row['Surname'] . " <" . $row['EmailAddress'] . ">";
		$name = $row['Forename'] . " " . $row['Surname'];
		$emailaddress = $row['EmailAddress'];
		$subject = $row['PlainSub'] . $row['NotifySub'];
		$message = "<p class=\"small\">Hello " . $row['Forename'] . " " . $row['Surname'] . ",</p>" . $row['PlainMess'] . $row['NotifyMess'];

		$message = str_replace("\r\n", "", $message);

		$from = [
			"Email" => "notify@chesterlestreetasc.co.uk",
			"Name" => CLUB_NAME,
			"Unsub" => [
				"Allowed" => true,
				"User" => $row['UserID'],
				"List" =>	"Notify"
			]
		];

    if ($row['EmailType'] == 'Payments') {
			$from = [
				"Email" => "payments@chesterlestreetasc.co.uk",
				"Name" => CLUB_SHORT_NAME . " Payments",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Payments"
				]
			];
		} else if ($row['EmailType'] == 'Galas') {
			$from = [
				"Email" => "galas@chesterlestreetasc.co.uk",
				"Name" => CLUB_NAME . " Galas"
			];
		} else if ($row['EmailType'] == 'Security') {
			$from = [
				"Email" => "security-support@chesterlestreetasc.co.uk",
				"Name" => CLUB_NAME . " Security",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Security"
				]
			];
		} else if ($row['EmailType'] == 'NewMember') {
			$from = [
				"Email" => "membership-updates@chesterlestreetasc.co.uk",
				"Name" => CLUB_NAME,
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'APIAlert') {
			$from = [
				"Email" => "alerts@api.account.chesterlestreetasc.co.uk",
				"Name" => CLUB_SHORT_NAME . " API Alerts",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'StaffBulletin') {
			$from = [
				"Email" => "team@staff.service.chesterlestreetasc.co.uk",
				"Name" => CLUB_SHORT_NAME . " Staff"
			];
		}

		if ($row['ForceSend'] == 1) {
			$from = [
				"Email" => "noreply@chesterlestreetasc.co.uk",
				"Name" => CLUB_NAME
			];

      if ($from['Unsub']['Allowed']) {
        unset($from['Unsub']);
      }
		}

		if ($row['EmailType'] == 'SquadMove') {
			$from = [
				"Email" => "squad-moves@swimmers.service.chesterlestreetasc.co.uk",
				"Name" => CLUB_NAME
			];
		} else if ($row['EmailType'] == 'Notify-Audit') {
      $from = [
				"Email" => "gdpr@club-digital.service.chesterlestreetasc.co.uk",
				"Name" => "CLS ASC Club Digital Services"
			];
    }

		if (notifySend($to, $subject, $message, $name, $emailaddress, $from)) {
      $completed->execute(['Sent', $emailid]);
		} else {
      $completed->execute(['Failed', $emailid]);
    }
	} else {
		$completed->execute(['No_Sub', $emailid]);
	}
}
