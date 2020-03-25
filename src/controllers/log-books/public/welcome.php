<?php

$pagetitle = "Member log books";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <h1>Log books <span class="badge badge-info">BETA</span></h1>
  <p class="lead">
    Members can log training sessions and other activity.
  </p>

  <p>
    Are you a parent/guardian/primary account holder?
  </p>

  <p>
    <a href="<?=htmlspecialchars(autoUrl("login"))?>" class="btn btn-primary">
      Login
    </a>
  </p>

  <p>
    Are you a member? (such as a swimmer, diver or water polo player)
  </p>

  <p>
    <a href="<?=htmlspecialchars(autoUrl("log-books/login"))?>" class="btn btn-primary">
      Login
    </a>
  </p>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();