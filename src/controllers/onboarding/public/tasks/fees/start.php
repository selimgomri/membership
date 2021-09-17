<?php

$session = \SCDS\Onboarding\Session::retrieve($_SESSION['OnboardingSessionId']);

if ($session->status == 'not_ready') halt(404);

$user = $session->getUser();

$tenant = app()->tenant;

$logos = app()->tenant->getKey('LOGO_DIR');

$stages = $session->stages;

$tasks = \SCDS\Onboarding\Session::stagesOrder();

if (!$session->batch) halt(503);

$db = app()->db;

$getBatch = $db->prepare("SELECT membershipBatch.ID id, membershipYear.ID yearId, membershipBatch.Completed completed, DueDate due, Total total, membershipYear.Name yearName, membershipYear.StartDate yearStart, membershipYear.EndDate yearEnd, PaymentTypes payMethods, PaymentDetails payDetails FROM membershipBatch INNER JOIN membershipYear ON membershipBatch.Year = membershipYear.ID INNER JOIN users ON users.UserID = membershipBatch.User WHERE membershipBatch.ID = ? AND users.Tenant = ?");
$getBatch->execute([
  $session->batch,
  app()->tenant->getId(),
]);

$batch = $getBatch->fetch(PDO::FETCH_OBJ);

if (!$batch) halt(404);

// Get batch items
$getBatchItems = $db->prepare("SELECT membershipBatchItems.ID id, membershipBatchItems.Membership membershipId, membershipBatchItems.Amount amount, membershipBatchItems.Notes notes, members.MForename firstName, members.MSurname lastName, members.ASANumber ngbId, clubMembershipClasses.Type membershipType, clubMembershipClasses.Name membershipName, clubMembershipClasses.Description membershipDescription FROM membershipBatchItems INNER JOIN members ON members.MemberID = membershipBatchItems.Member INNER JOIN clubMembershipClasses ON clubMembershipClasses.ID = membershipBatchItems.Membership WHERE Batch = ?");
$getBatchItems->execute([
  $session->batch
]);
$item = $getBatchItems->fetch(PDO::FETCH_OBJ);

$payMethods = json_decode($batch->payMethods);

$canPay = true;
$due = new DateTime($batch->due, new DateTimeZone('Europe/London'));
$due->setTime(0, 0, 0, 0);
$now = new DateTime('now', new DateTimeZone('Europe/London'));
$now->setTime(0, 0, 0, 0);
if ($now > $due) $canPay = false;

$markdown = new \ParsedownExtra();
$markdown->setSafeMode(true);

$pagetitle = 'Membership fees - Onboarding';

include BASE_PATH . "views/head.php";

?>

<div class="min-vh-100 mb-n3 overflow-auto">
  <div class="bg-light">
    <div class="container">
      <div class="row justify-content-center py-5">
        <div class="col-lg-8 col-md-10">

          <?php if ($logos) { ?>
            <img src="<?= htmlspecialchars(getUploadedAssetUrl($logos . 'logo-75.png')) ?>" srcset="<?= htmlspecialchars(getUploadedAssetUrl($logos . 'logo-75@2x.png')) ?> 2x, <?= htmlspecialchars(getUploadedAssetUrl($logos . 'logo-75@3x.png')) ?> 3x" alt="" class="img-fluid d-block mx-auto">
          <?php } else { ?>
            <img src="<?= htmlspecialchars(autoUrl('public/img/corporate/scds.png')) ?>" height="75" width="75" alt="" class="img-fluid d-block mx-auto">
          <?php } ?>

        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row justify-content-center py-5">
      <div class="col-lg-8 col-md-10">
        <h1 class="text-center">Pay your membership fees</h1>

        <p class="lead mb-5 text-center">
          Pay your Club and Swim England annual fees.
        </p>

        <?php if (!$batch->completed && $batch->total > 0 && sizeof($payMethods) > 0 && $canPay) { ?>
          <form method="post" class="needs-validation" novalidate>
            <h2>Pay <?= htmlspecialchars(MoneyHelpers::formatCurrency(MoneyHelpers::intToDecimal($batch->total), 'GBP')) ?></h2>
            <?php if (sizeof($payMethods) > 1) { ?>
              <!-- Select payment method -->
              <p class="mb-2">Choose a payment method</p>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pay-method" id="pay-card" value="card" <?php if (!in_array('card', $payMethods)) { ?>disabled<?php } ?> required>
                  <label class="form-check-label" for="pay-card">
                    Credit/debit card
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pay-method" id="pay-dd" value="dd" <?php if (!in_array('card', $payMethods)) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="pay-dd">
                    Add to my next monthly Direct Debit payment
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pay-method" id="pay-cash" value="cash" <?php if (!in_array('cash', $payMethods)) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="pay-cash">
                    Cash
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pay-method" id="pay-cheque" value="cheque" <?php if (!in_array('cheque', $payMethods)) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="pay-cheque">
                    Cheque
                  </label>
                </div>

                <div class="form-check mb-0">
                  <input class="form-check-input" type="radio" name="pay-method" id="pay-bacs" value="bacs" <?php if (!in_array('bacs', $payMethods)) { ?>disabled<?php } ?>>
                  <label class="form-check-label" for="pay-bacs">
                    Bank transfer
                  </label>
                </div>
              </div>
            <?php } else if (sizeof($payMethods) > 0) { ?>
              <!-- Just go straight to payment -->
              <p>You can only pay for this batch with {{PAY_METHOD}}</p>
            <?php } ?>
            <p class="d-grid mb-1">
              <button type="submit" class="btn btn-success">Pay now <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
            </p>
            <p class="small text-muted">With SCDS Checkout</p>

            <p>
              If you think there are any mistakes on this page, please contact your membership secretary before you proceed. They'll be able to adjust your members, memberships or fees.
            </p>
          </form>
        <?php } else if (!$batch->completed && $batch->total == 0) { ?>
          <form method="post" class="needs-validation" novalidate>
            <h2>Pay <?= htmlspecialchars(MoneyHelpers::formatCurrency(MoneyHelpers::intToDecimal($batch->total), 'GBP')) ?></h2>

            <p>
              It appears you have nothing to pay.
            </p>

            <p>
              If you think there are any mistakes on this page, please contact your membership secretary before you proceed. They'll be able to adjust your members, memberships or fees. Your membership secretary may contact you if you do not pay for a membership that you should have.
            </p>

            <p>
              Otherwise, review the items below and confirm.
            </p>
          </form>
        <?php } ?>

        <?php if ($item) { ?>
          <h2 class="mb-3">You're paying for the following memberships</h2>
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

          <?php if (!$batch->completed && $batch->total == 0) { ?>
            <form method="post" class="needs-validation" novalidate>
              <p>
                <button type="submit" class="btn btn-success">
                  Confirm memberships
                </button>
              </p>
            </form>
          <?php } ?>
        <?php } else { ?>
          <div class="alert alert-info">
            <p class="mb-0">
              <strong>There are no memberships to display for the period <?= htmlspecialchars((new DateTime($batch->yearStart))->format('j F Y')) ?> to <?= htmlspecialchars((new DateTime($batch->yearEnd))->format('j F Y')) ?></strong>
            </p>
          </div>
        <?php } ?>

        <h2>Membership Batch Details</h2>
        <p>
          If you have any issues, please give the following details to your membership secretary.
        </p>

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
            Due by end
          </dt>
          <dd class="col-9">
            <?= htmlspecialchars($session->dueDate->format('j F Y')) ?>
          </dd>

          <?php if (!$batch->completed) { ?>
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
          <?php } ?>
        </dl>

        <!-- <form method="post" class="needs-validation" novalidate>

          <p>
            <button type="submit" class="btn btn-success">Confirm</button>
          </p>

        </form> -->

      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('js/NeedsValidation.js');
$footer->render();

?>