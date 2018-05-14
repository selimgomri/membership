<?php
  $preventLoginRedirect = true;
  include "database.php";

  $errorMessage = "";
  $errorState = false;

  if (!empty($_POST['username']) && !empty($_POST['password'])) {
    // Let the user login
    $username = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['username'])));
    $password = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['password'])));

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

        /*$redirect = autoUrl($_SESSION['requestedURL']);
        $redirect = preg_replace(autoUrl(""), "" , $redirect);
        if ($redirect != autoUrl("login.php")) {
          header("Location: " . $redirect . "");
        }
        else {*/
          if (isset($_SESSION['requestedURL'])) {
            header("Location: " . $_SESSION['requestedURL'] . "");
          }
          else {
            header("Location: " . autoUrl("") . "");
          }
        //}
      }
      else {
        $_SESSION['ErrorState'] = true;
        $_SESSION['EnteredUsername'] = $username;
        header("Location: login.php");
      }
    }
    else {
      $_SESSION['ErrorState'] = true;
      $_SESSION['EnteredUsername'] = $username;
      header("Location: login.php");
    }
  }
