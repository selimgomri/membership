<?php

try {
  

  if (isset($_POST['TermsAndConditions'])) {
    app()->tenant->setKey('TermsAndConditions', $_POST['TermsAndConditions']);
  }

  if (isset($_POST['PrivacyPolicy'])) {
    app()->tenant->setKey('PrivacyPolicy', $_POST['PrivacyPolicy']);
  }

  if (isset($_POST['WelcomeLetter'])) {
    app()->tenant->setKey('WelcomeLetter', $_POST['WelcomeLetter']);
  }

  $_SESSION['PCC-SAVED'] = true;
} catch (Exception $e) {
  $_SESSION['PCC-ERROR'] = true;
}

header("Location: " . autoUrl("settings/codes-of-conduct/terms-and-conditions"));