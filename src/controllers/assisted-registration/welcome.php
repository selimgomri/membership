<?php

$pagetitle = "Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container-xl">
  <div class="row">
    <div class="col-md-8">

      <h1>Welcome to Assisted Registration</h1>
      <p class="lead">
        Assisted registration allows you to create an account for new parents/members and automatically connect their members to it.
      </p>
      
      <div class="alert alert-warning" role="alert">
        <p class="mb-0"><strong>Assisted registration will soon be removed from the membership system</strong></p>
        <p class="mb-0">We're replacing it with a new onboarding tool which will deliver a much better experience for members and administrators.</p>
      </div>
      
      <div class="alert alert-danger" role="alert">
        <p class="mb-0"><strong>Assisted registration no longer automatically applies discounts to complex registration fees when adding a member to an existing account</strong></p>
        <p class="mb-0">If you charge a family rate for club membership, the membership system won't apply a discount automatically. Ask an administrator to apply an invoice payment (credit) to the user's account once they have completed registration. The new onboarding tool will soon restore this functionality.</p>
      </div>

      <p>
        Members will be led through a registration process to check their details and fill out required forms when they first log in.
      </p>

      <p>
        To get started you will need;
      </p>

      <ul>
        <li>
          The parent/member's name,
        </li>
        <li>
          Their email address,
        </li>
        <li>
          Their phone number
        </li>
      </ul>

      <p>
        Given these details, we'll then ask you to select members from a list of all unregistered members. We'll send the user an email which includes instructions on how to log in.
      </p>

      <h2 id="get-started">Get started</h2>
      <p class="lead">First we'll ask you for the member's email address.</p>
      <p>This let's us check if they already have an account. If they don't we'll ask you some more details and make a new account. If they do, we'll take you straight to the select members page.</p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegEmailError'])) { ?>
      <div class="alert alert-danger">
        <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegEmailError'] == 'INV-EMAIL') { ?>
        <strong>The email address provided was not valid</strong>
        <?php } else { ?>
        <strong>A user already exists but is not a parent account</strong>
        <?php } unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegEmailError']); ?>
      </div>
      <?php } ?>

      <form method="post">
        <?=\SCDS\CSRF::write()?>
        <div class="mb-3">
          <label class="form-label" for="email-address">Parent email address</label>
          <input type="email" class="form-control" id="email-address" name="email-address" placeholder="Enter email">
        </div>
        <p>
          <button type="submit" class="btn btn-primary">
            Get started
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
