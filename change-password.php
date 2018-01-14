<?php
  $pagetitle = "Change Password";
  include "header.php";
?>
<div class="container">
<h1>Change your password</h1>
<form method="post" action="change-password-action.php">
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
  <p><input type="submit" name="submit" id="submit" class="btn btn-success" value="Save Changes"></p>
</form>
<p>Changes will take place instantly, if everything is correct. You can change your password as often as you like.</p>
</div>

<?php include "footer.php"; ?>
