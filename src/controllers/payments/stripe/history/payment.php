<?php

function getWalletName($name) {
  if ($name == 'apple_pay') {
    return 'Apple Pay';
  } else if ($name == 'amex_express_checkout') {
    return 'Amex Express Checkout';
  } else if ($name == 'google_pay') {
    return 'Google Pay';
  } else if ($name == 'masterpass') {
    return 'Masterpass  ';
  } else if ($name == 'samsung_pay') {
    return 'Samsung Pay';
  } else if ($name == 'visa_checkout') {
    return 'Visa Checkout';
  } else {
    return 'Other wallet';
  }
}

global $db;

$payment = $db->prepare("SELECT * FROM ((stripePayments INNER JOIN stripePaymentItems ON stripePaymentItems.Payment = stripePayments.ID) INNER JOIN users ON stripePayments.User = users.UserID) WHERE stripePayments.ID = ?");
$payment->execute([$id]);

$paymentItems = $db->prepare("SELECT * FROM stripePaymentItems WHERE stripePaymentItems.Payment = ?");
$paymentItems->execute([$id]);

$pm = $payment->fetch(PDO::FETCH_ASSOC);

if ($pm == null || ($_SESSION['AccessLevel'] != 'Admin' && $pm['User'] != $_SESSION['UserID'])) {
  halt(404);
}

\Stripe\Stripe::setApiKey(env('STRIPE'));

$payment = \Stripe\PaymentIntent::retrieve([
  'id' => $pm['Intent'],
  'expand' => ['customer', 'payment_method']
]);

pre($payment);

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
      <h1><?php if ($_SESSION['AccessLevel'] == 'Admin') { ?><?=htmlspecialchars($pm['Forename'] . ' ' . $pm['Surname'] . ':')?> <?php } ?>Card payment #<?=htmlspecialchars($id)?></h1>
      <p class="lead">At <?=$date->format("H:i \o\\n j F Y")?></p>

      <?php if ($card != null) { ?>
      <h2>Card information</h2>
      <dl class="row">
        <dt class="col-sm-5 col-md-4">Card</dt>
        <dd class="col-sm-7 col-md-8"><i class="fa <?=htmlspecialchars(getCardFA($card->brand))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($card->brand))?></span> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($card->last4)?></dd>

        <dt class="col-sm-5 col-md-4">Type</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars(mb_convert_case ($card->funding, MB_CASE_TITLE))?></dd>

        <?php if (isset($card->three_d_secure->authenticated) && $card->three_d_secure->authenticated && isset($card->three_d_secure->succeeded) && $card->three_d_secure->succeeded) { ?>
        <dt class="col-sm-5 col-md-4">Verification</dt>
        <dd class="col-sm-7 col-md-8">Verified using 3D Secure</dd>
        <?php } ?>

        <?php if (isset($card->wallet)) { ?>
        <dt class="col-sm-5 col-md-4">Mobile Wallet Payment</dt>
        <dd class="col-sm-7 col-md-8"><?=getWalletName($card->wallet->type)?></dd>

        <?php if (isset($card->wallet->dynamic_last4)) { ?>
        <dt class="col-sm-5 col-md-4">Device Account Number</dt>
        <dd class="col-sm-7 col-md-8">&#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($card->wallet->dynamic_last4)?></dd>
        <?php } ?>
        <?php } ?>
        
        <?php if (isset($payment->charges->data[0]->outcome->risk_level) && $payment->charges->data[0]->outcome->risk_level) { ?>
        <dt class="col-sm-5 col-md-4">Risk level</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars($payment->charges->data[0]->outcome->risk_level)?></dd>
        <?php } ?>
        
        <?php if (isset($payment->charges->data[0]->outcome->risk_score) && $payment->charges->data[0]->outcome->risk_score) { ?>
        <dt class="col-sm-5 col-md-4">Risk score</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars($payment->charges->data[0]->outcome->risk_score)?></dd>
        <?php } ?>
        
        <?php if (isset($payment->charges->data[0]->receipt_url) && $payment->charges->data[0]->receipt_url) { ?>
        <dt class="col-sm-5 col-md-4">Stripe receipt</dt>
        <dd class="col-sm-7 col-md-8"><a target="_blank" href="<?=htmlspecialchars($payment->charges->data[0]-> receipt_url)?>">Receipt</a></dd>
        <?php } ?>
        
        <?php if (isset($payment->payment_method->card->checks->address_line1_check) && $payment->payment_method->card->checks->address_line1_check) { ?>
        <dt class="col-sm-5 col-md-4">Address line 1</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars($payment->payment_method->card->checks->address_line1_check)?></dd>
        <?php } ?>
        
        <?php if (isset($payment->payment_method->card->checks->address_postal_code_check) && $payment->payment_method->card->checks->address_postal_code_check) { ?>
        <dt class="col-sm-5 col-md-4">Post code</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars($payment->payment_method->card->checks->address_postal_code_check)?></dd>
        <?php } ?>
        
        <?php if (isset($payment->payment_method->card->checks->cvc_check) && $payment->payment_method->card->checks->cvc_check) { ?>
        <dt class="col-sm-5 col-md-4">CVC</dt>
        <dd class="col-sm-7 col-md-8"><?=htmlspecialchars($payment->payment_method->card->checks->cvc_check)?></dd>
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
      <?php } else { ?>
      <h2>Refund this transaction</h2>
      <p>To refund gala entries, use the gala refunds system.</p>
      <?php } ?>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';