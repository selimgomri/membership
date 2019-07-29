<?php

function reportError($e) {
  $reportedError = false;
  if (env('ERROR_REPORTING_EMAIL') != null) {
    try {
      $emailMessage = '<p>This is an error report</p>';
      $emailMessage .= '<p>The active user was ' . htmlspecialchars($_SESSION['Forename'] . ' ' . $_SESSION['Surname']) . ' (User ID #' . $_SESSION['UserID'] . ')</p>';
      if (isset($e)) {
        ob_start();
        pre($e);
        $error = ob_get_clean();
        $emailMessage .= $error;
      }

      ob_start();
      pre(app('request'));
      $error = ob_get_clean();
      $emailMessage .= $error;

      notifySend(null, 'System Error Report', $emailMessage, "System Admin", env('ERROR_REPORTING_EMAIL'));
      $reportedError = true;
    } catch (Exception $f) {
      $reportedError = false;
    }
  }

  return $reportedError;
}