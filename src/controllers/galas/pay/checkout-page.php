<?php

global $db;

\Stripe\Stripe::setApiKey(env('STRIPE'));
if (env('STRIPE_APPLE_PAY_DOMAIN')) {
  \Stripe\ApplePayDomain::create([
    'domain_name' => env('STRIPE_APPLE_PAY_DOMAIN')
  ]);
}

$expMonth = date("m");
$expYear = date("Y");

$customer = $db->prepare("SELECT CustomerID FROM stripeCustomers WHERE User = ?");
$customer->execute([$_SESSION['UserID']]);
$customerId = $customer->fetchColumn();

$numberOfCards = $db->prepare("SELECT COUNT(*) `count`, stripePayMethods.ID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?))");
$numberOfCards->execute([$_SESSION['UserID'], 1, $expYear, $expYear, $expMonth]);
$countCards = $numberOfCards->fetch(PDO::FETCH_ASSOC);

$getCards = $db->prepare("SELECT stripePayMethods.ID, `MethodID`, stripePayMethods.Customer, stripePayMethods.Last4, stripePayMethods.Brand FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?)) ORDER BY `Name` ASC");
$getCards->execute([$_SESSION['UserID'], 1, $expYear, $expYear, $expMonth]);
$cards = $getCards->fetchAll(PDO::FETCH_ASSOC);

$methodId = $customerID = null;

$selected = null;
if (isset($_SESSION['GalaPaymentMethodID'])) {
  $selected = $_SESSION['GalaPaymentMethodID'];

  foreach ($cards as $card) {
    if ($card['ID'] == $_SESSION['GalaPaymentMethodID']) {
      $methodId = $card['MethodID'];
      $customerID = $card['Customer'];
    }
  }
}

$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

if (!isset($_SESSION['PaidEntries'])) {
  halt(404);
}

$intent = null;

if (isset($_SESSION['GalaPaymentIntent'])) {
  $intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);
} else {
  header("Location: " . autoUrl("galas/pay-for-entries"));
  return;
}

$getId = $db->prepare("SELECT ID FROM stripePayments WHERE Intent = ?");
$getId->execute([
  $intent->id
]);
$databaseId = $getId->fetchColumn();
$paymentDatabaseId = $databaseId;

if ($intent->status == 'succeeded') {
  header("Location: " . autoUrl("payments/card-transactions"));
  return;
}

if ($methodId != null && $customerID != null) {
  $intent = \Stripe\PaymentIntent::update(
    $_SESSION['GalaPaymentIntent'], [
      'payment_method' => $methodId,
      'customer' => $customerID,
    ]
  );
}

if ($customerId != null) {
  $intent = \Stripe\PaymentIntent::update(
    $_SESSION['GalaPaymentIntent'], [
      'customer' => $customerId,
    ]
  );
}

if (!isset($_SESSION['GalaPaymentMethodID'])) {
  $_SESSION['AddNewCard'] = true;
}

$getEntriesByPI = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE StripePayment = ?");
$getEntriesByPI->execute([
  $paymentDatabaseId
]);

$countries = getISOAlpha2Countries();

$pagetitle = "Checkout";
include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/galas/galaMenu.php";
?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Pay for entries</li>
    </ol>
  </nav>
  
  <div class="row">
    <div class="col-lg-8">
      <h1>Pay for gala entries</h1>
      <p class="lead">You can pay for gala entries by direct debit or by credit or debit card.</p>

      <h2>Selected entries</h2>
      <p>You'll pay for the following gala entries</p>

      <ul class="list-group mb-3">
        <?php while ($entry = $getEntriesByPI->fetch(PDO::FETCH_ASSOC)) {
          $notReady = !$entry['EntryProcessed'];
        ?>
        <li class="list-group-item">
          <h3><?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?> for <?=htmlspecialchars($entry['GalaName'])?></h3>
          <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-6">
              <p class="mb-0">
                <?=htmlspecialchars($entry['MForename'])?> is entered in;
              </p>
              <ul class="list-unstyled">
              <?php $count = 0; ?>
              <?php foreach($swimsArray as $colTitle => $text) { ?>
                <?php if ($entry[$colTitle]) { $count++; ?>
                <li><?=$text?></li>
                <?php } ?>
              <?php } ?>
            </div>
            <div class="col text-right">
              <div class="d-sm-none mb-3"></div>
              <p>
                <?php if ($entry['GalaFeeConstant']) { ?>
                <?=$count?> &times; &pound;<?=htmlspecialchars(number_format($entry['GalaFee'], 2))?>
                <?php } else { ?>
                <strong><?=$count?> swims</strong>
                <?php } ?>
              </p>

              <?php if ($notReady) { ?>
              <p>
                Once you pay for this entry, you won't be able to edit it.
              </p>
              <?php } ?>

              <p>
                <strong>Fee &pound;<?=htmlspecialchars(number_format($entry['FeeToPay'] ,2, '.', ''))?></strong>
              </p>
            </div>
          </div>
        </li>
        <?php } ?>
        <li class="list-group-item">
          <div class="row align-items-center">
            <div class="col-6">
              <p class="mb-0">
                <strong>Total to pay</strong>
              </p>
            </div>
            <div class="col text-right">
              <p class="mb-0">
                <strong>&pound;<?=htmlspecialchars(number_format($intent->amount/100 ,2, '.', ''))?></strong>
              </p>
            </div>
          </div>
        </li>
      </ul>

      <h2 class="mb-3">Payment Method</h2>
      <p>Pay with a mobile wallet such as Apple Pay or Google Pay, a saved card or a new card.</p>
        <div id="payment-request-card">
          <div class="mb-3">
            <form>
              <div id="payment-request-button">
                <!-- A Stripe Element will be inserted here. -->
              </div>
              <div id="alert-placeholder" class="mt-3"></div>
            </form>
          </div>

          <p class="text-center">Or</p>
        </div>

        <?php if (sizeof($cards) > 0) { ?>
        <div class="card mb-3" id="saved-cards">
          <form action="<?=autoUrl("galas/pay-for-entries/switch-method")?>" method="post" id="saved-card-form">
            <div class="card-header" id="device-title">
              Pay with a saved card
            </div>
            <div class="card-body">

              <div class="form-group <?php if ($selected == null) { ?>mb-0<?php } ?>">
                <label for="method">Payment card</label>
                <select class="custom-select" name="method" id="method" onchange="this.form.submit()">
                  <option value="select">Select a payment card</option>
                  <?php foreach ($cards as $card) { ?>
                  <option value="<?=$card['ID']?>" <?php if ($selected == $card['ID']) { $methodId = $card['MethodID']; ?>selected<?php } ?>>
                    <?=htmlspecialchars(getCardBrand($card['Brand']))?> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($card['Last4'])?>
                  </option>
                  <?php } ?>
                </select>
              </div>

              <noscript>
                <p>
                  <button type="submit" class="btn btn-success btn-block pm-can-disable" data-methodId="<?=$methodId?>">
                    Use selected card
                  </button>
                </p>
              </noscript>

              <?php if ($selected != null) { ?>
              <!-- Used to display form errors. -->
              <div id="saved-card-errors" role="alert"></div>

              <p class="mb-0">
                <button id="saved-card-button" class="btn btn-success btn-block pm-can-disable" type="button" data-secret="<?= $intent->client_secret ?>">
                  Pay now
                </button>
              </p>
              <?php } ?>
            </div>
          </form>
        </div>

        <p class="text-center">Or</p>
        <?php } ?>

        <div class="card mb-3">
          <div class="card-header">
            Pay with a new card
          </div>
          <div class="card-body">
            <form id="new-card-form" class="needs-validation" novalidate>
              <div class="form-group">
                <label for="new-cardholder-name">Cardholder name</label>
                <input type="text" class="form-control" id="new-cardholder-name" placeholder="C F Frost" required autocomplete="cc-name" aria-describedby="new-cardholder-name-help">
                <small id="new-cardholder-name-help" class="form-text text-muted">The name shown on your card</small>
                <div class="invalid-feedback">
                  You must provide your full name
                </div>
              </div>

              <div class="form-group">
                <label for="addr-line-1">Address line 1</label>
                <input type="text" class="form-control" id="addr-line-1" placeholder="1 Burns Green" required autocomplete="address-line1">
                <div class="invalid-feedback">
                  You must provide your address
                </div>
              </div>

              <div class="form-group">
                <label for="addr-post-code">Post Code</label>
                <input type="text" class="form-control text-uppercase" id="addr-post-code" placeholder="NE99 1AA" required autocomplete="postal-code">
                <div class="invalid-feedback">
                  You must provide your post code
                </div>
              </div>

              <div class="form-group">
                <label for="addr-post-code">Country</label>
                <select class="custom-select pm-can-disable" required id="addr-country" autocomplete="country">
                  <?php foreach ($countries as $code => $name) { ?>
                  <option <?php if ($code == 'GB') { ?>selected<?php } ?> value="<?=htmlspecialchars($code)?>"><?=htmlspecialchars($name)?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">
                  You must provide your country
                </div>
              </div>

              <!-- Used to display form errors. -->
              <div id="card-errors" role="alert"></div>

              <!-- Multiple Part Element -->
              <div class="form-group">
                <label for="card-number-element">
                  Card number
                </label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="card-brand-element"><img class="fa fa-fw" src="<?=autoUrl("public/img/stripe/network-svgs/credit-card.svg")?>" aria-hidden="true"></span>
                  </div>
                  <div id="card-number-element" class="form-control stripe-form-control"></div>
                  <div id="card-number-element-errors" class="stripe-feedback"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="col">
                  <div class="form-group">
                    <label for="card-expiry-element">
                      Expires
                    </label>
                    <span id="card-expiry-element" class="form-control"></span>
                    <div id="card-expiry-element-errors" class="stripe-feedback"></div>
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label for="card-cvc-element">
                      CVC
                    </label>
                    <span id="card-cvc-element" class="form-control"></span>
                    <div id="card-cvc-element-errors" class="stripe-feedback"></div>
                  </div>
                </div>
              </div>

              <!--
              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="reuse-card" name="reuse-card" checked>
                  <label class="custom-control-label" for="reuse-card">Save this card for future payments</label>
                </div>
              </div>
              -->

              <p>Your card details will be saved for use with future payments</p>

              <!-- Used to display form errors. -->
              <div id="new-card-errors" role="alert"></div>

              <p class="mb-0">
                <button id="new-card-button" class="btn btn-success btn-block pm-can-disable" type="submit" data-secret="<?= $intent->client_secret ?>">
                  Pay now
                </button>
              </p>
            </form>
          </div>
        </div>
    </div>
  </div>
</div>

<script src="<?=autoUrl("js/payment-helpers.js")?>"></script>
<script src="<?=autoUrl("js/gala-checkout.js")?>"></script>
<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . "views/footer.php"; ?>