<?php

$setup = false;
$secretKey = null;

$reset = false;
if (filter_var(getUserOption($_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], "hasGoogleAuth2FA"), FILTER_VALIDATE_BOOLEAN)) {
  $reset = true;
}

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['G2FAKey'])) {
  $_SESSION['TENANT-' . app()->tenant->getId()]['G2FAKey'] = $google2fa->generateSecretKey(32);
}

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
        Your Secret Key is <span class="mono"><?= $_SESSION['TENANT-' . app()->tenant->getId()]['G2FAKey'] ?></span>.
      </p>

      <h2>Scan Code</h2>
      <?php
      $qr_url = urlencode($google2fa->getQRCodeUrl(app()->tenant->getKey('CLUB_NAME'), $_SESSION['TENANT-' . app()->tenant->getId()]['EmailAddress'], $_SESSION['TENANT-' . app()->tenant->getId()]['G2FAKey']));
      ?>
      <img src="<?= htmlspecialchars(autoUrl("services/qr-generator?size=200&margin=0&text=" . $qr_url)) ?>" srcset="<?= htmlspecialchars(autoUrl("services/qr-generator?size=400&margin=0&text=" . $qr_url)) ?> 2x, <?= htmlspecialchars(autoUrl("services/qr-generator?size=600&margin=0&text=" . $qr_url)) ?> 3x" class="img-fluid mb-3">
      <p>
        Scan this QR Code with your Authenticator App.
      </p>

      <h2>Confirm Setup</h2>
      <p>
        We're going to ask you to enter the code shown on your device. This
        verifies Google Authenticator has been set up correctly.
      </p>
      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['G2FA_VerifyError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['G2FA_VerifyError']) { ?>
        <div class="alert alert-danger">
          <strong>The code you entered was not valid</strong>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['G2FA_VerifyError']);
      } ?>
      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="verify">Verify Code</label>
          <input type="text" class="form-control" id="verify" name="verify" aria-describedby="verifyHelp" placeholder="654321" pattern="[0-9]*" inputmode="numeric" min="0" max="999999" step="1" required>
          <div class="invalid-feedback">
            You must enter a valid code
          </div>
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

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
