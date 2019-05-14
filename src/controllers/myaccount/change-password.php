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
<h1>Change your password</h1>
<p class="lead">
  You should change your password regularly to keep you account safe and secure.
</p>
<?php if (isset($_SESSION['ErrorState'])) {
  echo $_SESSION['ErrorState'];
  unset($_SESSION['ErrorState']);
} ?>
<form class="cell" method="post" action="password">
  <div class="form-group">
      <label for="current">Confirm Current Password</label>
      <input type="password" class="form-control" name="current" id="current" placeholder="Current Password" autocomplete="current-password">
   </div>
   <hr>
   <div class="form-group">
      <label for="new1">New Password</label>
      <input type="password" class="form-control" name="new1" id="new1" placeholder="New Password" autocomplete="new-password">
  </div>
   <div class="form-group">
      <label for="new2">Confirm New Password</label>
      <input type="password" class="form-control" name="new2" id="new2" placeholder="Confirm New Password" autocomplete="new-password">
  </div>
  <p><input type="submit" name="submit" id="submit" class="btn btn-success" value="Save Changes"></p>
</form>
<p class="mb-0">Changes will take place instantly, if everything is correct. You can change your password as often as you like.</p>
</div>
</div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
