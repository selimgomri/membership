<?php

$use_white_background = true;

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-10 col-lg-8">
      <h1>Welcome to the <?=CLUB_NAME?> Membership System</h1>
      <p class="lead mb-5">
        The <?=CLUB_NAME?> Online Membership System allows you to manage your
        swimmers, enter competitions, stay up to date by email and make payments
        by Direct Debit.
      </p>

      <h2>Already Registered?</h2>
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

include BASE_PATH . "views/footer.php";

?>
