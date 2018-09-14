<?php

ignore_user_abort(true);
set_time_limit(0);

$sql = "SELECT * FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE `Status` = 'Queued' LIMIT 8;";
$result = mysqli_query($link, $sql);

for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$emailid = $row['EmailID'];
	if (isSubscribed($row['UserID'], $row['EmailType']) || $row['ForceSend'] == 1) {
		//$to = $row['EmailAddress'];
    $to = $row['Forename'] . " " . $row['Surname'] . " <" . $row['EmailAddress'] . ">";
		$name = $row['Forename'] . " " . $row['Surname'];
		$emailaddress = $row['EmailAddress'];
		$subject = $row['Subject'];
		$message = "<p class=\"small\">Hello " . $row['Forename'] . " " . $row['Surname'] . ",</p>" . $row['Message'];

		$message = str_replace("\r\n", "", $message);

		$from = [
			"Email" => "notify@chesterlestreetasc.co.uk",
			"Name" => "Chester-le-Street ASC",
			"Unsub" => [
				"Allowed" => true,
				"User" => $row['UserID'],
				"List" =>	"Notify"
			]
		];

    if ($row['EmailType'] == 'Payments') {
			$from = [
				"Email" => "payments@chesterlestreetasc.co.uk",
				"Name" => "CLS ASC Payments",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Payments"
				]
			];
		} else if ($row['EmailType'] == 'Galas') {
			$from = [
				"Email" => "galas@chesterlestreetasc.co.uk",
				"Name" => "Chester-le-Street ASC Galas"
			];
		} else if ($row['EmailType'] == 'Security') {
			$from = [
				"Email" => "support@chesterlestreetasc.co.uk",
				"Name" => "Chester-le-Street ASC Security",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"Security"
				]
			];
		} else if ($row['EmailType'] == 'NewMember') {
			$from = [
				"Email" => "membership-updates@chesterlestreetasc.co.uk",
				"Name" => "Chester-le-Street ASC",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'APIAlert') {
			$from = [
				"Email" => "alerts@api.account.chesterlestreetasc.co.uk",
				"Name" => "CLS ASC API Alerts",
				"Unsub" => [
					"Allowed" => true,
					"User" => $row['UserID'],
					"List" =>	"NewMember"
				]
			];
		} else if ($row['EmailType'] == 'StaffBulletin') {
			$from = [
				"Email" => "team@staff.service.chesterlestreetasc.co.uk",
				"Name" => "CLS ASC Staff"
			];
		}

		if ($row['ForceSend'] == 1) {
			$from = [
				"Email" => "noreply@chesterlestreetasc.co.uk",
				"Name" => "Chester-le-Street ASC"
			];

      if ($from['Unsub']['Allowed']) {
        unset($from['Unsub']);
      }
		}

		if ($row['EmailType'] == 'SquadMove') {
			$from = [
				"Email" => "squad-moves@swimmers.service.chesterlestreetasc.co.uk",
				"Name" => "Chester-le-Street ASC"
			];
		}

		if (notifySend($to, $subject, $message, $name, $emailaddress, $from)) {
			$sql = "UPDATE `notify` SET `Status` = 'Sent' WHERE `EmailID` = '$emailid';";
			mysqli_query($link, $sql);
		} else {
      $sql = "UPDATE `notify` SET `Status` = 'Failed' WHERE `EmailID` = '$emailid';";
  		mysqli_query($link, $sql);
    }
	} else {
		$sql = "UPDATE `notify` SET `Status` = 'No_Sub' WHERE `EmailID` = '$emailid';";
		mysqli_query($link, $sql);
	}
}
