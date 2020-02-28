<?php

// Select payment from list

$pagetitle = 'More Details - Payment Confirmation';

$ids = $_SESSION['PaymentConfSearch']['id'];

global $db;
$getPayments = $db->prepare("SELECT `Date`, `Name`, `Amount`, `Currency`, `Forename`, `Surname` FROM payments INNER JOIN users ON payments.UserID = users.UserID WHERE PaymentID = ? AND `Type` = 'Payment'");

if (sizeof($ids) == 0) {
  halt(404);
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>Select payment</h1>
      <p class="lead">Select the payment from the list below.</p>

      <form action="<?=autoUrl("confirmation/confirm-selected")?>" method="post">

        <div class="list-group mb-3">

          <?php foreach ($ids as $paymentId) { ?>

          <?php
          $getPayments->execute([
            $paymentId
          ]);
          $payment = $getPayments->fetch(PDO::FETCH_ASSOC);
          if ($payment == null) {
            break;
          }
          ?>

          <div class="list-group-item">
            <div class="custom-control custom-radio">
              <input type="radio" id="payment-<?=htmlspecialchars($paymentId)?>" name="payment" value="<?=htmlspecialchars($paymentId)?>" class="custom-control-input">
              <label class="custom-control-label d-block" for="payment-<?=htmlspecialchars($paymentId)?>">
                <?=htmlspecialchars($payment['Forename'] . ' ' . $payment['Surname'])?>: 
                <span class="mono">
                  &pound;<?=number_format($payment['Amount']/100, 2, '.', '')?>, <?=htmlspecialchars($payment['Name'])?>, <?=htmlspecialchars($payment['Date'])?>
                </span>
              </label>
            </div>
          </div>

          <?php } ?>

        </div>

        <p>
          <button class="btn btn-success" type="submit">
            Use selected
          </button>
        </p>
      </form>

    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->render();