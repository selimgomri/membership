<?php

http_response_code(401);

$pagetitle = "Login";
include BASE_PATH . "views/header.php";

$errorState = false;

if ( isset($_SESSION['ErrorState']) ) {
  $errorState = $_SESSION['ErrorState'];
  $username = $_SESSION['EnteredUsername'];
}

$lsv = hash('sha256', random_bytes(100) . date("c-U"));
$_SESSION['LoginSec'] = $lsv;

?>
<div class="frontpage1 d-flex flex-column" style="margin:-1.0rem 0;min-height:calc(100vh - 10.9375rem);">
  <div class="mb-auto"></div>
  <div class="container">
    <div class="">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="p-3 my-2 bg-white shadow rounded">
            <div class="text-dark">
              <h2 class="border-bottom border-gray pb-2 mb-3">Please Login</h2>
              <!--<div class="alert alert-warning">
                <strong>Overnight System Maintenance</strong> <br>
                Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
              </div>-->
                <?php if ($errorState == true) { ?>
                <div class="alert alert-danger">
                  <strong>Incorrect details</strong> <br>
                  Please try again
                  <?php if (isset($_SESSION['ErrorStateLSVMessage'])) {
                    echo $_SESSION['ErrorStateLSVMessage'];
                    unset($_SESSION['ErrorStateLSVMessage']);
                  } ?>
                </div>
                <?php } ?>

                <form method="post" action="<?=autoUrl("")?>" name="loginform" id="loginform" class="needs-validation" novalidate>
                  <div class="form-group">
                    <label for="username">Email Address</label>
                    <input type="text" name="username" id="username" class="form-control form-control-lg" value="<?php if ($errorState == true) { echo $username; } ?>" required autofocus placeholder="yourname@example.com" autocomplete="email">
                    <div class="invalid-feedback">
                      Please enter a valid email address.
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password" autocomplete="current-password">
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
                  <input type="hidden" name="target" value="<?php echo app('request')->path; ?>">
                  <input type="hidden" name="LoginSecurityValue" value="<?=$lsv?>">
                  <input type="hidden" name="SessionSecurity" value="<?=session_id()?>">
                  <p class="mb-0"><input type="submit" name="login" id="login" value="Login" class="btn btn-lg btn-block btn-primary"></p>
                <!--<span class="small text-center d-block"><a href="register.php">Create an account</a></span>
                <span class="small text-center d-block"><a href="forgot-password.php">Forgot password?</a></span>-->
              </form>
            </div>
          </div>

        </div>
        <div class="col-md-5 ml-auto">
          <div class="p-3 my-2 mb-4 bg-white shadow rounded">
            <p>
              Not yet registered for an account? It's really easy to get started.
            </p>
            <a class="btn btn-dark mb-3" href="<?php echo autoUrl("register") ?>">Create an account</a>
            <p>
              Forgotten your password? We'll get you going again in no time. All we need is your email address.
            </p>
            <a class="btn btn-dark mb-3" href="<?php echo autoUrl("resetpassword") ?>">Forgot password?</a>
            <p class="small mb-0">Support Helpline: <a class="" href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a></p>
            <p class="small">Support Website: <a class="" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">CLS ASC Help and Support</a></p>
            <p class="small mb-0">Unauthorised access to or misuse of this
            system is prohibited and constitutes an offence under the Computer
            Misuse Act 1990. If you disclose any information obtained through
            this system without authority then <?=htmlspecialchars(env('CLUB_NAME'))?> or
            Chester-le-Street ASC Club Digital Services may take legal action
            against you.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-auto"></div>
</div>

<?php
  $detes = [$lsv, $_SESSION['LoginSec']];
  //pre($detes);
  //pre($_SESSION['InfoSec']);
  if ( isset($_SESSION['ErrorState']) ) {
    unset($_SESSION['ErrorState']);
  }
$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
?>
