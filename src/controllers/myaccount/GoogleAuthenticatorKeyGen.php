<?php

$setup = false;
$secretKey = null;

$reset = false;
if (filter_var(getUserOption($_SESSION['UserID'], "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {
  $reset = true;
}

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

if (!isset($_SESSION['G2FAKey'])) {
  $_SESSION['G2FAKey'] = $google2fa->generateSecretKey(32);
}

$use_white_background = true;
$pagetitle = "Generate Key";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>2FA Key Generation</h1>
      <?php if ($reset) { ?>
      <p>
        Hi there. We're going to reset the codes you use for Google
        Authenticator. As soon as you confirmed setup below, any devices you've
        already set up with Google Authenticator (or a similar app) will stop
        working. This is essential to protect your security.
      </p>
      <?php } ?>
      <p>
        Your Secret Key is <span class="mono"><?=$_SESSION['G2FAKey']?></span>.
      </p>

      <h2>Scan Code</h2>
      <?php
      $qr_url = urlencode($google2fa->getQRCodeUrl(CLUB_NAME, $_SESSION['EmailAddress'], $_SESSION['G2FAKey']));
      ?>
      <img src="<?=autoUrl("services/qr-generator?size=200&margin=0&text=" . $qr_url)?>" srcset="<?=autoUrl("services/qr-generator?size=400&margin=0&text=" . $qr_url)?> 2x, <?=autoUrl("services/qr-generator?size=600&margin=0&text=" . $qr_url)?> 3x" class="img-fluid mb-3">
      <p>
        Scan this QR Code with your Authenticator App.
      </p>

      <h2>Confirm Setup</h2>
      <p>
        We're going to ask you to enter the code shown on your device. This
        verifies Google Authenticator has been set up correctly.
      </p>
      <?php if ($_SESSION['G2FA_VerifyError']) { ?>
      <div class="alert alert-danger">
        <strong>The code you entered was not valid</strong>
      </div>
      <?php } ?>
      <form method="post">
        <div class="form-group">
          <label for="verify">Verify Code</label>
          <input type="text" class="form-control" id="verify" name="verify" aria-describedby="verifyHelp" placeholder="123456" pattern="[0-9]*" inputmode="numeric">
          <small id="verifyHelp" class="form-text text-muted">This code is shown in your app.</small>
        </div>
      <p>
        <button class="btn btn-primary" type="submit">Verify</button>
      </p>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
