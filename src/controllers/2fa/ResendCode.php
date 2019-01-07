<?php

global $db;

try {
  $query = $db->prepare("SELECT EmailAddress, Forename, Surname FROM users WHERE UserID = ?");
  $query->execute([$_SESSION['2FAUserID']]);
  $row = $query->fetch(PDO::FETCH_ASSOC);

  $message = '
  <p>Hello. Confirm your login by entering the following code in your web browser.</p>
  <p><strong>' . $_SESSION['TWO_FACTOR_CODE'] . '</strong></p>
  <p>If you did not just try to log in, you can ignore this email. You may want to reset your password.</p>
  <p>This email was resent to this address at the request of the user.</p>
  <p>Kind Regards,<br>The ' . CLUB_NAME . ' Team</p>';

  if (notifySend(null, "Verification Code", $message, $row['Forename'] . " " . $row['Surname'], $row['EmailAddress'])) {
    $_SESSION['TWO_FACTOR'] = true;
    $_SESSION['TWO_FACTOR_RESEND'] = true;
    header("Location: " . autoUrl("2fa"));
  } else {
    halt(500);
  }
} catch (Exception $e) {
  halt(500);
}
