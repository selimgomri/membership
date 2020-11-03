<?php

if (!isset($_SESSION['SCDS-SuperUser'])) {
  http_response_code(302);
  header("location: " . autoUrl('admin/login'));
  return;
}

$pagetitle = "Admin Dashboard - SCDS";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">
  <div class="row justify-content-center py-3">
    <div class="col-lg-8 col-md-10">
    <div class="bg-primary text-white p-4 mb-4 d-inline-block rounded">
      <h1 class="">Administration Dashboard</h1>
      <p class="mb-0">Select a utility.</p>
      </div>

      <div class="list-group">
        <a href="<?= htmlspecialchars(autoUrl('admin/register')) ?>" class="list-group-item list-group-item-action">
          Add Tenant
        </a>
        <a href="<?= htmlspecialchars(autoUrl('admin/notify')) ?>" class="list-group-item list-group-item-action">
          Notify Usage
        </a>
      </div>

      
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();

?>