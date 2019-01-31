<?php

global $db;

use GeoIp2\Database\Reader;

$security_status = false;

use PragmaRX\Google2FA\Google2FA;
$ga2fa = new Google2FA();

if ($_POST['SessionSecurity'] == session_id()) {
  $security_status = true;
} else {
  $security_status = false;
}
if ($_POST['LoginSecurityValue'] == $_SESSION['LoginSec']) {
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
    $query = $db->prepare("SELECT EmailAddress, Forename, Surname, AccessLevel FROM users WHERE UserID = ?");
    $query->execute([$_SESSION['2FAUserID']]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    $_SESSION['EmailAddress'] = $row['EmailAddress'];
    $_SESSION['Forename'] = $row['Forename'];
    $_SESSION['Surname'] = $row['Surname'];
    $_SESSION['UserID'] = $_SESSION['2FAUserID'];
    $_SESSION['AccessLevel'] = $row['AccessLevel'];
    $_SESSION['LoggedIn'] = 1;

    unset($_SESSION['2FAUserID']);

    $hash = hash('sha512', time() . $_SESSION['UserID'] . random_bytes(64));

    $geo_string = "Location Information Unavailable";

    try {
      $reader = new Reader(BASE_PATH . 'storage/geoip/GeoLite2-City.mmdb');
      $record = $reader->city(app('request')->ip());
      $city;
      if ($record->city->name != "") {
        $city = $record->city->name . ', ';
      }
      $subdivision;
      if ($record->mostSpecificSubdivision->name != "" && $record->mostSpecificSubdivision->name != $record->city->name) {
        $subdivision = $record->mostSpecificSubdivision->name . ', ';
      }
      $country;
      if ($record->country->name != "") {
        $country = $record->country->name;
      }

      $geo_string = $city . $subdivision . $country;
    } catch (AddressNotFoundException $e) {
      $geo_string = "Unknown Location";
    } catch (InvalidDatabaseException $e) {
      $geo_string = "Location Information Unavailable";
    }

    $sql = "INSERT INTO `userLogins` (`UserID`, `IPAddress`, `GeoLocation`, `Browser`, `Platform`, `Mobile`, `Hash`, `HashActive`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    global $db;

    $mobile = 0;

    $browser_details = new WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

    $browser = $browser_details->browser->name . ' ' . $browser_details->browser->version->toString();

    if ($browser_details->isType('mobile')) {
      $mobile = 1;
    }

    $remember_me = 0;
    if ($_SESSION['2FAUserRememberMe']) {
      $remember_me = 1;
    }

    $login_details = [
      $_SESSION['UserID'],
      app('request')->ip(),
      $geo_string,
      $browser,
      $browser_details->os->toString(),
      $mobile,
      $hash,
      $remember_me
    ];

    try {
      $query = $db->prepare($sql);
      $query->execute($login_details);
    } catch (PDOException $e) {
      halt(500);
    }

    $user_info_cookie = json_encode([
      'Forename' => $row['Forename'],
      'Surname' => $row['Surname'],
      'Account' => $_SESSION['UserID'],
      'TopUAL'  => $row['AccessLevel']
    ]);

    unset($_SESSION['LoginSec']);

    setcookie(COOKIE_PREFIX . "UserInformation", $user_info_cookie, time()+60*60*24*120 , "/", 'chesterlestreetasc.co.uk', true, false);
    setcookie(COOKIE_PREFIX . "AutoLogin", $hash, time()+60*60*24*120, "/", 'chesterlestreetasc.co.uk', true, false);

    // Test if we've seen a login from here before
    $login_before_data = [
      $_SESSION['UserID'],
      app('request')->ip(),
      ucwords(app('request')->browser()),
      ucwords(app('request')->platform())
    ];

    $login_before = $db->prepare("SELECT COUNT(*) FROM `userLogins` WHERE `UserID` = ? AND `IPAddress` = ? AND `Browser` = ? AND `Platform` = ?");
    $login_before->execute($login_before_data);
    $login_before_count = $login_before->fetchColumn();

    if ($login_before_count == 1) {

      $subject = "New Account Login";
      $message = '<p>Somebody just logged into your ' . CLUB_NAME . ' Account from ' . $browser . ', using a device running ' . $browser_details->os->toString() . ' we believe was located in ' . $geo_string . '*.</p><p>We haven\'t seen a login from this location and device before.</p><p>If this was you then you can ignore this email. If this was not you, please <a href="' . autoUrl("") . '">log in to your account</a> and <a href="' . autoUrl("myaccount/password") . '">change your password</a> as soon as possible.</p><p>Kind Regards, <br>The ' . CLUB_NAME . ' Team</p><p class="text-muted small">* We\'ve estimated your location from your public IP Address. The location given may not be where you live.</p>';
      $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
      `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'Security')";
      try {
        $db->prepare($notify)->execute([$_SESSION['UserID'], $subject, $message]);
      } catch (PDOException $e) {
        halt(500);
      }

    }
  } catch (Exception $e) {
    halt(500);
  }
} else {
  $_SESSION['ErrorState'] = true;
  if ($security_status == false) {
    $_SESSION['ErrorState'] = true;
    $_SESSION['ErrorStateLSVMessage'] = "We were unable to verify the integrity of your login attempt. The site you entered your username and password on may have been attempting to capture your login details. Try reseting your password urgently.";
    $_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
  }
}

if (isset($_SESSION['UserID']) && filter_var(getUserOption($_SESSION['UserID'], "IsSpotCheck2FA"), FILTER_VALIDATE_BOOLEAN)) {
  setUserOption($_SESSION['UserID'], "IsSpotCheck2FA", false);
}

unset($_SESSION['LoginSec']);
header("Location: " . autoUrl(ltrim($_POST['target'], '/')));
