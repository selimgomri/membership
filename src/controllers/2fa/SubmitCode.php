<?php

global $db;

$resetFailedLoginCount = $db->prepare("UPDATE users SET WrongPassCount = 0 WHERE UserID = ?");

use GeoIp2\Database\Reader;

$security_status = false;

use PragmaRX\Google2FA\Google2FA;
$ga2fa = new Google2FA();

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

$auth_via_google_authenticator;
try {
  $auth_via_google_authenticator = $_SESSION['TWO_FACTOR_GOOGLE'] && $ga2fa->verifyKey(getUserOption($_SESSION['2FAUserID'], "GoogleAuth2FASecret"), $_POST['auth']);
} catch (Exception $e) {
  $auth_via_google_authenticator = false;
}

if (($_POST['auth'] == $_SESSION['TWO_FACTOR_CODE']) || $auth_via_google_authenticator && $security_status) {
  unset($_SESSION['TWO_FACTOR']);
  unset($_SESSION['TWO_FACTOR_CODE']);
  unset($_SESSION['TWO_FACTOR_GOOGLE']);

  if ($auth_via_google_authenticator) {
    // Do work to prevent replay attacks etc.
  }

  try {
    $login = new \CLSASC\Membership\Login($db);
    $login->setUser($_SESSION['2FAUserID']);
    if ($_SESSION['2FAUserRememberMe']) {
      $login->stayLoggedIn();
    }
    global $currentUser;
    $currentUser = $login->login();
    $resetFailedLoginCount->execute([$_SESSION['2FAUserID']]);
  } catch (Exception $e) {
    pre($e);
    exit();
    // halt(403);
  }
} else {
  $_SESSION['ErrorState'] = true;
  if ($security_status == false) {
    $_SESSION['ErrorState'] = true;
    $_SESSION['ErrorStateLSVMessage'] = "We were unable to verify the integrity of your login attempt. The site you entered your username and password on may have been attempting to capture your login details. Try reseting your password urgently.";
    $_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
  }
}

if (isset($_SESSION['UserID']) && bool(getUserOption($_SESSION['UserID'], "IsSpotCheck2FA"))) {
  setUserOption($_SESSION['UserID'], "IsSpotCheck2FA", false);
}

unset($_SESSION['LoginSec']);
$target = ltrim($_POST['target'], '/');
if ($target == null || $target == "") {
  $target = "";
}
header("Location: " . autoUrl($target));
