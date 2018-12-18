<?php
  $use_white_background = true;
  $pagetitle = "Password Reset";
  $preventLoginRedirect = true;
  include BASE_PATH . "views/header.php";
?>
<div class="container">
  <div class="">
    <h1>Lost password</h1>
    <form method="post">
      <div class="row">
        <div class="col-md-8 col-lg-5">
          <div class="form-group">
            <label for="userDetails">
              Username or Email Address
            </label>
            <input type="text" class="form-control" name="userDetails"
            id="userDetails" placeholder="Username or Email" required>
           </div>
           <div class="g-recaptcha mb-3"
           data-sitekey="6Lc4U0AUAAAAAOM613z7FDK5rsyPVR_IT0iXgBSA"
           data-callback="enableBtn"></div>
        </div>
      </div>
      <p>
        <input type="submit" name="submit" id="submit" class="btn btn-primary
        btn-lg" value="Request Password Change">
      </p>
    </form>
    <script>
    function enableBtn(){
      document.getElementById("submit").disabled = false;
     }
    document.getElementById("submit").disabled = true;
    </script>
    <p class="mb-0">
      If an account exists with either the username or email address you submit,
      we will send you a link by email to reset your password.
    </p>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
