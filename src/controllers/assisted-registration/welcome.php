<?php

$pagetitle = "Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">

      <h1>Welcome to Assisted Registration</h1>
      <p class="lead">
        Assisted registration allows you to create an account for new parents and automatically connect their swimmers to it.
      </p>

      <p>
        Parents will be led through a registration process to check their details and fill out required forms when they first log in.
      </p>

      <p>
        To get started you will need;
      </p>

      <ul>
        <li>
          The parent's name,
        </li>
        <li>
          Their email address,
        </li>
        <li>
          Their phone number
        </li>
      </ul>

      <p>
        Given these details, we'll then ask you to select swimmers from a list of all unregistered swimmers. We'll send the parent an email which includes instructions on how to log in.
      </p>

      <h2 id="get-started">Get started</h2>
      <p class="lead">First we'll ask you for the parent's email address.</p>
      <p>This let's us check if they already have an account. If they don't we'll ask you some more details and make a new account. If they do, we'll take you straight to the select swimmers page.</p>

      <?php if (isset($_SESSION['AssRegEmailError'])) { ?>
      <div class="alert alert-danger">
        <?php if ($_SESSION['AssRegEmailError'] == 'INV-EMAIL') { ?>
        <strong>The email address provided was not valid</strong>
        <?php } else { ?>
        <strong>A user already exists but is not a parent account</strong>
        <?php } unset($_SESSION['AssRegEmailError']); ?>
      </div>
      <?php } ?>

      <form method="post">
        <?=\SCDS\CSRF::write()?>
        <div class="form-group">
          <label for="email-address">Parent email address</label>
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

include BASE_PATH . 'views/footer.php';