<?php
  $pagetitle = "Change Your Email Address";
  include BASE_PATH . "views/header.php";
?>
<div class="container">
<div class="mb-3 p-3 bg-white rounded shadow">
<h1>Change your email address</h1>
<?php if (isset($_SESSION['ErrorState'])) {
  echo $_SESSION['ErrorState'];
  unset($_SESSION['ErrorState']);
} ?>
<form method="post" action="password">
	<p class="mb-0">Your email address is currently <?= $_SESSION['EmailAddress'] ?></p>
   <hr>
   <div class="form-group">
      <label for="newemail">New Email Address</label>
      <input type="newemail" class="form-control" name="newemail" id="newemail" placeholder="hello@example.com">
  </div>
  <p><input type="submit" name="submit" id="submit" class="btn btn-outline-dark" value="Save Changes"></p>
</form>
<p class="mb-0">Changes will take place instantly, if everything is correct. You can change your password as often as you like.</p>
</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
