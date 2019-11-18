<?php

ignore_user_abort(true);
set_time_limit(0);

$emailPrefix = '';
if (!bool(env('IS_CLS'))) {
	$emailPrefix = mb_strtolower(trim(env('ASA_CLUB_CODE'))) . '-';
}

$club_address = "";
$club = json_decode(CLUB_JSON);
for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
$club_address .= $club->ClubAddress[$i] . "\r\n";
}

global $db;
$getExtraEmails = $db->prepare("SELECT Name, EmailAddress, ID FROM notifyAdditionalEmails WHERE UserID = ? AND Verified = '1'");

$getPendingGroupMail = $db->query("SELECT ID, notifyHistory.Subject, notifyHistory.Message, notifyHistory.ForceSend, notifyHistory.JSONData FROM notifyHistory INNER JOIN notify ON notifyHistory.ID = notify.MessageID WHERE Status = 'Queued' GROUP BY ID LIMIT 8");

$getUsersForEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, notify.UserID, EmailID FROM notify INNER JOIN users ON notify.UserID = users.UserID WHERE MessageID = ?");

// Completed It PDO Object
$completed = $db->prepare("UPDATE `notify` SET `Status` = ? WHERE `EmailID` = ?");

while ($currentMessage = $getPendingGroupMail->fetch(PDO::FETCH_ASSOC)) {
  $getUsersForEmail->execute([$currentMessage['ID']]);

  $jsonData = json_decode($currentMessage['JSONData']);

  $db->beginTransaction();

  $mailObject = new \CLSASC\SuperMailer\CreateMail();
  $mailObject->setHtmlContent($currentMessage['Message']);

  $mailObject->showName();
  if (!$currentMessage['ForceSend']) { 
    $mailObject->setUnsubscribable();
  }

  $from = new \SendGrid\Mail\From("notify@" . env('EMAIL_DOMAIN'), env('CLUB_NAME'));
  if ($currentMessage['ForceSend']) {
    $from = new \SendGrid\Mail\From("noreply@" . env('EMAIL_DOMAIN'), env('CLUB_NAME'));
  }
  if ($jsonData->NamedSender->Email != null && $jsonData->NamedSender->Name) {
    $from = new \SendGrid\Mail\From($emailPrefix . "noreply@" . env('EMAIL_DOMAIN'), $jsonData->NamedSender->Name);
  }
  $tos = [];
  while ($user = $getUsersForEmail->fetch(PDO::FETCH_ASSOC)) {
    if ($currentMessage['ForceSend'] || isSubscribed($user['UserID'], 'Notify')) {
      $tos[] = new \SendGrid\Mail\To(
        $user['EmailAddress'],
        $user['Forename'] . ' ' . $user['Surname'],
        [
          '-name-' => $user['Forename'] . ' ' . $user['Surname'],
          '-unsub_link-' => autoUrl("notify/unsubscribe/" . dechex($user['UserID']) .  "/" . urlencode($user['EmailAddress']) . "/Notify")
        ]
      );
      $getExtraEmails->execute([$user['UserID']]);
      while ($extraEmails = $getExtraEmails->fetch(PDO::FETCH_ASSOC)) {
        $tos[] = new \SendGrid\Mail\To(
          $extraEmails['EmailAddress'],
          $extraEmails['Name'],
          [
            '-name-' => $extraEmails['Name'],
            '-unsub_link-' => autoUrl("cc/" . dechex($extraEmails['ID']) .  "/" . hash('sha256', $extraEmails['ID']) . "/unsubscribe")
          ]
        );
        $ccEmails[$extraEmails['EmailAddress']] = $extraEmails['Name'];
      }
      $completed->execute(['Sent', $user['EmailID']]);
    } else {
      $completed->execute(['No_Sub', $user['EmailID']]);
    }
  }
  $subject = $currentMessage['Subject'];
  $globalSubstitutions = [];
  $plain_text = $mailObject->getFormattedPlain();
  //$plain_text = str_replace(';', '', $plain_text);
  $plainTextContent = new \SendGrid\Mail\PlainTextContent($plain_text);
  $htmlContent = new \SendGrid\Mail\HtmlContent($mailObject->getFormattedHtml());

  $email = new \SendGrid\Mail\Mail(
    $from,
    $tos,
    $subject,
    $plainTextContent,
    $htmlContent,
    $globalSubstitutions
  );

  if ($jsonData->ReplyToMe->Email != null && $jsonData->ReplyToMe->Name != null) {
    try {
      $email->setReplyTo($jsonData->ReplyToMe->Email, $jsonData->ReplyToMe->Name);
    } catch (Exception $e) {
      $email->setReplyTo(env('CLUB_EMAIL'), env('CLUB_NAME') . ' Enquiries');
    }
  } else {
    $email->setReplyTo(env('CLUB_EMAIL'), env('CLUB_NAME') . ' Enquiries');
  }

  $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
  try {
    $response = $sendgrid->send($email);
    $db->commit();
  } catch (Exception $e) {
    $db->rollback();
  }

}
