<?php
  $use_white_background = true;
  $pagetitle = "Change Password";
  include BASE_PATH . "views/header.php";
?>
<div class="container">
<div class="">
<h1>Change your password</h1>
<? if (isset($_SESSION['ErrorState'])) {
  echo $_SESSION['ErrorState'];
  unset($_SESSION['ErrorState']);
} ?>
<form method="post" action="password">
  <div class="form-group">
      <label for="current">Confirm Current Password</label>
      <input type="password" class="form-control" name="current" id="current" placeholder="Current Password">
   </div>
   <hr>
   <div class="form-group">
      <label for="new1">New Password</label>
      <input type="password" class="form-control" name="new1" id="new1" placeholder="New Password">
  </div>
   <div class="form-group">
      <label for="new2">Confirm New Password</label>
      <input type="password" class="form-control" name="new2" id="new2" placeholder="Confirm New Password">
  </div>
  <p><input type="submit" name="submit" id="submit" class="btn btn-outline-dark" value="Save Changes"></p>
</form>
<p class="mb-0">Changes will take place instantly, if everything is correct. You can change your password as often as you like.</p>
</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
