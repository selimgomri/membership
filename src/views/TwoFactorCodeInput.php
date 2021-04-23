<?php

$pagetitle = "Two Factor Authentication";

$do_random_2FA = filter_var(getUserOption($_SESSION['TENANT-' . app()->tenant->getId()]['2FAUserID'], "IsSpotCheck2FA"), FILTER_VALIDATE_BOOLEAN);

$errorState = false;

$target = "";
if (isset($_GET['target'])) {
  $target = $_GET['target'];
}

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
  $errorState = $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
}

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <?php if (!$do_random_2FA) { ?>
    <h1>Enter your Two Factor Authentication Code</h1>
  <?php } else { ?>
    <h1>Enter your Verification Code</h1>
  <?php } ?>
  <div class="row">
    <div class="col-md-8 col-lg-5">
      <?php if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE']) || $_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE'] !== true) { ?>
        <p class="lead mb-5">
          We've just sent you an authentication code by email. Please type this
          code below.
        </p>
      <?php } else { ?>
        <p class="lead mb-5">
          Enter the authentication code from your Authenticator App.
        </p>
      <?php } ?>
      <?php if (isset($do_random_2FA) && $do_random_2FA) { ?>
        <p class="mb-5">We've asked you to do this as part of a random security spot check.</p>
      <?php } ?>
      <!--
      <div class="alert alert-warning">
        <strong>Overnight System Maintenance</strong> <br>
        Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
      </div>
      -->
      <?php if (isset($errorState) && $errorState) { ?>
        <div class="alert alert-danger">
          <strong>Your authentication code was incorrect</strong> <br>
          Please try again
          <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorStateLSVMessage'])) {
            echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorStateLSVMessage'];
            unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorStateLSVMessage']);
          } ?>
        </div>
      <?php } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_RESEND']) && $_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_RESEND']) { ?>
        <div class="alert alert-success">
          <p class="mb-0"><strong>We have successfully sent your email</strong></p>
          <p class="mb-0">Please now check your inbox. It may take a moment to receive the email.</p>
        </div>
      <?php } ?>

      <form method="post" action="<?= autoUrl("2fa") ?>" name="2faform" id="2faform" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="auth">Authentication Code</label>
          <input type="number" name="auth" id="auth" class="form-control form-control-lg" required autofocus placeholder="654321" pattern="[0-9]*" inputmode="numeric" min="0" max="999999" step="1">
          <div class="invalid-feedback">
            Please enter a numeric authentication code.
          </div>
        </div>
        <input type="hidden" name="target" value="<?= htmlspecialchars($target) ?>">

        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="RememberMe" id="RememberMe" <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['2FAUserRememberMe']) && bool($_SESSION['TENANT-' . app()->tenant->getId()]['2FAUserRememberMe'])) { ?>checked<?php } ?> aria-describedby="RememberMeHelp" value="1">
            <label class="custom-control-label" for="RememberMe">Keep me logged in</label>
            <small id="RememberMeHelp" class="form-text text-muted">
              Untick this box if you are using a public or shared computer
            </small>
          </div>
        </div>

        <?php if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE']) || $_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE'] !== true) { ?>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="setup-time-based-codes" id="setup-time-based-codes" aria-describedby="setup-time-based-codes-help">
              <label class="custom-control-label" for="setup-time-based-codes">Set up an authenticator app</label>
              <small id="setup-time-based-codes-help" class="form-text text-muted">
                Set up an authenticator app, such as Google Authenticator, so that you can use time-based one-time passwords and don't need to wait for an email from us. You can always still get codes by email if you don't have your phone with you.
              </small>
            </div>
          </div>
        <?php } ?>

        <?= SCDS\CSRF::write() ?>
        <input type="hidden" name="SessionSecurity" value="<?= session_id() ?>">
        <p class="mb-5"><input type="submit" name="verify" id="verify" value="Verify" class="btn btn-lg btn-primary"></p>
      </form>

      <p class="mb-5">
        <a href="<?= autoUrl("2fa/exit") ?>" class="btn btn-dark">Cancel</a>
        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE']) && $_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_GOOGLE']) { ?>
          <a href="<?= autoUrl("2fa/resend?target=" . urlencode($target)) ?>" class="btn btn-dark">Get code by email</a>
        <?php } else { ?>
          <a href="<?= autoUrl("2fa/resend?target=" . urlencode($target)) ?>" class="btn btn-dark">Resend Email</a>
        <?php } ?>
      </p>

      <p class="small mb-0">
        For support, please contact your own club in the first instance.
      </p>
      <p class="small mb-0">
        Support Helpline: <a class="" href="mailto:support@myswimmingclub.co.uk">support@myswimmingclub.co.uk</a>
      </p>
      <p class="small mb-4">
        Support Website: <a class="" href="<?= htmlspecialchars(autoUrl('help-and-support', false)) ?>">Help and Support</a>
      </p>


      <p class="small d-none mb-5">
        Unauthorised access to or misuse of this system is prohibited and constitutes an offence under the Computer Misuse Act 1990. If you disclose any information obtained through this system without authority then <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> or Swimming Club Data Systems may take legal action against you.
      </p>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();

unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
unset($_SESSION['TENANT-' . app()->tenant->getId()]['TWO_FACTOR_RESEND']);

?>