<?php
$pagetitle = "Login";

$errorState = $username = null;

if ( isset($_SESSION['ErrorState']) ) {
  $errorState = $_SESSION['ErrorState'];
  $username = $_SESSION['EnteredUsername'];
}

$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <h1 class="mb-5">Sign in using your <?=htmlspecialchars(env('CLUB_NAME'))?> Account</h1>
  <div class="row">
    <div class="col-md-8 col-lg-5">
      <!--
      <div class="alert alert-warning">
        <strong>Overnight System Maintenance</strong> <br>
        Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
      </div>
      -->
      <?php if ($errorState) { ?>
      <div class="alert alert-danger">
        <strong>Your details were incorrect</strong> <br>
        Please try again
        <?php if (isset($_SESSION['ErrorStateLSVMessage'])) {
          echo $_SESSION['ErrorStateLSVMessage'];
          unset($_SESSION['ErrorStateLSVMessage']);
        } ?>
      </div>
      <?php } ?>

      <?php if (isset($_SESSION['ErrorAccountLocked']) && $_SESSION['ErrorAccountLocked']) { ?>
      <div class="alert alert-danger">
        <strong>Your account has been locked due to a number of failed login attempts</strong> <br>
        Please <a href="<?=htmlspecialchars(autoUrl("resetpassword"))?>" class="alert-link">reset your password</a> in order to continue
      </div>
      <?php unset($_SESSION['ErrorAccountLocked']); } ?>

      <form method="post" action="<?=htmlspecialchars(autoUrl("login"))?>" name="loginform" id="loginform" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="email-address">Email address</label>
          <input type="email" name="email-address" id="email-address" class="form-control form-control-lg text-lowercase" <?php if ($errorState) { ?> value="<?=htmlspecialchars($username)?>"<?php } ?> required <?php if (!$username) { ?>autofocus<?php } ?> placeholder="yourname@example.com" autocomplete="email">
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password" <?php if ($username) { ?>autofocus<?php } ?> autocomplete="current-password">
          <div class="invalid-feedback">
            Please enter a password.
          </div>
        </div>
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="RememberMe" id="RememberMe" checked aria-describedby="RememberMeHelp">
            <label class="custom-control-label" for="RememberMe">Keep me logged in</label>
            <small id="RememberMeHelp" class="form-text text-muted">
              Untick this box if you are using a public or shared computer
            </small>
          </div>
        </div>
        <input type="hidden" name="target" value="<?=$_SESSION['TARGET_URL']?>">
        <?=SCDS\CSRF::write()?>
        <input type="hidden" name="SessionSecurity" value="<?=session_id()?>">
        <p class="mb-5"><input type="submit" name="login" id="login" value="Login" class="btn btn-lg btn-primary"></p>
        <div class="mb-5">
          <p>
            Not yet registered? It's easy to get started.
          </p>
          <span>
            <a href="<?=htmlspecialchars(autoUrl("register"))?>" class="btn btn-dark">
              Create an account
            </a>
          </span>
          <span>
            <a href="<?=htmlspecialchars(autoUrl("resetpassword"))?>" class="btn btn-dark">
              Forgot password?
            </a>
          </span>
        </div>
      </form>

      <?php if (bool(env('IS_CLS'))) { ?>
      <p class="small mb-0">
        Support Helpline: <a class=""
        href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>
      </p>

      <p class="small mb-4">
        Support Website: <a class=""
        href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">CLS
        ASC Help and Support</a>
      </p>
      <?php } else { ?>
      <p class="small mb-0">
        For support, please contact your own club in the first instance.
      </p>
      <p class="small mb-0">
        Support Helpline: <a class=""
        href="mailto:support@myswimmingclub.co.uk">support@myswimmingclub.co.uk</a>
      </p>

      <p class="small mb-4">
        Support Website: <a class=""
        href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">SCDS Help and Support (hosted by CLS ASC)</a>
      </p>
      <?php } ?>

      <!--
      <p class="small mb-5">
        Unauthorised access to or misuse of this system is prohibited and constitutes an offence under the Computer Misuse Act 1990. If you disclose any information obtained through this system without authority then <?=htmlspecialchars(env('CLUB_NAME'))?> or Swimming Club Data Systems may take legal action against you.
      </p>
      -->
        
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();

unset($_SESSION['ErrorState']);

?>
