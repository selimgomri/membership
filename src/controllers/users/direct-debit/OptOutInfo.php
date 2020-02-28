<?php

global $db;

$getUser = $db->prepare("SELECT Forename, Surname, RR FROM users WHERE UserID = ? AND AccessLevel = 'Parent';");
$getUser->execute([$person]);

$user = $getUser->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$renewalAvailable = true;
try {
  include 'GetRenewal.php';
} catch (Exception $e) {
  $renewalAvailable = false;
}

$pagetitle = 'Allow Opt Out';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Allow a Direct Debit Opt Out</h1>
        <form method="post">
          <p class="lead"><?php if ($user['RR']) { ?>Complete registration without setting up a direct debit<?php } else { ?>Complete renewal without setting up a direct debit<?php } ?></p>

          <?php if (isset($_SESSION['ErrorInvalidRequest']) && $_SESSION['ErrorInvalidRequest']) { ?>
          <div class="alert alert-warning">
            <strong>We couldn't verify you had the authority to do that</strong>
          </div>
          <?php } unset($_SESSION['ErrorInvalidRequest']); ?>

          <?php if (isset($_SESSION['ErrorNoReg']) && $_SESSION['ErrorNoReg']) { ?>
          <div class="alert alert-warning">
            <strong>There was no open registration or renewal</strong>
          </div>
          <?php } unset($_SESSION['ErrorNoReg']); ?>

          <?php if (isset($_SESSION['Successful']) && $_SESSION['Successful']) { ?>
          <div class="alert alert-success">
            <strong>We've marked the renewal/registration as complete and bypassed the direct debit requirement</strong>
          </div>
          <?php } unset($_SESSION['Successful']); ?>

          <?php if ($renewalAvailable) { ?>

          <p>Allow <?=htmlspecialchars($user['Forename'] . ' ' . $user['Surname'])?> to not use Direct Debit.</p>

          <?=\SCDS\FormIdempotency::write()?>
          <?=\SCDS\CSRF::write()?>

          <p>
            <button type="submit" class="btn btn-success">
              Allow
            </button>
          </p>
          <?php } else { ?>

          <p>There's not currently a registration or renewal open for <?=htmlspecialchars($user['Forename'] . ' ' . $user['Surname'])?>, so you're not able to authorise a direct debit opt out.</p>

          <?php } ?>
        </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();