<?php

$pagetitle = "Provide User Details - Assisted Registration";

$first = "";
$last = "";
$email = "";
$mobile = "";

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData'])) {
  $first = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData']['first'];
  $last = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData']['last'];
  $email = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData']['email-address'];
  $mobile = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData']['phone'];
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegPostData']);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container-xl">
  <div class="row">
    <div class="col-md-8">
      <h1>Provide user details</h1>
      <p class="lead">
        Tell us the following...
      </p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) { ?>
      <div class="alert alert-warning">
        <strong>There was a problem with some of the data supplied</strong>
      </div>
      <?php } ?>

      <form method="post" class="needs-validation" novalidate>

        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label class="form-label" for="first">First name</label>
              <input type="text" class="form-control" id="first" name="first" placeholder="First" required value="<?=htmlspecialchars($first)?>">
              <div class="invalid-feedback">
                Please enter a first name.
              </div>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label class="form-label" for="last">Last name</label>
              <input type="text" class="form-control" id="last" name="last" placeholder="Last" required value="<?=htmlspecialchars($last)?>">
              <div class="invalid-feedback">
                Please enter a last name.
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="email-address">Email Address</label>
          <input disabled type="email" class="form-control text-lowercase" id="email-address" name="email-address" placeholder="name@example.com" required value="<?=htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUserEmail'])?>">
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="phone">Mobile Number</label>
          <input type="tel" pattern="\+{0,1}[0-9]*" class="form-control" id="phone" name="phone" placeholder="01234 567891" required value="<?=htmlspecialchars($mobile)?>">
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

<?php

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']);
}

$footer = new \SCDS\Footer();
$footer->addJS("js/NeedsValidation.js");
$footer->render();