<?php

$db = app()->db;
$tenant = app()->tenant;

$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, RR FROM users WHERE Tenant = ? AND UserID = ? AND Active");
$userInfo->execute([
  $tenant->getId(),
  $id
]);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

// Get Stripe direct debit info
$getStripeDD = $db->prepare("SELECT stripeMandates.ID, Mandate, Last4, SortCode, `Address`, Reference, `URL`, `Status` FROM stripeMandates INNER JOIN stripeCustomers ON stripeMandates.Customer = stripeCustomers.CustomerID WHERE stripeCustomers.User = ? AND (`Status` = 'accepted' OR `Status` = 'pending') ORDER BY CreationTime DESC LIMIT 1;");
if (stripeDirectDebit()) {
  $getStripeDD->execute([
    $id
  ]);
}
$stripeDD = $getStripeDD->fetch(PDO::FETCH_ASSOC);

$bankName = $bank = $has_logo = $logo_path = null;
$hasGC = false;
if (userHasMandates($id)) {
  $bankName = mb_strtoupper(bankDetails($id, "account_holder_name"));
  if ($bankName != "UNKNOWN") {
    $bankName = $bankName . ', ';
  } else {
    $bankName = null;
  }
  $bank = mb_strtoupper(bankDetails($id, "bank_name"));
  $logo_path = getBankLogo($bank);
  $hasGC = true;
}

$getPending = $db->prepare("SELECT `PaymentID` `id`, `Date` `date`, `Name` `name`, `Amount` `amount`, `Currency` `currency`, `Type` `type` FROM `paymentsPending` WHERE `UserID` = ? AND `PMkey` IS NULL AND `Status` = 'Pending' ORDER BY `Date` DESC");
$getPending->execute([$id]);
$item = $getPending->fetch(PDO::FETCH_OBJ);

?>

<p>
  We can start an early Direct Debit payment collection for <?= htmlspecialchars($info['Forename']) ?>. This allows you to take additional fees earlier than scheduled.
</p>

<?php if ($item) { ?>

  <p>
    Please select the pending payment items you would like to include in this Direct Debit charge.
  </p>

  <p>
    The minimum (total) amount you may charge is &pound;1 (1GBP). This is due to Direct Debit scheme rules.
  </p>

  <ul class="list-group rounded-0">
    <?php do {

      $amount = $item->amount;
      if ($item->type == 'Refund') $amount = 0 - $amount;
      
      ?>
      <li class="list-group-item">
        <div class="row">
          <div class="col">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
              <label class="form-check-label fw-bold" for="flexCheckDefault">
                <div><?= htmlspecialchars($item->name) ?></div>
                <div><?= htmlspecialchars(MoneyHelpers::formatCurrency((float) MoneyHelpers::intToDecimal($amount), $item->currency)) ?></div>
              </label>
            </div>
          </div>
          <div class="col text-end">
            <?= htmlspecialchars((new DateTime($item->date, new DateTimeZone('Europe/London')))->format('d/m/Y')) ?>
          </div>
        </div>
      </li>
    <?php } while ($item = $getPending->fetch(PDO::FETCH_OBJ)); ?>
    <li class="list-group-item bg-light fw-bold">
      Total &pound;<span id="early-total">0.00</span>
    </li>
  </ul>

<?php } else { ?>

  <div class="alert alert-warning">
    <p class="mb-0">
      <strong>There are no pending payments for <?= htmlspecialchars($info['Forename']) ?></strong>
    </p>
    <p class="mb-0">
      Please <a class="alert-link" href="<?= htmlspecialchars(autoUrl("payments/invoice-payments/new")) ?>">add invoice payment items</a>.
    </p>
  </div>

<?php } ?>