<?php

$pagetitle = "Two Factor Authentication";

$errorState = false;

if ( isset($_SESSION['ErrorState']) ) {
  $errorState = $_SESSION['ErrorState'];
}

$lsv = hash('sha256', random_bytes(100));
$_SESSION['LoginSec'] = $lsv;

$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <h1>Enter your Two Factor Authentication Code</h1>
  <div class="row">
    <div class="col-md-8 col-lg-5">
      <p class="lead mb-5">
        We've just sent you an authentication code by email. Please type this
        code below.
      </p>
      <!--
      <div class="alert alert-warning">
        <strong>Overnight System Maintenance</strong> <br>
        Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
      </div>
      -->
      <?php if ($errorState == true) { ?>
      <div class="alert alert-danger">
        <strong>Your authentication code was incorrect</strong> <br>
        Please try again
        <?php if (isset($_SESSION['ErrorStateLSVMessage'])) {
          echo $_SESSION['ErrorStateLSVMessage'];
          unset($_SESSION['ErrorStateLSVMessage']);
        } ?>
      </div>
      <?php } ?>

      <?php if ($_SESSION['TWO_FACTOR_RESEND']) { ?>
      <div class="alert alert-success">
        <p class="mb-0"><strong>We have successfully resent your email</strong></p>
        <p class="mb-0">Please now check your inbox. It may take a moment to recieve the email.</p>
      </div>
      <?php } ?>

      <form method="post" action="<?=autoUrl("2fa")?>" name="2faform" id="2faform">
        <div class="form-group">
          <label for="auth">Authentication Code</label>
          <input type="number" name="auth" id="auth" class="form-control form-control-lg" required autofocus placeholder="654321">
        </div>
        <input type="hidden" name="target" value="<?=$_SESSION['TARGET_URL']?>">
        <input type="hidden" name="LoginSecurityValue" value="<?=$lsv?>">
        <input type="hidden" name="SessionSecurity" value="<?=session_id()?>">
        <p class="mb-5"><input type="submit" name="verfy" id="verify" value="Verify" class="btn btn-lg btn-primary"></p>
      </form>

      <p class="mb-5">
        <a href="<?=autoUrl("2fa/exit")?>" class="btn btn-dark">Cancel</a>
        <a href="<?=autoUrl("2fa/resend")?>" class="btn btn-dark">Resend Email</a>
      </p>

      <p class="small mb-0">
        Support Helpline: <a class=""
        href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a>
      </p>

      <p class="small mb-4">
        Support Website: <a class=""
        href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">CLS
        ASC Help and Support</a>
      </p>

      <!--
      <p class="small mb-5">
        Unauthorised access to or misuse of this system is prohibited and
        constitutes an offence under the Computer Misuse Act 1990. If you
        disclose any information obtained through this system without authority
        then <?=CLUB_NAME?> or Chester-le-Street ASC Club Digital Services may
        take legal action against you.
      </p>
      -->
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

unset($_SESSION['ErrorState']);
unset($_SESSION['TWO_FACTOR_RESEND']);

?>
