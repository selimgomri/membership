<?php

  $preventLoginRedirect = true;
  include "database.php";

  // Registration Form Handler

  $forename = mysqli_real_escape_string($$link, trim(htmlspecialchars(ucwords($_POST['forename']))));
  $surname = mysqli_real_escape_string($$link, trim(htmlspecialchars(ucwords($_POST['surname']))));
  $username = mysqli_real_escape_string($$link, strtolower(trim(htmlspecialchars($_POST['username']))));
  $password1 = mysqli_real_escape_string($$link, trim(htmlspecialchars($_POST['password1'])));
  $password2 = mysqli_real_escape_string($$link, trim(htmlspecialchars($_POST['password2'])));
  $email = mysqli_real_escape_string($$link, strtolower(trim(htmlspecialchars($_POST['email']))));
  $mobile = mysqli_real_escape_string($$link, preg_replace('/\D/', '', $_POST['mobile'])); // Removes anything that isn't a digit
  $emailAuth = mysqli_real_escape_string($_POST['emailAuthorise']);
  if ($emailAuth != 1) {
    $emailAuth == 0;
  }
  $smsAuth = mysqli_real_escape_string($_POST['smsAuthorise']);
  if ($smsAuth != 1) {
    $smsAuth == 0;
  }

  $username = preg_replace('/\s+/', '', $username);

  $usernameSQL = "SELECT * FROM users WHERE Username = '$username' LIMIT 0, 30 ";
  $usernameResult = mysqli_query($$link, $usernameSQL);
  $usernameCount = mysqli_num_rows($usernameResult);

  $emailSQL = "SELECT * FROM users WHERE EmailAddress = '$email' LIMIT 0, 30 ";
  $emailResult = mysqli_query($$link, $emailSQL);
  $emailCount = mysqli_num_rows($emailResult);

  if ($forename != null && $surname != null && $username != null && $email != null && $mobile != null && $password1 != null && $password1 != null) {
    if ($usernameCount > 0 || $emailCount > 0) {
      // Fail, Need unique username
      $_SESSION['RegistrationUsername'] = $username;
      $_SESSION['RegistrationForename'] = $forename;
      $_SESSION['RegistrationSurname'] = $surname;
      $_SESSION['RegistrationEmail'] = $email;
      $_SESSION['RegistrationMobile'] = $mobile;

      header("Location: register.php");

    }
    else {
      // Registration may be allowed
      if ($password1 == $password2) {
        $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);
        // Success
        $sql = "INSERT INTO `users` (`UserID`, `Username`, `Password`, `AccessLevel`, `EmailAddress`, `EmailComms`, `Forename`, `Surname`, `Mobile`, `MobileComms`) VALUES (NULL, '$username', '$hashedPassword', 'Parent', '$email', '$emailAuth', '$forename', '$surname', '$mobile', '$smsAuth');";
        mysqli_query($$link, $sql);
        // Check it went in
        $query = "SELECT * FROM users WHERE Username = '$username' AND Password = '$hashedPassword' LIMIT 0, 30 ";
        $result = mysqli_query($$link, $query);
        $row = mysqli_fetch_array($result);
        $id = $row['UserID'];
        $query = "INSERT INTO wallet (UserID, Balance) VALUES ('$id', 0)";
        mysqli_query($$link, $query);
        $count = mysqli_num_rows($result);

        if ($count == 1) {
          $email = $row['EmailAddress'];
          $forename = $row['Forename'];
          $surname = $row['Surname'];
          $userID = $row['UserID'];

          $_SESSION['Username'] = $username;
          $_SESSION['EmailAddress'] = $email;
          $_SESSION['Forename'] = $forename;
          $_SESSION['Surname'] = $surname;
          $_SESSION['UserID'] = $userID;
          $_SESSION['AccessLevel'] = $row['AccessLevel'];
          $_SESSION['LoggedIn'] = 1;

          // PHP Email
          $subject = "Thanks for Joining " . $username;
          $to = $email;
          $sContent = '
          <script type="application/ld+json">
          {
            "@context": "http://schema.org",
            "@type": "EmailMessage",
            "potentialAction": {
              "@type": "ViewAction",
              "url": "https://dev.chesterlestreetasc.co.uk/software/account/login.php",
              "target": "https://dev.chesterlestreetasc.co.uk/software/account/login.php",
              "name": "Login"
            },
            "description": "Login to your accounts",
            "publisher": {
              "@type": "Organization",
              "name": "Chester-le-Street ASC",
              "url": "https://www.chesterlestreetasc.co.uk",
              "url/googlePlus": "https://plus.google.com/110024389189196283575"
            }

          }
          </script>
          <h1>Hello ' . $forename . '</h1>
          <p>Thanks for signing up for your Chester-le-Street ASC Account.</p>
          <p>Your username is <code>' . $username . '</code>. Use it to sign in.</p>
          <p>You can change your personal details and password in My Account, but can\'t change your username.</p>
          ';

          // Always set content-type when sending HTML email
          $headers = "MIME-Version: 1.0" . "\r\n";
          $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
          $headers .= 'From: Chester-le-Street ASC <noreply@chesterlestreetasc.co.uk>' . "\r\n";

          mail($to,$subject,$sContent,$headers);

          header("Location: index.php");
        }
        else {
          // Error with database
          header("Location: register.php");
        }
      }
      else {
        header("Location: register.php");
      }
    }
  }
  else {
    header("Location: register.php");
  }
?>
