<?php

  $errorMessage = "";
  $errorState = false;

  if (!empty($_POST['username']) && !empty($_POST['password'])) {
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

        $sql = "INSERT INTO `userLogins` (`UserID`, `IPAddress`, `Browser`, `Platform`, `Mobile`) VALUES (?, ?, ?, ?, ?)";
        global $db;

        $mobile = 0;

        if (app('request')->isMobile()) {
          $mobile = 1;
        }

        $login_details = [
          $_SESSION['UserID'],
          app('request')->ip(),
          ucwords(app('request')->browser()),
          ucwords(app('request')->platform()),
          $mobile
        ];

        try {
        	$query = $db->prepare($sql);
        	$query->execute($login_details);
        } catch (PDOException $e) {
        	halt(500);
        }

        if ($_SESSION['AccessLevel'] == "Parent") {
          $subject = "Account Login";
          $message = '
          <p>Somebody just logged into your Chester-le-Street ASC Account from ' . ucwords(app('request')->browser()) . '.</p>
          <p>If this was you then you can ignore this email. If this was not you, please <a href="' . autoUrl("") . '">log in to your account</a> and <a href="' . autoUrl("myaccount/password") . '">change your password</a> as soon as possible.</p>
          <p>Kind Regards, <br>The Chester-le-Street ASC Team</p>
          ';
          $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
          `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Security')";
          try {
            $db->prepare($notify)->execute([$_SESSION['UserID'], $subject, $message]);
          } catch (PDOException $e) {
            halt(500);
          }
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
  ?>
