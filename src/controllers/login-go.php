<?php

  $errorMessage = "";
  $errorState = false;

  use GeoIp2\Database\Reader;

  if ((!empty($_POST['username']) && !empty($_POST['password'])) && ($_POST['LoginSecurityValue'] == $_SESSION['LoginSecurityValue'])) {
    // Let the user login
    $username = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['username'])));
    $password = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['password'])));
    $target = mysqli_real_escape_string($link, trim($_POST['target']));

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
        $_SESSION['Username'] = $username;
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
          if ($record->mostSpecificSubdivision->name != "") {
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

        if (app('request')->isMobile()) {
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
          ucwords(app('request')->browser()),
          ucwords(app('request')->platform()),
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

        unset($_SESSION['LoginSecurityValue']);

        setcookie("CLSASC_UserInformation", $user_info_cookie, time()+60*60*24*120 , "/", 'chesterlestreetasc.co.uk', true, false);
        setcookie("CLSASC_AutoLogin", $hash, time()+60*60*24*120, "/", 'chesterlestreetasc.co.uk', true, true);

        $subject = "Account Login";
        $message = '<p>Somebody just logged into your Chester-le-Street ASC Account from ' . ucwords(app('request')->browser()) . ', using a device we believe was located in ' . $geo_string . '.</p><p>If this was you then you can ignore this email. If this was not you, please <a href="' . autoUrl("") . '">log in to your account</a> and <a href="' . autoUrl("myaccount/password") . '">change your password</a> as soon as possible.</p><p>Kind Regards, <br>The Chester-le-Street ASC Team</p>';
        $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
        `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 0, 'Security')";
        try {
          $db->prepare($notify)->execute([$_SESSION['UserID'], $subject, $message]);
        } catch (PDOException $e) {
          halt(500);
        }

        if (isset($target)) {
          $target = ltrim($target, '/');
          header("Location: " . autoUrl($target) . "");
        }
        else {
          header("Location: " . autoUrl('') . "");
        }
        //}
      }
      else {
        $_SESSION['ErrorState'] = true;
        $_SESSION['EnteredUsername'] = $username;
        header("Location: " . autoUrl('') . "");
      }
    }
    else {
      $_SESSION['ErrorState'] = true;
      $_SESSION['EnteredUsername'] = $username;
      header("Location: " . autoUrl('') . "");
    }
  }

  if ($_POST['LoginSecurityValue'] != $_SESSION['LoginSecurityValue']) {
    $_SESSION['ErrorState'] = true;
    $_SESSION['ErrorStateLSVMessage'] = "A Login Security Value was not available. We have prevented your login for security reasons. The site you entered your username and password on may have been attempting to capture your login details. Try reseting your password urgently.";
    header("Location: " . autoUrl('') . "");
  }
  ?>
