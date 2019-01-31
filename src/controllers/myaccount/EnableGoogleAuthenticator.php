<?php

$setup = false;
$secretKey = null;

use PragmaRX\Google2FA\Google2FA;
$ga2fa = new Google2FA();

if (filter_var(getUserOption($_SESSION['UserID'], "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {
  $setup = true;
  $secretKey = getUserOption($_SESSION['UserID'], "GoogleAuth2FASecret");
}

$use_white_background = true;
$pagetitle = "Google Authenticator";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <?php if (!$setup) { ?>
      <h1>Setup Google Authenticator for 2FA</h1>
      <p class="lead">
        Google Authenticator is a multifactor app for mobile devices that
        generates timed codes used during the Two-Step Verification process.
      </p>
      <p>
        <a href="<?=autoUrl("myaccount/googleauthenticator/setup")?>" class="btn btn-primary btn-lg">
          Set up Google Authenticator
        </a>
      </p>
      <?php } else { ?>
      <h1>Your Google Authenticator Options</h1>
      <p class="lead">
        Welcome to your App Based 2FA Options
      </p>

      <h2>Need to set up your device again?</h2>
      <p>
        We can quickly generate a new token for you to use. This will stop any
        previous devices that you have set up from working.
      </p>
      <p>
        <a href="<?=autoUrl("myaccount/googleauthenticator/setup")?>" class="btn btn-dark">
          Setup Again
        </a>
      </p>

      <h2>Disable Google Authenticator</h2>
      <p>
        It's easy to switch back to Email Only Two Factor Authentication at any
        time. To disable 2FA entirely, head to Account Options.
      </p>
      <p>
        <a href="<?=autoUrl("myaccount/googleauthenticator/disable")?>" class="btn btn-dark">
          Disable Google Authenticator 2FA
        </a>
        <a href="<?=autoUrl("myaccount/general")?>" class="btn btn-dark">
          Disable All 2FA
        </a>
      </p>
      <?php } ?>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
