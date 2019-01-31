<?php

use Respect\Validation\Validator as v;

$_SESSION['UserDetailsPostData'] = $_POST;

// Overwrite passwords so they aren't kept in the session file
$_SESSION['UserDetailsPostData']['password1'] = "";
$_SESSION['UserDetailsPostData']['password2'] = "";

if (!v::email()->validate($_POST['email-addr'])) {
  $_SESSION['AC-RegUserDetails-Errors']['Email'] = "The email address is invalid";
  header("Location: " . app('request')->curl);
}

if ($_POST['forename'] == "") {
  $_SESSION['AC-RegUserDetails-Errors']['Parent-FN'] = "No parent first name";
  header("Location: " . app('request')->curl);
}

if ($_POST['surname'] == "") {
  $_SESSION['AC-RegUserDetails-Errors']['Parent-LN'] = "No parent last name";
  header("Location: " . app('request')->curl);
}

if ($_POST['password1'] != $_POST['password2']) {
  $_SESSION['AC-RegUserDetails-Errors']['PasswordMatch'] = "Passwords do not match";
  header("Location: " . app('request')->curl);
}

if ($_POST['password1']) {
  $_SESSION['AC-RegUserDetails-Errors']['PasswordMatch'] = "Passwords do not match";
  header("Location: " . app('request')->curl);
}

if (!v::stringType()->length(7, null)->validate($_POST['password1'])) {
  $_SESSION['AC-RegUserDetails-Errors']['PasswordMatch'] = "Password does not meet the minimum length requirements";
  header("Location: " . app('request')->curl);
}

if ($_POST['mobile'] == "") {
  $_SESSION['AC-RegUserDetails-Errors']['MobileMissing'] = "You didn't provide a mobile number";
  header("Location: " . app('request')->curl);
}

$_SESSION['UserDetailsPostData']['mobile'] = "+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", $_POST['mobile'])), '0');

// If errors are set, kill
if (isset($_SESSION['AC-RegUserDetails-Errors'])) {
  die();
}

// Otherwise begin to continue. If email submitted is different to original, it
// must be verified.

$query = $db->prepare("SELECT Email FROM joinParents WHERE Hash = ?");
$query->execute([$_SESSION['AC-Registration']['Hash']]);

if ($query->fetchColumn() != $_POST['email-addr']) {
  // Verify the new email
}
