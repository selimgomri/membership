<?php

namespace SCDS;

/**
 * CSRF class which provides namespaced static functions for CSRF requests
 */
class CSRF {
  public static function write() {
    // If the token is not set, define it
    if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['CSRF']) || $_SESSION['TENANT-' . app()->tenant->getId()]['CSRF'] == null) {
      $_SESSION['TENANT-' . app()->tenant->getId()]['CSRF'] = hash('sha256', random_bytes(100));
    }

    echo '<input name="SCDS-GLOBAL-CSRF" type="hidden" value="' . htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['CSRF']) . '">';
  }

  public static function verify($throwException = false) {
    if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['CSRF']) && isset($_POST['SCDS-GLOBAL-CSRF']) && $_SESSION['TENANT-' . app()->tenant->getId()]['CSRF'] == $_POST['SCDS-GLOBAL-CSRF']) {
      // Verifies CSRF, proceed normally
      return true;
    } else if ($throwException) {
      throw new CSRFValidityException('Invalid Cross-Site Request Forgery Token');
    } else {
      return false;
    }
  }
}