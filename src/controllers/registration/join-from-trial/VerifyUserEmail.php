<?php

global $db;

$query = $db->prepare("SELECT COUNT(*) FROM joinParents WHERE Hash = ? AND Invited = ?");
$query->execute([$_SESSION['AC-Registration']['Hash'], true]);

if ($query->fetchColumn() != 1) {
  halt(404);
}

$pagetitle = "Verify your email address";
$use_white_background = true;

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>Verify your email</h1>
  <div class="row">
    <div class="col-md-8 mb-5">
      <form method="post" class="needs-validation" novalidate>
        <p class="lead">
          We've sent you a verification code by email. Enter it in the box below
          when you get it.
        </p>

        <p class="text-truncate">
          It was sent to
          <?=htmlspecialchars($_SESSION['AC-UserDetails']['email-addr'])?>
        </p>

        <?php if ($_SESSION['AC-VerifyEmail-Error'] === true) { ?>
          <div class="alert alert-warning">
            The verification code was incorrect. Please try again.
          </div>
        <?php
        unset($_SESSION['AC-VerifyEmail-Error']); } ?>

        <?php if ($_SESSION['AC-Registration']['EmailConfirmationResent'] === true) { ?>
          <div class="alert alert-success">
            Your verification code was resent.
          </div>
        <?php
        unset($_SESSION['AC-Registration']['EmailConfirmationResent']); } ?>

        <?php if ($_SESSION['AC-Registration']['EmailModified'] === true) { ?>
          <div class="alert alert-success">
            We sent a verification code to your new email address.
          </div>
        <?php
        unset($_SESSION['AC-Registration']['EmailModified']); } ?>

        <div class="form-group">
          <label for="verify-code">Verification Code</label>
          <input type="number" name="verify-code" id="verify-code"
          class="form-control form-control-lg" placeholder="123456" required
          autofocus pattern="[0-9]*" inputmode="numeric">
          <div class="invalid-feedback">
            Please enter a number.
          </div>
        </div>

        <p>
          <button class="btn btn-primary" type="submit">
            Verify
          </button>
        </p>

      </form>
    </div>
    <div class="col">
      <div class="cell">
        <form action="<?=autoUrl("register/ac/verify-email/modify")?>" method="post" class="needs-validation" novalidate>
          <h2>Made a mistake?</h2>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email-addr" id="email-addr" class="form-control" placeholder="abc@example.com" required>
            <div class="invalid-feedback">
              Please enter a valid email.
            </div>
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <p class="mb-0">
            <button class="btn btn-dark" type="button">Change Email</button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';
