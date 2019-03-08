<?php

if (filter_var(getUserOption($_SESSION['UserID'], "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {

  setUserOption($_SESSION['UserID'], "hasGoogleAuth2FA", false);
  setUserOption($_SESSION['UserID'], "GoogleAuth2FASecret", null);
  header("Location: " . autoUrl("myaccount/googleauthenticator"));

} else {
  halt(404);
}
