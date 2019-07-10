<?php

try {
  global $systemInfo;

  if (isset($_POST['TermsAndConditions'])) {
    $systemInfo->setSystemOption('TermsAndConditions', $_POST['TermsAndConditions']);
  }

  if (isset($_POST['PrivacyPolicy'])) {
    $systemInfo->setSystemOption('PrivacyPolicy', $_POST['PrivacyPolicy']);
  }

  $_SESSION['PCC-SAVED'] = true;
} catch (Exception $e) {
  $_SESSION['PCC-ERROR'] = true;
}

header("Location: " . currentUrl());