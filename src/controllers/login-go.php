<?php

  $errorMessage = "";
  $errorState = false;
  $target = "";

  $security_status = false;

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

  use GeoIp2\Database\Reader;

  if ((!empty($_POST['username']) && !empty($_POST['password'])) && ($security_status)) {
    // Let the user login
    $username = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['username'])));
    $password = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['password'])));
    $target = ltrim(mysqli_real_escape_string($link, trim($_POST['target'])), '/');

    $username = preg_replace('/\s+/', '', $username);

    $query = "SELECT * FROM users WHERE Username = '$username' OR EmailAddress = '$username' LIMIT 0, 30 ";
    $result = mysqli_query($link, $query);
    $count = mysqli_num_rows($result);

    if ($count == 1) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $hash = $row['Password'];
      $email = $row['EmailAddress'];
      $forename = $row['Forename'];
      $surname = $row['Surname'];
      $userID = $row['UserID'];

      if (password_verify($password, $hash)) {
        $do_random_2FA = random_int(0, 99) < 5 || filter_var(getUserOption($userID, "IsSpotCheck2FA"), FILTER_VALIDATE_BOOLEAN);
      if ($row['AccessLevel'] != "Parent" || filter_var(getUserOption($userID, "Is2FA"), FILTER_VALIDATE_BOOLEAN) || $do_random_2FA) {
          // Do 2FA
          if (filter_var(getUserOption($userID, "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {
            $_SESSION['TWO_FACTOR_GOOGLE'] = true;
          } else {
            $code = random_int(100000, 999999);

            if ($do_random_2FA) {
              setUserOption($userID, "IsSpotCheck2FA", true);
            }

            $message = '
            <p>Hello. Confirm your login by entering the following code in your web browser.</p>
            <p><strong>' . $code . '</strong></p>
            <p>If you did not just try to log in, you can ignore this email. You may want to reset your password.</p>
            <p>Kind Regards,<br>The ' . CLUB_NAME . ' Team</p>';

            if (notifySend(null, "Verification Code", $message, $forename . " " . $surname, $email)) {
              $_SESSION['TWO_FACTOR_CODE'] = $code;
            } else {
              halt(500);
            }
          }
          $_SESSION['2FAUserID'] = $userID;
          if ($_POST['RememberMe']) {
            $_SESSION['2FAUserRememberMe'] = 1;
          }
          $_SESSION['TWO_FACTOR'] = true;
          header("Location: " . autoUrl("2fa"));
        } else {
          $_SESSION['EmailAddress'] = $email;
          $_SESSION['Forename'] = $forename;
          $_SESSION['Surname'] = $surname;
          $_SESSION['UserID'] = $userID;
          $_SESSION['AccessLevel'] = $row['AccessLevel'];
          $_SESSION['LoggedIn'] = 1;

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
          if ($_POST['RememberMe']) {
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
            $message = '<p>Somebody just logged into your <?=CLUB_NAME?> Account from ' . $browser . ', using a device running ' . $browser_details->os->toString() . ' we believe was located in ' . $geo_string . '*.</p><p>We haven\'t seen a login from this location and device before.</p><p>If this was you then you can ignore this email. If this was not you, please <a href="' . autoUrl("") . '">log in to your account</a> and <a href="' . autoUrl("myaccount/password") . '">change your password</a> as soon as possible.</p><p>Kind Regards, <br>The ' . CLUB_NAME . ' Team</p><p class="text-muted small">* We\'ve estimated your location from your public IP Address. The location given may not be where you live.</p>';
            $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
            `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'Security')";
            try {
              $db->prepare($notify)->execute([$_SESSION['UserID'], $subject, $message]);
            } catch (PDOException $e) {
              halt(500);
            }

          }
        }
      } else {
        $_SESSION['ErrorState'] = true;
        $_SESSION['EnteredUsername'] = $username;

      }
    }
    else {
      $_SESSION['ErrorState'] = true;
      $_SESSION['EnteredUsername'] = $username;

    }
  } else {
    if (!$security_status) {
      $_SESSION['ErrorState'] = true;
      $_SESSION['ErrorStateLSVMessage'] = "We were unable to verify the integrity of your login attempt. The site you entered your username and password on may have been attempting to capture your login details. Try reseting your password urgently.";
      $_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
    }
  }
  $_SESSION['InfoSec'] = [$_POST['LoginSecurityValue'], $_SESSION['LoginSec']];
  unset($_SESSION['LoginSec']);
  header("Location: " . autoUrl(ltrim($_POST['target'], '/')));
  ?>
