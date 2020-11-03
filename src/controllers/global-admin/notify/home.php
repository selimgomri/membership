<?php

if (!isset($_SESSION['SCDS-SuperUser'])) {
  http_response_code(302);
  header("location: " . autoUrl('admin/login'));
  return;
}

$pagetitle = "Notify - Admin Dashboard - SCDS";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Notify</li>
    </ol>
  </nav>

  <h1>
    Notify System
  </h1>
  <p class="lead">Notify is our <strong>GDPR Compliant</strong> email system for contacting parents and users.</p>

  <?php if (isset($_SESSION['SCDS-Notify']['Success'])) { ?>
    <div class="alert alert-success">
      <p class="mb-0">
        <strong>Message sent successfully</strong>
      </p>
    </div>
  <?php unset($_SESSION['SCDS-Notify']['Success']);
  } ?>

  <ul>
    <li>
      <a href="<?= htmlspecialchars(autoUrl('admin/notify/history')) ?>">Message History</a>
    </li>
    <li>
      <a href="<?= htmlspecialchars(autoUrl('admin/notify/composer')) ?>">New Email</a>
    </li>
  </ul>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();
