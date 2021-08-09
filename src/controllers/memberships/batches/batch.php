<?php

$user = app()->user;
$db = app()->db;

$getBatch = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
$getBatch->execute([
  $id,
  app()->tenant->getId(),
]);

$batch = $getBatch->fetch(PDO::FETCH_OBJ);

if (!$batch) halt(404);

// Get batch items
$getBatchItems = $db->prepare("SELECT membershipBatchItems.ID id, membershipBatchItems.Membership membershipId, membershipBatchItems.Amount amount, membershipBatchItems.Notes notes, members.MForename firstName, members.MSurname lastName, members.ASANumber ngbId, clubMembershipClasses.Type membershipType, clubMembershipClasses.Name membershipName, clubMembershipClasses.Description membershipDescription FROM membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership WHERE Batch = ?");
$getBatchItems->execute([
  $id
]);
$item = $getBatchItems->fetch(PDO::FETCH_OBJ);

$payMethods = json_decode($batch->payMethods);

$markdown = new \ParsedownExtra();
$markdown->setSafeMode(true);

$pagetitle = "Batch " . htmlspecialchars($id) . " - Membership Centre";
include BASE_PATH . "views/header.php";

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container-xl">

    <!-- Page header -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page"><a href="<?= htmlspecialchars(autoUrl("memberships")) ?>">Memberships</a></li>
        <li class="breadcrumb-item active" aria-current="page">Batch</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          Batch for <?= htmlspecialchars($batch->yearName) ?>
        </h1>
        <p class="lead mb-0">
          <?= htmlspecialchars($id) ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container-xl">

  <div class="row">
    <div class="col-lg-8">

      <h2>Batch Details</h2>

      <dl class="row">

        <dt class="col-3">
          Batch ID
        </dt>
        <dd class="col-9">
          <?= htmlspecialchars($batch->id) ?>
        </dd>

        <dt class="col-3">
          Period
        </dt>
        <dd class="col-9">
          <?= htmlspecialchars((new DateTime($batch->yearStart))->format('j F Y')) ?> to <?= htmlspecialchars((new DateTime($batch->yearEnd))->format('j F Y')) ?>
        </dd>

        <dt class="col-3">
          Amount
        </dt>
        <dd class="col-9">
          <?= htmlspecialchars(MoneyHelpers::formatCurrency(MoneyHelpers::intToDecimal($batch->total), 'GBP')) ?>
        </dd>

        <dt class="col-3">
          Pay by
        </dt>
        <dd class="col-9">
          <?php if (sizeof($payMethods) > 0) { ?>
            <ul class="mb-0">
              <?php if (in_array('card', $payMethods)) { ?>
                <li>Credit/debit card</li>
              <?php } ?>
              <?php if (in_array('dd', $payMethods)) { ?>
                <li>Next Direct Debit payment</li>
              <?php } ?>
            <?php } else { ?>
              No payment methods - speak to club staff
            <?php } ?>
        </dd>
      </dl>

      <?php if ($batch->total > 0 && sizeof($payMethods) > 0) { ?>
        <form method="post">
          <h2>Pay</h2>
          <?php if (sizeof($payMethods) > 1) { ?>
            <!-- Select payment method -->
            <p class="mb-2">Choose a payment method</p>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="pay-method" id="pay-card" value="1" <?php if (!in_array('card', $payMethods)) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="pay-card">
                  Credit/debit card
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="pay-method" id="pay-dd" value="1" <?php if (!in_array('card', $payMethods)) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="pay-dd">
                  Next Direct Debit payment
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="pay-method" id="pay-dd" value="1" <?php if (!in_array('cash', $payMethods)) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="pay-dd">
                  Cash
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="pay-method" id="pay-dd" value="1" <?php if (!in_array('cheque', $payMethods)) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="pay-dd">
                  Cheque
                </label>
              </div>

              <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="pay-method" id="pay-dd" value="1" <?php if (!in_array('bacs', $payMethods)) { ?>disabled<?php } ?>>
                <label class="form-check-label" for="pay-dd">
                  Bank transfer
                </label>
              </div>
            </div>
          <?php } else if (sizeof($payMethods) > 0) { ?>
            <!-- Just go straight to payment -->
            <p>You can only pay for this batch with PAY_METHOD</p>
          <?php } ?>
          <p class="d-grid mb-1">
            <button type="submit" class="btn btn-success">Pay for memberships <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
          </p>
          <p class="small text-muted">With SCDS Checkout</p>
        </form>
      <?php } ?>

      <h2>Memberships</h2>

      <?php if ($item) { ?>
        <ul class="list-group mb-3">
          <?php do { ?>
            <li class="list-group-item">
              <h3><?= htmlspecialchars($item->firstName . ' ' . $item->lastName) ?></h3>
              <p class="lead"><?= htmlspecialchars($item->membershipName) ?></p>

              <dl class="row">
                <dt class="col-3">
                  Membership ID
                </dt>
                <dd class="col-9">
                  <?= htmlspecialchars($item->membershipId) ?>
                </dd>

                <dt class="col-3">
                  Amount
                </dt>
                <dd class="col-9">
                  <?= htmlspecialchars(MoneyHelpers::formatCurrency(MoneyHelpers::intToDecimal($item->amount), 'GBP')) ?>
                </dd>

                <dt class="col-3">
                  NGB ID
                </dt>
                <dd class="col-9">
                  <?= htmlspecialchars($item->ngbId) ?>
                </dd>

                <?php if ($item->notes) { ?>
                  <dt class="col-3">
                    Notes
                  </dt>
                  <dd class="col-9 mb-n2">
                    <?= $markdown->text($item->notes) ?>
                  </dd>
                <?php } ?>

                <?php if ($item->membershipDescription) { ?>
                  <dt class="col-3">
                    Membership Description
                  </dt>
                  <dd class="col-9 mb-n2">
                    <?= $markdown->text($item->membershipDescription) ?>
                  </dd>
                <?php } ?>
              </dl>
            </li>
          <?php } while ($item = $getBatchItems->fetch(PDO::FETCH_OBJ)); ?>
        </ul>
      <?php } else { ?>
      <?php } ?>
    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
