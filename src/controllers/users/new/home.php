<?php

$pagetitle = 'Create a new user';

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="<?= htmlspecialchars(autoUrl("users")) ?>">Users</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        Create
      </li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>
        Create a new user
      </h1>
      <p class="lead">
        We've removed the ability for users to create their own account. Instead, you can now provision users here.
      </p>

      <p>
        Members and volunteers can use a single account with multiple permissions assigned to them.
      </p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
