<?php

$_SESSION['AddNotifyCC'] = [
  'AuthCode'          => random_int(100000, 999999),
  'Name'              => $_POST['new-cc-name'],
  'EmailAddress'      => $_POST['new-cc']
];

$message = '
<p>Please use the code below to verify your email address.</p>
<p><strong>' . $_SESSION['AddNotifyCC']['AuthCode'] . '</strong></p>
<p>If you did not request this authorisation code, please ignore this email.</p>';

if (!notifySend(null, "Verify your email", $message, $_SESSION['AddNotifyCC']['Name'], $_SESSION['AddNotifyCC']['EmailAddress'])) {
  halt(500);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h2>Verify your email address</h2>
      <p class="lead">
        We've sent a code to that email address. Please enter that code below.
      </p>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="auth">Authentication Code</label>
          <input type="number" name="auth" id="auth" class="form-control form-control-lg" required="" autofocus="" placeholder="654321" pattern="[0-9]*" inputmode="numeric">
          <div class="invalid-feedback">
            Please enter a numeric authentication code.
          </div>
        </div>

        <button class="btn btn-lg btn-primary" type="submit">
          Verify
        </button>
      </form>
    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';
