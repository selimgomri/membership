<?php
  $pagetitle = "Password Reset";
  $preventLoginRedirect = true;
  include "header.php";
?>
<div class="container">
<h1>Lost password</h1>
<form method="post" action="forgot-password-action.php">
  <div class="form-group">
      <label for="userDetails">Username or Email Address</label>
      <input type="text" class="form-control" name="userDetails" id="userDetails" placeholder="Username or Email" required>
   </div>
   <div class="g-recaptcha mb-3" data-sitekey="6Lc4U0AUAAAAAOM613z7FDK5rsyPVR_IT0iXgBSA" data-callback="enableBtn"></div>
  <p><input type="submit" name="submit" id="submit" class="btn btn-outline-dark" value="Request Password Change"></p>
</form>
<script>
  function enableBtn(){
    document.getElementById("submit").disabled = false;
   }
  document.getElementById("submit").disabled = true;
</script>
<p>If an account exists with either the username or email address you submit, we will send you a new password by email.</p>
</div>

<?php include "footer.php"; ?>
