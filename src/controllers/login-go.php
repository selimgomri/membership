<?php

use GeoIp2\Database\Reader;

global $db;

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

  $getUser = $db->prepare("SELECT Forename, Surname, UserID, EmailAddress, Password, AccessLevel FROM users WHERE EmailAddress = ?");
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
      $do_random_2FA = random_int(0, 99) < 5 || filter_var(getUserOption($userID, "IsSpotCheck2FA"), FILTER_VALIDATE_BOOLEAN);
      if ($row['AccessLevel'] != "Parent" || filter_var(getUserOption($userID, "Is2FA"), FILTER_VALIDATE_BOOLEAN) || $do_random_2FA) {
        // Do 2FA
        if (filter_var(getUserOption($userID, "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {
          $_SESSION['TWO_FACTOR_GOOGLE'] = true;
        } else {
          $code = random_int(100000, 999999);

          if ($do_random_2FA) {
            setUserOption($userID, "IsSpotCheck2FA", true);
          }

          $message = '
          <p>Hello. Confirm your login by entering the following code in your web browser.</p>
          <p><strong>' . $code . '</strong></p>
          <p>If you did not just try to log in, you can ignore this email. You may want to reset your password.</p>
          <p>Kind Regards,<br>The ' . env('CLUB_NAME') . ' Team</p>';

          if (notifySend(null, "Verification Code", $message, $forename . " " . $surname, $email)) {
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
        } catch (Exception $e) {
          halt(403);
        }

        unset($_SESSION['LoginSec']);
      }
    } else {
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
if ($_SESSION['ErrorState'] === true && $_POST['target'] == "") {
  header("Location: " . autoUrl("login"));
} else {
  header("Location: " . autoUrl(ltrim($_POST['target'], '/')));
}
