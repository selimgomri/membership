<?php

use GeoIp2\Database\Reader;

global $db;

$incrementFailedLoginCount = $db->prepare("UPDATE users SET WrongPassCount = WrongPassCount + 1 WHERE UserID = ?");
$resetFailedLoginCount = $db->prepare("UPDATE users SET WrongPassCount = 0 WHERE UserID = ?");

$errorMessage = "";
$errorState = false;
$target = "";

$security_status = false;

if ($_POST['SessionSecurity'] == session_id()) {
  $security_status = true;
} else {
  $security_status = false;
}

if (SCDS\CSRF::verify()) {
  $security_status = true;
} else {
  $security_status = false;
}

if ((!empty($_POST['email-address']) && !empty($_POST['password'])) && ($security_status)) {
  // Let the user login
  $username = trim(mb_strtolower($_POST['email-address']));
  $target = ltrim(trim($_POST['target']), '/');

  $getUser = $db->prepare("SELECT Forename, Surname, UserID, EmailAddress, Password, AccessLevel, WrongPassCount FROM users WHERE EmailAddress = ?");
  $getUser->execute([$_POST['email-address']]);

  $row = $getUser->fetch(PDO::FETCH_ASSOC);

  if ($row != null) {
    //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $hash = $row['Password'];
    $email = $row['EmailAddress'];
    $forename = $row['Forename'];
    $surname = $row['Surname'];
    $userID = $row['UserID'];

    if (password_verify($_POST['password'], $hash)) {
      $do_random_2FA = random_int(0, 99) < 5 || bool(getUserOption($userID, "IsSpotCheck2FA")) || $row['WrongPassCount'] > 2;
      if ($row['AccessLevel'] != "Parent" || bool(getUserOption($userID, "Is2FA")) || $do_random_2FA) {
        // Do 2FA
        if (bool(getUserOption($userID, "hasGoogleAuth2FA"))) {
          $_SESSION['TWO_FACTOR_GOOGLE'] = true;
        } else {
          $code = random_int(100000, 999999);

          if ($do_random_2FA && !($row['AccessLevel'] != "Parent" || bool(getUserOption($userID, "Is2FA")))) {
            setUserOption($userID, "IsSpotCheck2FA", true);
          }

          $message = '
          <p>Hello. Confirm your login by entering the following code in your web browser.</p>
          <p><strong>' . $code . '</strong></p>
          <p>If you did not just try to log in, you can ignore this email. You may want to reset your password.</p>
          <p>Kind Regards, <br>The ' . env('CLUB_NAME') . ' Team</p>';

          $date = new DateTime('now', new DateTimeZone('Europe/London'));

          if (notifySend(null, "Verification Code - Requested at " . $date->format("H:i:s \o\\n d/m/Y"), $message, $forename . " " . $surname, $email)) {
            $_SESSION['TWO_FACTOR_CODE'] = $code;
          } else {
            halt(500);
          }
        }
        $_SESSION['2FAUserID'] = $userID;
        if ($_POST['RememberMe']) {
          $_SESSION['2FAUserRememberMe'] = 1;
        }
        $_SESSION['TWO_FACTOR'] = true;
        header("Location: " . autoUrl("2fa"));
      } else {
        try {
          $login = new \CLSASC\Membership\Login($db);
          $login->setUser($userID);
          if ($_POST['RememberMe']) {
            $login->stayLoggedIn();
          }
          global $currentUser;
          $currentUser = $login->login();
          $resetFailedLoginCount->execute([$userID]);
        } catch (Exception $e) {
          halt(403);
        }

        unset($_SESSION['LoginSec']);
      }
    } else {
      // Incorrect PW
      // Don't notify user of error
      // Increment failed login count
      $incrementFailedLoginCount->execute([$userID]);

      // Set error state
      $_SESSION['ErrorState'] = true;
      $_SESSION['EnteredUsername'] = $username;
    }
  }
  else {
    $_SESSION['ErrorState'] = true;
    $_SESSION['EnteredUsername'] = $username;

  }
} else {
  if (!$security_status) {
    $_SESSION['ErrorState'] = true;
    $_SESSION['ErrorStateLSVMessage'] = "We were unable to verify the integrity of your login attempt. The site you entered your email address and password on may have been attempting to capture your login details. Try reseting your password urgently.";
    $_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
  }
}
$_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
unset($_SESSION['LoginSec']);
if (isset($_SESSION['ErrorState']) && $_SESSION['ErrorState'] && $_POST['target'] == "" || isset($_SESSION['ErrorAccountLocked']) && $_SESSION['ErrorAccountLocked'] && $_POST['target'] == "") {
  header("Location: " . autoUrl("login"));
} else {
  header("Location: " . autoUrl(ltrim($_POST['target'], '/')));
}
