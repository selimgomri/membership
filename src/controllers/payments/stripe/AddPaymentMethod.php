<?php

// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(app()->tenant->getKey('STRIPE'));

$db = app()->db;

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$getUserEmail->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

$checkIfCustomer = $db->prepare("SELECT COUNT(*) FROM stripeCustomers WHERE User = ?");
$checkIfCustomer->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

if ($checkIfCustomer->fetchColumn() == 0) {
  // See your keys here: https://dashboard.stripe.com/account/apikeys
  \Stripe\Stripe::setApiKey(app()->tenant->getKey('STRIPE'));

  // Create a Customer:
  $customer = \Stripe\Customer::create([
    "name" => $user['Forename'] . ' ' . $user['Surname'],
    "description" => "Customer for " . $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'] . ' (' . $user['EmailAddress'] . ')',
    'email' => $user['EmailAddress'],
    'phone' => $user['Mobile']
  ]);

  // YOUR CODE: Save the customer ID and other info in a database for later.
  $id = $customer->id;
  $addCustomer = $db->prepare("INSERT INTO stripeCustomers (User, CustomerID) VALUES (?, ?)");
  $addCustomer->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'],
    $id
  ]);
}

$setupIntent = null;
if (!isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeSetupIntentId'])) {
  $setupIntent = \Stripe\SetupIntent::create();
  $_SESSION['TENANT-' . app()->tenant->getId()]['StripeSetupIntentId'] = $setupIntent->id;
} else {
  $setupIntent = \Stripe\SetupIntent::retrieve($_SESSION['TENANT-' . app()->tenant->getId()]['StripeSetupIntentId']);
}

$countries = getISOAlpha2Countries();

$fontCss = 'https://fonts.googleapis.com/css?family=Open+Sans';
if (!bool(env('IS_CLS'))) {
  $fontCss = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro';
}

$pagetitle = 'Add a payment card';

include BASE_PATH . 'views/header.php';

?>

<div id="stripe-data" data-stripe-publishable="<?=htmlspecialchars(app()->tenant->getKey('STRIPE_PUBLISHABLE'))?>" data-stripe-font-css="<?=htmlspecialchars($fontCss)?>">
</div>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/cards")?>">Cards</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add card</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <h1>Add a payment card</h1>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['PayCardError'])) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>An error occurred</strong>
        </p>
        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['PayCardErrorMessage'])) { ?>
        <p class="mb-0"><?=htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['PayCardErrorMessage'])?></p>
        <?php } ?>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['PayCardError']); unset($_SESSION['TENANT-' . app()->tenant->getId()]['PayCardErrorMessage']); ?>
      <?php } ?>

      <form action="<?=htmlspecialchars(autoUrl("payments/cards/add"))?>" method="post" id="payment-form" class="mb-5 needs-validation" novalidate>
        <div id="form-hideable" class="show fade">
          <div class="form-group">
            <label for="cardholder-name">Cardholder name</label>
            <input type="text" class="form-control pm-can-disable" id="cardholder-name" placeholder="C F Frost" required aria-describedby="cardholder-name-help" autocomplete="cc-name">
            <small id="cardholder-name-help" class="form-text text-muted">The name shown on your card</small>
            <div class="invalid-feedback">
              You must provide your full name
            </div>
          </div>

          <div class="form-group">
            <label for="addr-line-1">Address line 1</label>
            <input type="text" class="form-control pm-can-disable" id="addr-line-1" placeholder="1 Burns Green" required autocomplete="address-line1">
            <div class="invalid-feedback">
              You must provide your address
            </div>
          </div>

          <div class="form-group">
            <label for="addr-post-code">Post Code</label>
            <input type="text" class="form-control pm-can-disable text-uppercase" id="addr-post-code" placeholder="NE99 1AA" required autocomplete="postal-code">
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
                <span id="card-expiry-element" class="form-control pm-can-disable"></span>
                <div id="card-expiry-element-errors" class="stripe-feedback"></div>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="card-cvc-element">
                  CVC
                </label>
                <span id="card-cvc-element" class="form-control pm-can-disable"></span>
                <div id="card-cvc-element-errors" class="stripe-feedback"></div>
              </div>
            </div>
          </div>

        </div>

        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>

        <p>
          <button id="card-button" class="btn btn-success" data-secret="<?= $setupIntent->client_secret ?>">Add payment card</button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/payment-helpers.js");
$footer->addJs("public/js/add-payment-card.js");
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();