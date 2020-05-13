<?php

function reportError($e) {
  $reportedError = false;
  if (env('ERROR_REPORTING_EMAIL') != null) {
    try {
      $emailMessage = '<p>This is an error report</p>';
      if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) {
        $emailMessage .= '<p>The active user was ' . htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['Forename'] . ' ' . $_SESSION['TENANT-' . app()->tenant->getId()]['Surname']) . ' (User ID #' . $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'] . ')</p>';
      }
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