<?php

$hash = hash('sha256', random_int(0, 999999) . env('CLUB_NAME'));
$name = mb_ucfirst(trim($_POST['new-cc-name']));
$email = mb_strtolower(trim($_POST['new-cc']));

global $db;
$insert = $db->prepare("INSERT INTO notifyAdditionalEmails (`UserID`, `EmailAddress`, `Name`, `Hash`) VALUES (?, ?, ?, ?)");
$insert->execute([
  $_SESSION['UserID'],
  $email,
  $name,
  $hash
]);

$id = $db->lastInsertId();

$link = "verify-cc-email/auth/" . $id . "/" . $hash;

$message = '
<p>Hello ' . htmlspecialchars($name) . ',</p>
<p>' . htmlspecialchars($_SESSION['Forename'] . ' ' . $_SESSION['Surname']) . ' wishes for you to also get emails from ' . htmlspecialchars(env('CLUB_NAME')) . '.</p>
<p>Please follow the link below to verify your email address.</p>
<p><strong><a href="' . autoUrl($link) . '">' . autoUrl($link) . '</a></strong></p>
<p>This will confirm your email address and send carbon copies of emails from coaches and committee members at ' . htmlspecialchars(env('CLUB_NAME')) . ' to this address.</p>
<p>If you did not request this, please ignore this email.</p>';

if (!notifySend(null, "Verify your email", $message, $name, $email)) {
  halt(500);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <div class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
        echo $list->render('email');
      ?>
    </div>
    <div class="col-md-9">
      <h2>Verify your email address</h2>
      <p class="lead">
        We've sent an email to that address. The recipient will have to follow a link to confirm their address.
      </p>

      <p>
        <a href="<?=autoUrl("my-account/email")?>" class="btn btn-success">
          Return to email settings
        </a>
      </p>
    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';
