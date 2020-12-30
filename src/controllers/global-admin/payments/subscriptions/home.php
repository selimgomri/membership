<?php

$pagetitle = "Subscriptions - Payments - Admin Dashboard - SCDS";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('admin/payments')) ?>">Pay</a></li>
      <li class="breadcrumb-item active" aria-current="page">Subscriptions</li>
    </ol>
  </nav>

  <h1>
    Subscriptions
  </h1>
  <p class="lead">Automatic subscription and billing systems.</p>


</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();
