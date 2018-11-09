<?php
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
      $userID = $row['UserID'];

      $resetLink = $userID . "-reset-" . md5(generateRandomString(20) . time());

      $query = "INSERT INTO passwordTokens (`UserID`, `Token`, `Type`) VALUES ('$userID', '$resetLink', 'Password_Reset');";
      mysqli_query($link, $query);

      // PHP Email
      $subject = "Password Reset for " . $row['Forename'] . " " . $row['Surname'];
      $to =  "" . $row['Forename'] . " " . $row['Surname'] . " <" . $row['EmailAddress'] . ">";
      $sContent = '
      <h1>Hello ' . $row['Forename'] . '</h1>
      <p>Here\'s your <a href="' . autoUrl("resetpassword/auth/" . $resetLink) . '">password reset link - ' . autoUrl("resetpassword/auth/" . $resetLink) . '</a>.</p>
      <p>Follow this link to reset your password quickly and easily.</p>
      <p>If you did not request a password reset, please delete and ignore this email.</p>
      ';

      if (notifySend($to, $subject, $sContent, $row['Forename'] . " " .
      $row['Surname'], $row['EmailAddress'], ["Email" =>
      "password-help@chesterlestreetasc.co.uk", "Name" => CLUB_NAME . " Account Help"])) {
        echo '
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-sm-6 col-md-5 col-lg4">
              <div class="alert alert-success">
                <strong>We found your account and have sent you an email to reset your password</strong>
                <p class="mb-2">Check your email account and follow the link to reset your password.</a>.</p>
                <p class="mb-0">If you request another password reset, only the most recent link will work.</p>
              </div>
            </div>
          </div>
        </div>
        ';
      } else {
        echo '
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-sm-6 col-md-5 col-lg4">
              <div class="alert alert-warning">
                <strong>We were unable to send password reset details to your email address</strong>
                <p>If you do not have an account, <a href="' . autoUrl("register") . '" class="alert-link">register for an account</a></p>
                <p>Or, <a href="' . autoUrl("resetpassword") . '" class="alert-link">try again</a></p>
                <p class="mb-0">Contact our support team if the issue persists.</p>
              </div>
            </div>
          </div>
        </div>
        ';
      }
    }
    else {
      // error
      echo '
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-sm-6 col-md-5 col-lg4">
            <div class="alert alert-warning">
              <strong>We did not find an account using those details</strong>
              <p>If you do not have an account, <a href="' . autoUrl("register") . '" class="alert-link">register for an account</a></p>
              <p>Or, <a href="' . autoUrl("resetpassword") . '" class="alert-link">try again</a></p>
            </div>
          </div>
        </div>
      </div>
      ';
    }
  }


?>
<?php include BASE_PATH . "views/footer.php" ?>
