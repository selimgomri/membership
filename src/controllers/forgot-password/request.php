<?php
  $use_white_background = true;
  $pagetitle = "Password Reset";
  $preventLoginRedirect = true;
  include BASE_PATH . "views/header.php";
?>
<div class="container">
  <div class="">
    <h1>Reset your password</h1>
    <form method="post">
      <div class="row">
        <div class="col-md-8 col-lg-5">
          <div class="form-group">
            <label for="email-address">
              Email Address
            </label>
            <input type="email" class="form-control" name="email-address"
            id="email-address" placeholder="hello@example.com" required>
           </div>
           <div class="g-recaptcha mb-3"
           data-sitekey="6Lc4U0AUAAAAAOM613z7FDK5rsyPVR_IT0iXgBSA"
           data-callback="enableBtn"></div>

          <p>
            <input type="submit" name="submit" id="submit" class="btn
            btn-primary btn-lg" value="Request Password Change">
          </p>

          <p class="mb-0">
            If an account exists with email address you submit, we will send you
            a link by email to reset your password.
          </p>
        </div>
      </div>
    </form>
    <script>
    function enableBtn(){
      document.getElementById("submit").disabled = false;
     }
    document.getElementById("submit").disabled = true;
    </script>
  </div>
</div>
<script src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>
<?php include BASE_PATH . "views/footer.php"; ?>
