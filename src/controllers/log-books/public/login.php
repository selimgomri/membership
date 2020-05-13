<?php
$pagetitle = "Member Login - Log Books";

$username = "";
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LogBooks-SE-ID'])) {
  $username = $_SESSION['TENANT-' . app()->tenant->getId()]['LogBooks-SE-ID'];
}

$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <h1 class="mb-5">Sign in using your Swim England Number and Club Password</h1>
  <div class="row justify-content-between">
    <div class="col-md-8 col-lg-5">

      <!--
      <div class="alert alert-warning">
        <strong>Overnight System Maintenance</strong> <br>
        Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
      </div>
      -->
      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['LogBooks-MemberLoginError'])) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>We could not log you in</strong>
        </p>
        <p class="mb-0">
          <?=htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['LogBooks-MemberLoginError'])?>
        </p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['LogBooks-MemberLoginError']); } ?>

      <form method="post" action="<?= autoUrl("log-books/login") ?>" name="loginform" id="loginform" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="swim-england">Swim England Membership Number</label>
          <input type="text" name="swim-england" id="swim-england" class="form-control form-control-lg text-lowercase" value="<?= htmlspecialchars($username) ?>" required <?php if (!$username) { ?>autofocus<?php } ?> placeholder="123456" autocomplete="">
          <div class="invalid-feedback">
            Please enter a valid membership number.
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password" <?php if (mb_strlen($username) > 0) { ?>autofocus<?php } ?> aria-describedby="password-help" autocomplete="current-password">
          <div class="invalid-feedback">
            Please enter a password.
          </div>
          <small id="password-help" class="text-muted">
            This is the password for your club account, not your Swim England account.
          </small>
        </div>
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="RememberMe" id="RememberMe" aria-describedby="RememberMeHelp" disabled>
            <label class="custom-control-label" for="RememberMe">Keep me logged in - <span class="badge badge-info">Coming soon</span></label>
            <small id="RememberMeHelp" class="form-text text-muted">
              Untick this box if you are using a public or shared computer
            </small>
          </div>
        </div>
        <input type="hidden" name="target" value="<?= $_SESSION['TENANT-' . app()->tenant->getId()]['TARGET_URL'] ?>">
        <?= SCDS\CSRF::write() ?>
        <input type="hidden" name="SessionSecurity" value="<?= session_id() ?>">
        <p class="mb-5"><input type="submit" name="login" id="login" value="Login" class="btn btn-lg btn-primary"></p>
      </form>

      <?php if (bool(env('IS_CLS'))) { ?>
        <p class="small mb-0">
          Support Helpline: <a class="" href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>
        </p>

        <p class="small mb-4">
          Support Website: <a class="" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">CLS
            ASC Help and Support</a>
        </p>
      <?php } else { ?>
        <p class="small mb-0">
          For support, please contact your own club in the first instance.
        </p>
        <p class="small mb-0">
          Support Helpline: <a class="" href="mailto:support@myswimmingclub.co.uk">support@myswimmingclub.co.uk</a>
        </p>

        <p class="small mb-4">
          Support Website: <a class="" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">SCDS Help and Support (hosted by CLS ASC)</a>
        </p>
      <?php } ?>

      <!--
      <p class="small mb-5">
        Unauthorised access to or misuse of this system is prohibited and constitutes an offence under the Computer Misuse Act 1990. If you disclose any information obtained through this system without authority then <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?> or Swimming Club Data Systems may take legal action against you.
      </p>
      -->

    </div>
    <div class="col col-lg-4">
      <div class="cell">
        <p>
          If you've never signed in before, ask your account holder (usually your parent or guardian) to follow the setup instructions in the <strong>log books</strong> section of their account.
        </p>

        <p>
          They'll be able to create a password for your account. You can change your password once you have logged in for the first time.
        </p>

        <p>
          If you're aged 13 or over, you'll soon be able to add an email address to your account so that you can reset your own password.
        </p>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();

unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);

?>