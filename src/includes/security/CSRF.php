<?php

namespace SCDS;

/**
 * CSRF class which provides namespaced static functions for CSRF requests
 */
class CSRF {
  public static function write() {
    // If the token is not set, define it
    if (!isset($_SESSION['CSRF']) || $_SESSION['CSRF'] == null) {
      $_SESSION['CSRF'] = hash('sha256', random_bytes(100));
    }

    echo '<input id="SCDS-GLOBAL-CSRF" name="SCDS-GLOBAL-CSRF" type="hidden" value="' . htmlspecialchars($_SESSION['CSRF']) . '">';
  }

  public static function verify($throwException = false) {
    if (isset($_SESSION['CSRF']) && isset($_POST['SCDS-GLOBAL-CSRF']) && $_SESSION['CSRF'] == $_POST['SCDS-GLOBAL-CSRF']) {
      // Verifies CSRF, proceed normally
      return true;
    } else if ($throwException) {
      throw new CSRFValidityException('Invalid Cross-Site Request Forgery Token');
    } else {
      return false;
    }
  }

  public static function verifyCode($code, $throwException = false) {
    if (isset($_SESSION['CSRF']) && $_SESSION['CSRF'] == $code) {
      // Verifies CSRF, proceed normally
      return true;
    } else if ($throwException) {
      throw new CSRFValidityException('Invalid Cross-Site Request Forgery Token');
    } else {
      return false;
    }
  }
}