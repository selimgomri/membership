<?php
  http_response_code(401);

  $pagetitle = "Login";
  include BASE_PATH . "views/header.php";

  $errorState = false;

  if ( isset($_SESSION['ErrorState']) ) {
    $errorState = $_SESSION['ErrorState'];
    $username = $_SESSION['EnteredUsername'];
  }

  ?>
<div class="frontpage1 d-flex flex-column" style="margin:-1.0rem 0;">
  <div class="mb-auto"></div>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-sm-8 col-md-5 col-lg-6 col-xl-4 align-middle">
        <div class="my-3 bg-dark rounded shadow">
          <div class="m-0 p-3 bg-white rounded-top">
            <h2 class="border-bottom border-gray pb-2 mb-0">Please Login</h2>
            <div class="text-dark pt-3">
              <!--<div class="alert alert-warning">
                <strong>Overnight System Maintenance</strong> <br>
                Our systems will not be sending emails between 23:00 on Friday 31 August and 15:00 on Saturday 1 September
              </div>-->
                <?php if ($errorState == true) { ?>
                <div class="alert alert-danger">
                  <strong>Incorrect details</strong> <br>
                  Please try again
                </div>
                <?php } ?>

                <form method="post" action="<?php echo autoUrl(""); ?>" name="loginform" id="loginform">
                  <div class="form-group">
                    <label for="username">Email Address</label>
                    <input type="text" name="username" id="username" class="form-control form-control-lg" value="<?php if ($errorState == true) { echo $username; } ?>" required autofocus placeholder="yourname@example.com">
                  </div>
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control form-control-lg" required placeholder="Password">
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
                  <p><input type="submit" name="login" id="login" value="Login" class="btn btn-lg btn-block btn-primary"></p>
                  <div class="row mb-0">
                    <div class="col">
                      <a class="btn btn-block btn-dark" href="<?php echo autoUrl("register") ?>">Create an account</a>
                    </div>
                    <div class="col">
                      <a class="btn btn-block btn-dark" href="<?php echo autoUrl("resetpassword") ?>">Forgot password?</a>
                    </div>
                  </div>
                <!--<span class="small text-center d-block"><a href="register.php">Create an account</a></span>
                <span class="small text-center d-block"><a href="forgot-password.php">Forgot password?</a></span>-->
              </form>
            </div>
          </div>
          <div class="p-3 text-white">
            <p class="small mb-0">Support Helpline: <a class="text-white" href="mailto:support@chesterlestreetasc.co.uk">support@chesterlestreetasc.co.uk</a></p>
            <p class="small mb-0">Support Website: <a class="text-white" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership">CLS ASC Help and Support</a></p>
            <!--<p class="small mb-0">Unauthorised access to or misuse of this system is prohibited and constitutes an offence under the Computer Misuse Act 1990. If you disclose any information obtained through this system without authority CLS ASC may take legal action against you.</p>-->
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="mt-auto"></div>
</div>
<?php

  if ( isset($_SESSION['ErrorState']) ) {
    unset($_SESSION['ErrorState']);
  }
  include BASE_PATH . "views/footer.php";
?>
