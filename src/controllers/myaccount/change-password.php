<?php
  $fluidContainer = true;
  $pagetitle = "Change Password";
  include BASE_PATH . "views/header.php";
?>
<div class="container-fluid">
  <div class="row justify-content-between">
    <div class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
        echo $list->render('password');
      ?>
    </div>
    <div class="col-md-9">
<?php

if (isset($_SESSION['PasswordUpdate']) && $_SESSION['PasswordUpdate']) { ?>
  <div class="alert alert-success">
    <strong>You've successfully changed your password</strong>
  </div>
<?php unset($_SESSION['PasswordUpdate']); } ?>

<h1>Change your password</h1>
<p class="lead">
  You should change your password regularly to keep you account safe and secure.
</p>
<?php if (isset($_SESSION['ErrorState'])) {
  echo $_SESSION['ErrorState'];
  unset($_SESSION['ErrorState']);
} ?>
<div class="alert alert-warning">
  <p class="mb-0">Passwords must consist of at least 8 characters.</p>
</div>
<form class="cell needs-validation" method="post" action="<?=htmlspecialchars(autoUrl("my-account/password"))?>" novalidate>
  <?=SCDS\CSRF::write()?>
  <div class="form-group">
    <label for="current">Confirm your current password</label>
    <input type="password" class="form-control" name="current" id="current" placeholder="Current Password" autocomplete="current-password" required>
    <div class="invalid-feedback">
      Please enter your current password.
    </div>
  </div>
  <div class="form-row">
    <div class="col-sm">
      <div class="form-group">
        <label for="new1">New password</label>
        <input type="password" class="form-control" name="new1" id="new1" placeholder="New Password" autocomplete="new-password" minlength="8" required>
        <div class="invalid-feedback">
          Please enter a password that is at least 8 characters in length.
        </div>
      </div>
    </div>
    <div class="col-sm">
      <div class="form-group">
        <label for="new2">Confirm new password</label>
        <input type="password" class="form-control" name="new2" id="new2" placeholder="Confirm New Password" autocomplete="new-password" minlength="8" required>
        <div class="invalid-feedback">
          Please enter a password that is at least 8 characters in length.
        </div>
      </div>
    </div>
  </div>
  <p><input type="submit" name="submit" id="submit" class="btn btn-success" value="Change password"></p>
</form>
</div>
</div>
</div>

<?php $footer = new \SDCS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->useFluidContainer();
$footer->render(); ?>
