<?php

$pagetitle = "Provide User Details - Assisted Registration";

$first = "";
$last = "";
$email = "";
$mobile = "+44";

if (isset($_SESSION['AssRegPostData'])) {
  $first = $_SESSION['AssRegPostData']['first'];
  $last = $_SESSION['AssRegPostData']['last'];
  $email = $_SESSION['AssRegPostData']['email-address'];
  $mobile = "+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", trim($_SESSION['AssRegPostData']['phone']))), '0');
  unset($_SESSION['AssRegPostData']);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Provide user details</h1>
      <p class="lead">
        Tell us the following...
      </p>

      <?php if (isset($_SESSION['AssRegFormError']) && $_SESSION['AssRegFormError']) { ?>
      <div class="alert alert-warning">
        <strong>There was a problem with some of the data supplied</strong>
      </div>
      <?php } ?>

      <form method="post">

        <div class="form-row">
          <div class="col">
            <div class="form-group">
              <label for="first">First name</label>
              <input type="text" class="form-control" id="first" name="first" placeholder="First" required value="<?=htmlspecialchars($first)?>">
              <div class="invalid-feedback">
                Please enter a first name.
              </div>
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label for="last">Last name</label>
              <input type="text" class="form-control" id="last" name="last" placeholder="Last" required value="<?=htmlspecialchars($last)?>">
              <div class="invalid-feedback">
                Please enter a last name.
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="email-address">Email Address</label>
          <input type="email" class="form-control text-lowercase" id="email-address" name="email-address" placeholder="name@example.com" required value="<?=htmlspecialchars($email)?>">
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>

        <div class="form-group">
          <label for="phone">Mobile Number</label>
          <input type="tel" class="form-control" id="phone" name="phone" value="+44" required value="<?=htmlspecialchars($mobile)?>">
          <div class="invalid-feedback">
            Please enter a valid mobile number.
          </div>
        </div>

        <p>
          <button class="btn btn-success" type="submit">
            Continue
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

if (isset($_SESSION['AssRegFormError'])) {
  unset($_SESSION['AssRegFormError']);
}

include BASE_PATH . 'views/footer.php';