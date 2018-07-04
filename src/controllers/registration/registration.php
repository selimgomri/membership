<?php

use Respect\Validation\Validator as v;

// Registration Form Handler

$forename = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['forename']))));
$surname = mysqli_real_escape_string($link, trim(htmlspecialchars(ucwords($_POST['surname']))));
//$username = mysqli_real_escape_string($link, strtolower(trim(htmlspecialchars($_POST['username']))));
$username = mysqli_real_escape_string($link, $forename . $surname . "-" . md5(generateRandomString(20) . time()));
$password1 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['password1'])));
$password2 = mysqli_real_escape_string($link, trim(htmlspecialchars($_POST['password2'])));
$email = mysqli_real_escape_string($link, strtolower(trim(htmlspecialchars($_POST['email']))));
$mobile = mysqli_real_escape_string($link, preg_replace('/\D/', '', $_POST['mobile'])); // Removes anything that isn't a digit
$emailAuth = 0;
if ($_POST['emailAuthorise'] != 1) {
  $emailAuth = 0;
} else {
  $emailAuth = 1;
}
$smsAuth = 0;
if ($_POST['smsAuthorise'] != 1) {
  $smsAuth = 0;
} else {
  $smsAuth = 1;
}

$status = true;
$statusMessage = "";

$username = preg_replace('/\s+/', '', $username);

$usernameSQL = "SELECT * FROM users WHERE Username = '$username' LIMIT 0, 30 ";
$usernameResult = mysqli_query($link, $usernameSQL);
if (mysqli_num_rows($usernameResult) > 0) {
  $status = false;
  $statusMessage .= "
  <li>An internal error occured</li>
  ";
}

if (!v::stringType()->length(7, null)->validate($password1)) {
  $status = false;
  $statusMessage .= "
  <li>Password does not meet the password length requirements. Passwords must be
  8 characters or longer</li>
  ";
}

if (!v::email()->validate($email)) {
  $status = false;
  $statusMessage .= "
  <li>That email address is not valid</li>
  ";
}

if (!v::phone()->validate($mobile)) {
  $status = false;
  $statusMessage .= "
  <li>That phone number is not valid</li>
  ";
}

if ($password1 != $password2) {
  $status = false;
  $statusMessage .= "
  <li>Passwords do not match</li>
  ";
}

$emailSQL = "SELECT * FROM users WHERE EmailAddress = '$email' LIMIT 0, 30 ";
$emailResult = mysqli_query($link, $emailSQL);
if (mysqli_num_rows($emailResult) > 0) {
  $status = false;
  $statusMessage .= "
  <li>That email address is already used</li>
  ";
}

$hashedPassword = password_hash($password1, PASSWORD_BCRYPT);

$account = [
  "Forename"      => $forename,
  "Surname"       => $surname,
  "Username"      => $username,
  "Password"      => $hashedPassword,
  "EmailAddress"  => $email,
  "EmailComms"    => $emailAuth,
  "Mobile"        => $mobile,
  "MobileComms"   => $smsAuth
];

$accountJSON = json_encode($account);

if ($status) {
  // Registration may be allowed
  // Success
  $authCode = md5(generateRandomString(20) . time());
  $sql = "INSERT INTO `newUsers` (`AuthCode`, `UserJSON`) VALUES ('$authCode',
  '$accountJSON');";
  mysqli_query($link, $sql);
  // Check it went in
  $query = "SELECT * FROM `newUsers` WHERE `AuthCode` = '$authCode' LIMIT 1";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_array($result);
  $id = $row['ID'];
  $verifyLink = "register/auth/" . $id . "/" . "new-user/" . $authCode;

  // PHP Email
  $subject = "Thanks for Joining " . $forename;
  $to = $email;
  $sContent = '
  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "EmailMessage",
    "potentialAction": {
      "@type": "ViewAction",
      "url": "' . autoUrl("") . '",
      "target": "' . autoUrl("") . '",
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
  <p>Hello ' . $forename . '</p>
  <p>Thanks for signing up for your Chester-le-Street ASC Account.</p>
  <p>We need you to verify your email address by following this link - <a
  href="' . autoUrl($verifyLink) . '" target="_blank">' .
  autoUrl($verifyLink) . '</a></p>
  <p>You will use your email address, <code>' . $email . '</code> to sign in.</p>
  <p>You can change your personal details and password in My Account</p>
  <p>For help, send an email to <a
  href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>/</p>
  ';

  $messageid = time() .'-' . md5("CLS-Membership-Signup" . $to) . '@account.chesterlestreetasc.co.uk';

  // Always set content-type when sending HTML email
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Message-ID: <" . $messageid . ">\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= 'From: Chester-le-Street ASC <noreply@chesterlestreetasc.co.uk>' . "\r\n";

  mail($to,$subject,$sContent,$headers);

  $_SESSION['RegistrationGoVerify'] = '
  <div class="alert alert-success mb-0">
    <p class="mb-0">
      <strong>
        We now need you to verify your email address
      </strong>
    </p>

    <p class="mb-0">
      You\'ll find an email waiting for you in your inbox. Follow the link in
      there and we can finish your registration. You cannot complete account
      registration without verifying your email.
    </p>
  </div>
  ';

  header("Location: " . autoUrl("register"));
} else {
  $_SESSION['RegistrationUsername'] = $username;
  $_SESSION['RegistrationForename'] = $forename;
  $_SESSION['RegistrationSurname'] = $surname;
  $_SESSION['RegistrationEmail'] = $email;
  $_SESSION['RegistrationMobile'] = $mobile;
  if ($emailAuth == 1) {
    $_SESSION['RegistrationEmailAuth'] = " checked ";
  }
  if ($smsAuth == 1) {
    $_SESSION['RegistrationSmsAuth'] = " checked ";
  }

  $_SESSION['ErrorState'] = '
  <div class="alert alert-warning">
  <p><strong>Something wasn\'t right</strong></p>
  <ul class="mb-0">' . $statusMessage . '</ul></div>';

  header("Location: " . autoUrl("register"));
}
?>
