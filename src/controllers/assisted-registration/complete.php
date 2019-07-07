<?php

$pagetitle = "Success - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Success</h1>
      <p class="lead">
        We've created the account and added the swimmers.
      </p>

      <p>
        An email is on it's way to <?=htmlspecialchars($_SESSION['AssRegName'])?> which includes instructions to set a password.
      </p>
    </div>
  </div>
</div>

<?php

unset($_SESSION['AssRegName']);
unset($_SESSION['AssRegUser']);
unset($_SESSION['AssRegPass']);
unset($_SESSION['AssRegComplete']);

include BASE_PATH . 'views/footer.php';