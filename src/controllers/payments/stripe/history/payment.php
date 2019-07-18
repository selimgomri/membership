<?php

global $db;

$payment = $db->prepare("SELECT * FROM stripePayments INNER JOIN stripePaymentItems ON stripePaymentItems.Payment = stripePayments.ID WHERE stripePayments.ID = ?");
$payment->execute([$id]);

$paymentItems = $db->prepare("SELECT * FROM stripePaymentItems WHERE stripePaymentItems.Payment = ?");
$paymentItems->execute([$id]);

$pm = $payment->fetch(PDO::FETCH_ASSOC);

if ($pm == null || $pm['User'] != $_SESSION['UserID']) {
  halt(404);
}

\Stripe\Stripe::setApiKey(env('STRIPE'));

$payment = \Stripe\PaymentIntent::retrieve($pm['Intent']);

$pagetitle = 'Card Payment #' . htmlspecialchars($id);

include BASE_PATH . 'views/header.php';

$card = null;
if (isset($payment->charges->data[0]->payment_method_details->card)) {
  $card = $payment->charges->data[0]->payment_method_details->card;
}

$date = new DateTime($pm['DateTime'], new DateTimeZone('UTC'));
$date->setTimezone(new DateTimeZone('Europe/London'));

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/cards")?>">Cards</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/card-transactions")?>">History</a></li>
      <li class="breadcrumb-item active" aria-current="page">#<?=htmlspecialchars($id)?></li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <h1>Card payment #<?=htmlspecialchars($id)?></h1>
      <p class="lead">At <?=$date->format("H:i \o\\n j F Y")?></p>

      <?php if ($card != null) { ?>
      <h2>Card information</h2>
      <dl class="row">
        <dt class="col-sm-3">Card</dt>
        <dd class="col-sm-9"><i class="fa <?=htmlspecialchars(getCardFA($card->brand))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($card->brand))?></span> **** <?=htmlspecialchars($card->last4)?></dd>

        <dt class="col-sm-3">Type</dt>
        <dd class="col-sm-9"><?=htmlspecialchars(mb_convert_case ($card->funding, MB_CASE_TITLE))?></dd>

        <?php if ($card->three_d_secure->authenticated && $card->three_d_secure->succeeded) { ?>
        <dt class="col-sm-3">Verification</dt>
        <dd class="col-sm-9">Verified using 3D Secure</dd>
        <?php } ?>
      </dl>
      <?php } ?>

      <h2>Payment items</h2>
      <p class="lead">All items in this payment</p>

      <?php if ($item = $paymentItems->fetch(PDO::FETCH_ASSOC)) { ?>
      <ul class="list-group mb-3">
        <?php do { ?>
        <li class="list-group-item">
          <h3><?=htmlspecialchars($item['Name'])?></h3>
          <p><?=htmlspecialchars($item['Description'])?></p>

          <p class="mb-0">&pound;<?=number_format($item['Amount']/100, 2, '.', '')?></p>
        </li>
        <?php } while ($item = $paymentItems->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } ?>

      <?php if (isset($payment->charges->data[0]->amount_refunded) && $payment->charges->data[0]->amount_refunded > 0) { ?>
      <h2>Payment refunds</h2>
      <p>&pound;<?=number_format($payment->charges->data[0]->amount_refunded/100, 2, '.', '')?> refunded to <?=htmlspecialchars(getCardBrand($card->brand))?> **** <?=htmlspecialchars($card->last4)?></p>
      <?php } ?>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';