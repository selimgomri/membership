<?php

global $db;

try {
  if ($_SESSION['AddNotifyCC']['AuthCode'] = $_POST['auth']) {
    $user = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE UserID = ?");
    $user->execute([$_SESSION['UserID']]);

    $insert = $db->prepare("INSERT INTO notifyAdditionalEmails (UserID, Name, EmailAddress) VALUES (?, ?, ?)");
    $insert->execute([$_SESSION['UserID'], $_SESSION['AddNotifyCC']['Name'], $_SESSION['AddNotifyCC']['EmailAddress']]);

    if ($details = $user->fetch(PDO::FETCH_ASSOC)) {
      $message = '
      <p>We\'ve linked a new CC Address for this account.</p>
      <p>This means when emails are sent to all parents via our Notify service, ' . $_SESSION['AddNotifyCC']['EmailAddress'] . ' will also receive a copy of those emails.</p>
      <p>You can add more linked Email Addresses if you wish.</p>
      <p>If you did not request this, please visit <a href="' . autoUrl("my-account/email") . '">Email Options</a> in My Account.</p>';

      notifySend(null, "New linked email address set up", $message, $details['Forename'] . ' ' . $details['Surname'], $details['EmailAddress']);
    }
    $_SESSION['AddNotifySuccess'] = true;
  } else {
    // Invalid Code
    $_SESSION['AddNotifyError'] = true;
  }
} catch (Exception $e) {
  // Error
  $_SESSION['AddNotifyError'] = true;
}

unset($_SESSION['AddNotifyCC']);

header("Location: " . autoUrl("my-account/email"));
