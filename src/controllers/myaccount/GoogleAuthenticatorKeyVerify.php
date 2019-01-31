<?php

$setup = false;
$secretKey = null;

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();
$success = $google2fa->verifyKey($_SESSION['G2FAKey'], $_POST['verify']);

if ($success) {
  setUserOption($_SESSION['UserID'], "hasGoogleAuth2FA", true);
  setUserOption($_SESSION['UserID'], "GoogleAuth2FASecret", $_SESSION['G2FAKey']);
  unset($_SESSION['G2FAKey']);
  header("Location: " . autoUrl("myaccount/googleauthenticator"));
} else {
  $_SESSION['G2FA_VerifyError'] = true;
  header("Location: " . app('request')->curl);
}
