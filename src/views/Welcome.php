<?php

$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-10 col-lg-8">

      <?php if (isset($_SESSION['PWA']) && $_SESSION['PWA']) { ?>
      <p class="lead">By proceeding to use this progressive web app you agree to our use of cookies.</p>
      <?php } ?>

      <h1>Welcome to the <?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?> Membership System</h1>
      <p class="lead mb-5">
        The <?=htmlspecialchars(app()->tenant->getKey('CLUB_NAME'))?> Online Membership System allows you to manage your swimmers, enter competitions, stay up to date by email and make payments by Direct Debit.
      </p>

      <h2>Already registered?</h2>
      <p class="lead">
        Log in to your account now
      </p>
      <p class="mb-5">
        <a class="btn btn-lg btn-primary" href="<?=autoUrl("login")?>">
          Login
        </a>
      </p>

      <h2>Not got an account?</h2>
      <p class="lead">
        Registration is quick and easy. You can sign up in a flash.
      </p>
      <p class="mb-5">
        <a class="btn btn-lg btn-dark" href="<?=autoUrl("register")?>">
          Register Now
        </a>
      </p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();