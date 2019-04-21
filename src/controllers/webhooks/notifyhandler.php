<?php

$club_address = "";
$club = json_decode(CLUB_JSON);
for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
$club_address .= $club->ClubAddress[$i] . "\r\n";
}

$head = "
<!DOCTYPE html>
<html lang=\"en-gb\">
<head>
  <meta charset=\"utf-8\">
  <link href=\"https://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">
  <style type=\"text/css\">
    html, body {
      font-family: \"Open Sans\", -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial,sans-serif;
      font-size: 1rem;
      background: #e3eef6;
    }
    p, h1, h2, h3, h4, h5, h6, ul, ol, img, .table, blockquote {
      margin: 0 0 1rem 0;
    }
    .small {
      font-size: 0.70rem;
      color: #868e96;
      margin-bottom: 0.70rem;
    }
    .text-center {
      text-align: center;
    }
    .bottom {
      margin: 1rem 0 0 0;
    }
  </style>
</head>
<body>
<div style=\"background:#e3eef6;\">
  <table style=\"width:100%;border:0px;text-align:left;padding:10px 0px 10px 0px;background:#e3eef6;\"><tr><td align=\"center\">
    <table style=\"width:100%;max-width:700px;border:0px;text-align:center;background:#ffffff;padding:10px 10px 0px 10px;\"><tr><td>
    <img src=\"" . autoUrl("img/notify/NotifyLogo.png") . "\"
    style=\"width:300px;max-width:100%;\" srcset=\"" .
    autoUrl("img/notify/NotifyLogo@2x.png") . " 2x, " .
    autoUrl("img/notify/NotifyLogo@3x.png") . " 3x\" alt=\"" . CLUB_NAME . " Logo\"></td></tr></table>
    <table style=\"width:100%;max-width:700px;border:0px;text-align:left;background:#ffffff;padding:0px 10px;\"><tr><td>
";


ignore_user_abort(true);
set_time_limit(0);

global $db;
$getExtraEmails = $db->prepare("SELECT Name, EmailAddress, ID FROM notifyAdditionalEmails WHERE UserID = ?");

$getPendingGroupMail = $db->query("SELECT ID, notifyHistory.Subject, notifyHistory.Message, notifyHistory.ForceSend FROM notifyHistory INNER JOIN notify ON notifyHistory.ID = notify.MessageID WHERE Status = 'Queued' GROUP BY ID LIMIT 8");

$getUsersForEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, notify.UserID, EmailID FROM notify INNER JOIN users ON notify.UserID = users.UserID WHERE MessageID = ?");

// Completed It PDO Object
$completed = $db->prepare("UPDATE `notify` SET `Status` = ? WHERE `EmailID` = ?");

while ($currentMessage = $getPendingGroupMail->fetch(PDO::FETCH_ASSOC)) {
  $getUsersForEmail->execute([$currentMessage['ID']]);

  $db->beginTransaction();

  $message = $head . '<p class="small text-muted">Hello -name-, </p>' . $currentMessage['Message'] . "
      </td></tr></table>
      <table style=\"width:100%;max-width:700px;border:0px;background:#f8fcff;padding:0px 10px;\"><tr><td>
      <div
  class=\"bottom text-center\">
  <p class=\"small\" align=\"center\"><strong>" . CLUB_NAME . "</strong><br>";
  $club = json_decode(CLUB_JSON);
  for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
  $message .= $club->ClubAddress[$i] . "<br>";
  }
  $message .= "</p>
  <p class=\"small\" align=\"center\">This email was sent automatically by the " . CLUB_NAME . " Membership System.</p>";
  if (!defined('IS_CLS') || !IS_CLS) {
  $message .= '<p class="small" align="center">The Membership System was built by Chester-le-Street ASC.</p>';
  }
  $message .= "<p class=\"small\" align=\"center\">Have questions? Contact us at <a
  href=\"mailto:" . $club->ClubEmails->Main . "\">" . $club->ClubEmails->Main . "</a>.</p>
  <p class=\"small\" align=\"center\">To control your email options, go to <a href=\"" .
  autoUrl("myaccount/email") . "\">My Account</a>.</p>";
  if (!$currentMessage['ForceSend']) {
    $message .= '<p class="small" align="center"><a href="-unsub_link-">Click to Unsubscribe</a></p>';
  }
  $message .= "
  <p class=\"small\" align=\"center\">&copy; " . CLUB_NAME . " " . date("Y") . "</p>
      </div>
      </table>
    </table>
    </div>
    </body>
    </html>";

  $message = str_replace("\r\n", "", $message);

  $from = new \SendGrid\Mail\From("notify@chesterlestreetasc.co.uk", CLUB_NAME);
  if ($currentMessage['ForceSend']) {
    $from = new \SendGrid\Mail\From("noreply@chesterlestreetasc.co.uk", CLUB_NAME);
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
      $completed->execute(['NoSub', $user['EmailID']]);
    }
  }
  $subject = $currentMessage['Subject'];
  $globalSubstitutions = [];
  $plain_text =
    "Hello -name-, " . str_replace("&pound;", "£", str_replace("&copy;", "©",
    strip_tags(str_replace(["</h1>", "</h2>", "</h3>", "</h4>", "</h5>",
    "</h6>", "</p>", "</li>"], "\r\n", $currentMessage['Message'])))) . "\r\n\r\n" . CLUB_NAME . "\r\n" . $club_address . "\r\nClick to Unsubscribe\r\n-unsub_link-\r\n\r\n© " . CLUB_NAME . " " . date("Y");
  //$plain_text = str_replace(';', '', $plain_text);
  $plainTextContent = new \SendGrid\Mail\PlainTextContent($plain_text);
  $htmlContent = new \SendGrid\Mail\HtmlContent($message);

  $email = new \SendGrid\Mail\Mail(
    $from,
    $tos,
    $subject,
    $plainTextContent,
    $htmlContent,
    $globalSubstitutions
  );

  $email->setReplyTo(CLUB_EMAIL, CLUB_NAME . ' Enquiries');

  $sendgrid = new \SendGrid(SENDGRID_API_KEY);
  try {
    $response = $sendgrid->send($email);
    $db->commit();
  } catch (Exception $e) {
    $db->rollback();
  }

}
