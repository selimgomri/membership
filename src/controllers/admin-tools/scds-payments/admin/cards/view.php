<?php

$db = app()->db;
$tenant = app()->adminCurrentTenant;
$user = app()->adminCurrentUser;

$getPaymentMethods = $db->prepare("SELECT tenantPaymentMethods.ID, `MethodID`, `MandateID`, `Type`, `TypeData`, `AcceptanceData`, `MethodDetails`, `Usable`, `Status` FROM tenantPaymentMethods LEFT JOIN tenantPaymentMandates ON tenantPaymentMandates.PaymentMethod = tenantPaymentMethods.MethodID INNER JOIN tenantStripeCustomers ON tenantPaymentMethods.Customer = tenantStripeCustomers.CustomerID WHERE Tenant = ? AND `Type` = 'card' AND `Usable` AND tenantPaymentMethods.ID = ? ORDER BY Usable DESC, tenantPaymentMethods.Created DESC");
$getPaymentMethods->execute([
  $tenant->getId(),
  $id
]);
$paymentMethod = $getPaymentMethods->fetch(PDO::FETCH_ASSOC);

if (!$paymentMethod) {
  halt(404);
}

$typeData = json_decode($paymentMethod['TypeData']);

$pagetitle = htmlspecialchars(mb_convert_case($typeData->brand, MB_CASE_TITLE) . ' ' . $typeData->last4) . " - Payments - SCDS";

$default = $tenant->getKey('DEFAULT_PAYMENT_MANDATE');

include BASE_PATH . "views/root/head.php";

?>

<div class="container">

  <?php include BASE_PATH . 'controllers/admin-tools/scds-payments/admin/nav.php'; ?>

  <div class="row align-items-center">
    <div class="col-auto">
      <div class="bg-primary text-white p-4 my-4 d-inline-block rounded">
        <h1 class=""><?= htmlspecialchars(getCardBrand($typeData->brand)) ?> &#0149;&#0149;&#0149;&#0149; <?= htmlspecialchars($typeData->last4) ?></h1>
        <p class="lead mb-0"><?= htmlspecialchars(mb_convert_case($typeData->funding, MB_CASE_TITLE)) ?> card</p>
      </div>
    </div>
    <aside class="col-auto ml-auto">
      <img src="<?= htmlspecialchars(autoUrl("public/img/stripe/" . $typeData->brand . ".png")) ?>" srcset="<?= htmlspecialchars(autoUrl("public/img/stripe/" . $typeData->brand . "@2x.png")) ?> 2x, <?= htmlspecialchars(autoUrl("public/img/stripe/" . $typeData->brand . "@3x.png")) ?> 3x" style="height:2.5rem;">
    </aside>
  </div>

  <div class="row">
    <div class="col-lg-8">

      <h2>
        Payments with this card
      </h2>

      <div class="alert alert-info">
        <p class="mb-0">
          <strong>No transaction history is available</strong>
        </p>
        <p class="mb-0">
          This card has never been used for any payments
        </p>
      </div>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();

?>