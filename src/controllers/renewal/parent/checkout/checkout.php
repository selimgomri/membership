<?php

$db = app()->db;
$tenant = app()->tenant;

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

try {
  $res = \Stripe\ApplePayDomain::create([
    'domain_name' => app('request')->hostname
  ], [
    'stripe_account' => $tenant->getStripeAccount()
  ]);
} catch (Exception $e) {
  // Not the end of the world so report the error and continue.
  // Any errors can be resolved later.
  reportError($e);
}

$expMonth = date("m");
$expYear = date("Y");

$customer = $db->prepare("SELECT CustomerID FROM stripeCustomers WHERE User = ?");
$customer->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$customerId = $customer->fetchColumn();

$numberOfCards = $db->prepare("SELECT COUNT(*) `count`, stripePayMethods.ID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?))");
$numberOfCards->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], 1, $expYear, $expYear, $expMonth]);
$countCards = $numberOfCards->fetch(PDO::FETCH_ASSOC);

$getCards = $db->prepare("SELECT stripePayMethods.ID, `MethodID`, stripePayMethods.Customer, stripePayMethods.Last4, stripePayMethods.Brand FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?)) ORDER BY `Name` ASC");
$getCards->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], 1, $expYear, $expYear, $expMonth]);
$cards = $getCards->fetchAll(PDO::FETCH_ASSOC);

$methodId = $customerID = null;

$selected = null;
if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID'])) {
  $selected = $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID'];

  foreach ($cards as $card) {
    if ($card['ID'] == $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID']) {
      $methodId = $card['MethodID'];
      $customerID = $card['Customer'];
    }
  }
}

if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'])) {
  halt(404);
}

$intent = null;

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'])) {
  $intent = \Stripe\PaymentIntent::retrieve(
    $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'],
    [
      'stripe_account' => $tenant->getStripeAccount()
    ]
  );
} else {
  header("Location: " . autoUrl("renewal/go"));
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
    $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'],
    [
      'payment_method' => $methodId,
      'customer' => $customerID,
    ],
    [
      'stripe_account' => $tenant->getStripeAccount()
    ]
  );
} else if ($customerId != null) {
  $intent = \Stripe\PaymentIntent::update(
    $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentIntent'],
    [
      'customer' => $customerId,
    ],
    [
      'stripe_account' => $tenant->getStripeAccount()
    ]
  );
}

if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalPaymentMethodID'])) {
  $_SESSION['TENANT-' . app()->tenant->getId()]['AddNewCard'] = true;
}

$getPaymentItems = $db->prepare("SELECT `Description`, `Amount`, `Currency` FROM stripePaymentItems WHERE `Payment` = ?");
$getPaymentItems->execute([
  $databaseId
]);

$countries = getISOAlpha2Countries();

$fontCss = 'https://fonts.googleapis.com/css?family=Open+Sans';
if (!app()->tenant->isCLS()) {
  $fontCss = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro';
}

$entryRequestDetails = [];
$entryRequestDetails[] = [
  'label' => 'Subtotal',
  'amount' => $intent->amount
];

$renewal = $_SESSION['TENANT-' . app()->tenant->getId()]['RegRenewalID'];

$numFormatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

$pagetitle = "Checkout";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div id="stripe-data" data-stripe-publishable="<?= htmlspecialchars(getenv('STRIPE_PUBLISHABLE')) ?>" data-stripe-font-css="<?= htmlspecialchars($fontCss) ?>" data-redirect-url-new="<?= htmlspecialchars(autoUrl("renewal/payments/checkout/complete-new")) ?>" data-redirect-url="<?= htmlspecialchars(autoUrl("renewal/payments/checkout/complete")) ?>" data-org-name="<?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>" data-intent-amount="<?= htmlspecialchars($intent->amount) ?>" data-intent-currency="<?= htmlspecialchars($intent->currency) ?>" data-payment-request-line-items="<?= htmlspecialchars(json_encode($entryRequestDetails)) ?>" data-stripe-account-id="<?= htmlspecialchars($tenant->getStripeAccount()) ?>">
</div>

<div class="container">

  <div class="row align-items-center justify-content-between mb-3">
    <div class="col-lg-7">
      <h1>Pay your membership fees</h1>
      <p class="lead">Checkout</p>
    </div>
    <div class="col text-lg-end">
      <div class="accepted-network-logos">
        <p>
          <img class="apple-pay-row" src="<?= autoUrl("public/img/stripe/apple-pay-mark.svg") ?>" aria-hidden="true"><img class="google-pay-row" src="<?= autoUrl("public/img/stripe/google-pay-mark.svg") ?>" aria-hidden="true"><img class="visa-row" src="<?= autoUrl("public/img/stripe/visa.svg") ?>" aria-hidden="true"><img class="mastercard-row" src="<?= autoUrl("public/img/stripe/mastercard.svg") ?>" aria-hidden="true"><img class="amex-row" src="<?= autoUrl("public/img/stripe/amex.svg") ?>" aria-hidden="true">
        </p>
      </div>
    </div>
  </div>

  <div class="row justify-content-between">
    <div class="col-lg-4 order-lg-2">

      <div class="position-sticky top-3" style="top: 3.5rem;">

        <h2>Summary</h2>
        <p>You're paying for;</p>

        <ul class="list-group mb-3 accordion" id="entry-list-group">
          <?php while ($item = $getPaymentItems->fetch(PDO::FETCH_ASSOC)) { ?>
            <li class="list-group-item">
              <div class="row">
                <div class="col-auto">
                  <p class="mb-0">
                    <?= htmlspecialchars($item['Description']) ?>
                  </p>
                </div>
                <div class="col text-end">
                  <p class="mb-0">
                    <strong><?php if ($item['Amount'] < 0) { ?>-<?php } ?>&pound;<?= htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) abs($item['Amount']))->withPointMovedLeft(2)->toScale(2))) ?></strong>
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
              <div class="col text-end">
                <p class="mb-0">
                  <strong>&pound;<?= htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $intent->amount))->withPointMovedLeft(2)->toScale(2)) ?></strong>
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="col-lg-7 order-lg-1">
      <h2 class="mb-3">Payment details</h2>

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
          <form id="saved-card-form">
            <div class="card-header" id="device-title">
              Pay with a saved card
            </div>
            <div class="card-body pb-0">

              <div class="mb-3">
                <label class="form-label" for="method">Choose a saved card</label>
                <select class="form-select pm-can-disable" name="method" id="method">
                  <option value="select">Select card</option>
                  <?php foreach ($cards as $card) { ?>
                    <option value="<?= $card['MethodID'] ?>">
                      <?= htmlspecialchars(getCardBrand($card['Brand'])) ?> &#0149;&#0149;&#0149;&#0149; <?= htmlspecialchars($card['Last4']) ?>
                    </option>
                  <?php } ?>
                </select>
              </div>

              <div id="save-card-box" class="d-none">
                <!-- Used to display form errors. -->
                <div id="saved-card-errors" role="alert"></div>

                <p>
                <div class="d-grid gap-2">
                  <button id="saved-card-button" class="btn btn-success pm-can-disable" type="button" data-secret="<?= $intent->client_secret ?>">
                    Pay &pound;<?= htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $intent->amount))->withPointMovedLeft(2)->toScale(2)) ?> now
                  </button>
                </div>
                </p>
              </div>

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
            <div class="mb-3">
              <label class="form-label" for="new-cardholder-name">Cardholder name</label>
              <input type="text" class="form-control pm-can-disable" id="new-cardholder-name" placeholder="C F Frost" required autocomplete="cc-name" aria-describedby="new-cardholder-name-help">
              <small id="new-cardholder-name-help" class="form-text text-muted">The name shown on your card</small>
              <div class="invalid-feedback">
                You must provide your full name
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="addr-line-1">Address line 1</label>
              <input type="text" class="form-control pm-can-disable" id="addr-line-1" placeholder="1 Burns Green" required autocomplete="address-line1">
              <div class="invalid-feedback">
                You must provide your address
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="addr-post-code">Post Code</label>
              <input type="text" class="form-control pm-can-disable text-uppercase" id="addr-post-code" placeholder="NE99 1AA" required autocomplete="postal-code">
              <div class="invalid-feedback">
                You must provide your post code
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="addr-post-code">Country</label>
              <select class="form-select pm-can-disable pm-can-disable" required id="addr-country" autocomplete="country">
                <?php foreach ($countries as $code => $name) { ?>
                  <option <?php if ($code == 'GB') { ?>selected<?php } ?> value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($name) ?></option>
                <?php } ?>
              </select>
              <div class="invalid-feedback">
                You must provide your country
              </div>
            </div>

            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>

            <!-- Multiple Part Element -->
            <div class="mb-3">
              <label class="form-label" for="card-number-element">
                Card number
              </label>
              <div class="input-group">
                <span class="input-group-text" id="card-brand-element"><img class="fa fa-fw" src="<?= autoUrl("public/img/stripe/network-svgs/credit-card.svg") ?>" aria-hidden="true"></span>
                <div id="card-number-element" class="form-control stripe-form-control pm-can-disable"></div>
                <div id="card-number-element-errors" class="stripe-feedback"></div>
              </div>
            </div>

            <div class="row">
              <div class="col">
                <div class="mb-3">
                  <label class="form-label" for="card-expiry-element">
                    Expires
                  </label>
                  <span id="card-expiry-element" class="form-control pm-can-disable"></span>
                  <div id="card-expiry-element-errors" class="stripe-feedback"></div>
                </div>
              </div>
              <div class="col">
                <div class="mb-3">
                  <label class="form-label" for="card-cvc-element">
                    CVC
                  </label>
                  <span id="card-cvc-element" class="form-control pm-can-disable"></span>
                  <div id="card-cvc-element-errors" class="stripe-feedback"></div>
                </div>
              </div>
            </div>

            <!--
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="reuse-card" name="reuse-card" checked>
                  <label class="form-check-label" for="reuse-card">Save this card for future payments</label>
                </div>
              </div>
              -->

            <p>Your card details will be saved for use with future payments</p>

            <!-- Used to display form errors. -->
            <div id="new-card-errors" role="alert"></div>

            <p class="mb-0">
            <div class="d-grid gap-2">
              <button id="new-card-button" class="btn btn-success pm-can-disable" type="submit" data-secret="<?= $intent->client_secret ?>">
                Pay &pound;<?= htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $intent->amount))->withPointMovedLeft(2)->toScale(2)) ?> now
              </button>
            </div>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/payment-helpers.js");
$footer->addJs("public/js/registration-and-renewal/card-checkout.js");
$footer->addJs("public/js/NeedsValidation.js");
$footer->render(); ?>