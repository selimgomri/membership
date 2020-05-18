<?php

$emailPrefix = '';
if (!app()->tenant->isCLS()) {
	$emailPrefix = mb_strtolower(trim(app()->tenant->getKey('ASA_CLUB_CODE'))) . '-';
}

ignore_user_abort(true);
set_time_limit(0);

$db = app()->db;
$getExtraEmails = $db->prepare("SELECT Name, EmailAddress FROM notifyAdditionalEmails WHERE UserID = ?");

$pending = $db->query("SELECT `EmailID`, `notify`.`UserID`, `EmailType`, `notify`.`ForceSend`, `Forename`, `Surname`, `EmailAddress`, notify.Subject AS PlainSub, notify.Message AS PlainMess FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE notify.MessageID IS NULL AND `Status` = 'Queued' LIMIT 8;");

// Completed It PDO Object
$completed = $db->prepare("UPDATE `notify` SET `Status` = ? WHERE `EmailID` = ?");

while ($row = $pending->fetch(PDO::FETCH_ASSOC)) {
	$emailid = $row['EmailID'];
	if (isSubscribed($row['UserID'], $row['EmailType']) || $row['ForceSend'] == 1) {
    $getExtraEmails->execute([$row['UserID']]);

		//$to = $row['EmailAddress'];
    $to = $row['Forename'] . " " . $row['Surname'] . " <" . $row['EmailAddress'] . ">";
		$name = $row['Forename'] . " " . $row['Surname'];
		$emailaddress = $row['EmailAddress'];
		$subject = $row['PlainSub'] . $row['NotifySub'];
		$message = "<p class=\"small\">Hello " . $row['Forename'] . " " . $row['Surname'] . ",</p>" . $row['PlainMess'] . $row['NotifyMess'];

		$message = str_replace("\r\n", "", $message);

		$from = [
			"Email" => "noreply@" . env('EMAIL_DOMAIN'),
			"Name" => app()->tenant->getKey('CLUB_NAME'),
			"Unsub" => [
				"Allowed" => true,
				"User" => $row['UserID'],
				"List" =>	"Notify"
			]
		];

    if ($row['EmailType'] == 'Payments') {
			$from = [
				"Email" => $emailPrefix . "payments@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME'),
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Payments"
				]
			];
		} else if ($row['EmailType'] == 'Galas') {
			$from = [
				"Email" => $emailPrefix . "galas@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME') . " Galas"
			];
		} else if ($row['EmailType'] == 'Security') {
			$from = [
				"Email" => $emailPrefix . "security-support@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME') . " Security",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Security"
				]
			];
		} else if ($row['EmailType'] == 'NewMember') {
			$from = [
				"Email" => $emailPrefix . "membership-updates@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME'),
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'APIAlert') {
			$from = [
				"Email" => $emailPrefix . "membership-system-api-alerts@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_SHORT_NAME') . " API Alerts",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'StaffBulletin') {
			$from = [
				"Email" => $emailPrefix . "team-notify@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_SHORT_NAME') . " Staff"
			];
		}

		if ($row['ForceSend'] == 1) {
			$from = [
				"Email" => $emailPrefix . "noreply@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME')
			];

      if ($from['Unsub']['Allowed']) {
        unset($from['Unsub']);
      }
		}

		if ($row['EmailType'] == 'SquadMove') {
			$from = [
				"Email" => $emailPrefix . "squad-moves@" . env('EMAIL_DOMAIN'),
				"Name" => app()->tenant->getKey('CLUB_NAME')
			];
		} else if ($row['EmailType'] == 'Notify-Audit') {
      $from = [
				"Email" => $emailPrefix . "gdpr-notify@" . env('EMAIL_DOMAIN'),
				"Name" => "SCDS GDPR Compliance"
			];
    }

    if ($row['EmailType'] == "Notify") {
      $ccEmails = [];
      while ($extraEmails = $getExtraEmails->fetch(PDO::FETCH_ASSOC)) {
        $ccEmails[$extraEmails['EmailAddress']] = $extraEmails['Name'];
      }
      $from['CC'] = $ccEmails;
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
