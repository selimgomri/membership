<?php
  $preventLoginRedirect = true;
  $pagetitle = "Password Reset";
  include BASE_PATH . "views/header.php";

  $userDetails = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['userDetails'])));
  $captcha = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['g-recaptcha-response'])));
  $captchaStatus = null;

  #
  # Verify captcha
  $post_data = http_build_query(
      array(
          'secret' => 	'6Lc4U0AUAAAAAIrWOxxxwvU6gz_149mZCZc8VEY8',
          'response' => $_POST['g-recaptcha-response'],
          'remoteip' => $_SERVER['REMOTE_ADDR']
      )
  );
  $opts = array('http' =>
      array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
      )
  );
  $context  = stream_context_create($opts);
  $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
  $result = json_decode($response);
  if (!$result->success) {
    echo '
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-sm-6 col-md-5 col-lg4">
          <div class="alert alert-danger">
            <strong>Captcha Verification Failed</strong>
            <p class="mb-0">You must prove that you are human to succeed in life.</p>
          </div>
        </div>
      </div>
    </div>
    ';
  }
  else {
    $captchaStatus = true;
    $found = false;
    $row = "";
    // Test for valid username
    $sql = "SELECT * FROM users WHERE Username = '$userDetails' ";
    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);
    if ($count == 1) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $found = true;
    }

    // Test for valid email
    if ($found != true) {
      $sql = "SELECT * FROM users WHERE EmailAddress = '$userDetails' ";
      $result = mysqli_query($link, $sql);
      $count = mysqli_num_rows($result);
      if ($count == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $found = true;
      }
    }

    if ($found == true) {
      // Reset the password
      $password = generateRandomString(12);
      $newHash = password_hash($password, PASSWORD_BCRYPT);
      $userID = $row['UserID'];
      $sql = "UPDATE `users` SET `Password` = '$newHash' WHERE `UserID` = '$userID'";
      mysqli_query($link, $sql);

      // PHP Email
      $subject = "Password Reset for " . $row['Username'];
      $to =  "" . $row['Forename'] . " " . $row['Surname'] . " <" . $row['EmailAddress'] . ">";
      $sContent = '<h1>Hello ' . $row['Forename'] . '</h1>
      <p>We\'ve reset your password for your Chester-le-Street ASC Account to <code>' . $password . '</code>.</p>
      <p>Please reset your password as soon as possible in My Account by following this $link <a href="' . autoUrl("myaccount/change-password.php") . '"> ' . autoUrl("myaccount/change-password.php") . '</a></p>
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
        "description": "Login with your Temporary Password",
        "publisher": {
          "@type": "Organization",
          "name": "Chester-le-Street ASC",
          "url": "https://www.chesterlestreetasc.co.uk",
          "url/googlePlus": "https://plus.google.com/110024389189196283575"
        }

      }
      </script>';

      // Always set content-type when sending HTML email
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $headers .= 'From: Chester-le-Street ASC <noreply@chesterlestreetasc.co.uk>' . "\r\n";

      mail($to,$subject,$sContent,$headers);

      echo '
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-6 col-md-5 col-lg4">
            <div class="alert alert-success">
              <strong>We found your account and have reset your password</strong>
              <p class="mb-2">Check your email account for your password, <a href="login.php" class="alert-link">then login</a>.</p>
              <p class="mb-0">Reset your password as soon as possible.</p>
            </div>
          </div>
        </div>
      </div>
      ';
    }
    else {
      // error
      echo '
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-6 col-md-5 col-lg4">
            <div class="alert alert-warning">
              <strong>We did not find an account using those details</strong>
              <p>If you do not have an account, <a href="register.php" class="alert-link">register an account</a></p>
              <p>Or, <a href="forgot-password.php" class="alert-link">try again</a></p>
            </div>
          </div>
        </div>
      </div>
      ';
    }
  }


?>
<?php include "footer.php" ?>
