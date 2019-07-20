<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));
if (env('STRIPE_APPLE_PAY_DOMAIN')) {
  \Stripe\ApplePayDomain::create([
    'domain_name' => env('STRIPE_APPLE_PAY_DOMAIN')
  ]);
}

global $db;

$expMonth = date("m");
$expYear = date("Y");

$customer = $db->prepare("SELECT CustomerID FROM stripeCustomers WHERE User = ?");
$customer->execute([$_SESSION['UserID']]);
$customerId = $customer->fetchColumn();

$numberOfCards = $db->prepare("SELECT COUNT(*) `count`, stripePayMethods.ID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?))");
$numberOfCards->execute([$_SESSION['UserID'], 1, $expYear, $expYear, $expMonth]);
$countCards = $numberOfCards->fetch(PDO::FETCH_ASSOC);

$getCards = $db->prepare("SELECT stripePayMethods.ID, `MethodID`, stripePayMethods.Customer, stripePayMethods.Name, stripePayMethods.Last4, stripePayMethods.Brand FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?)) ORDER BY `Name` ASC");
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

$getEntry = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE EntryID = ? AND NOT Charged AND members.UserID = ?");

$hasEntries = false;
foreach ($_SESSION['PaidEntries'] as $entry => $details) {
  $getEntry->execute([$entry, $_SESSION['UserID']]);
  $entry = $getEntry->fetch(PDO::FETCH_ASSOC);
  if ($entry != null) {
    $hasEntries = true;
  }
}
if (!$hasEntries) {
  halt(404);
}

$entryRequestDetails = [];

if (!isset($_SESSION['PaidEntries'])) {
  halt(404);
}

$total = 0;

foreach ($_SESSION['PaidEntries'] as $entry => $details) {
  $total += $details['Amount'];
}

if ($total == 0) {
  header("Location: " . autoUrl("galas/pay-for-entries"));
  return;
}

$intent = null;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  $intent = \Stripe\PaymentIntent::create([
    'amount' => $total,
    'currency' => 'gbp',
    'payment_method_types' => ['card'],
    'confirm' => false,
    'setup_future_usage' => 'off_session',
  ]);
  $_SESSION['GalaPaymentIntent'] = $intent->id;
} else {
  $intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);
}

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

if ($total != $intent->amount) {
  $intent = \Stripe\PaymentIntent::update(
    $_SESSION['GalaPaymentIntent'], [
      'amount' => $total,
    ]
  );
}

if (!isset($_SESSION['GalaPaymentMethodID'])) {
  $_SESSION['AddNewCard'] = true;
}

$pagetitle = "Checkout";
include BASE_PATH . "views/header.php";
include BASE_PATH . "controllers/galas/galaMenu.php";
?>

<style>
/**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
.card-element {
  box-sizing: border-box;

  /* height: 40px; */

  padding: 1rem;

  color: #333;
  font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  font-size: 1rem;

  border: 1px solid #ced4da;

  background-color: white;

  box-shadow: none;
}
</style>
<?php if (bool(env('IS_CLS'))) { ?>
<style>
.card-element {
  border-radius: 0px;
}
</style>
<?php } ?>


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
      <p>If you haven't opted out of direct debit gala payments and you don't make a payment by card, you'll be automatically charged for gala entries as part of your monthly payment when the gala coordinator submits the entries to the host club.</p>

      <?php if (isset($_SESSION['GalaPaymentMethodID']) || isset($_SESSION['AddNewCard'])) { ?>

      <h2>Paying for</h2>
      <p>You're paying for the following gala entries</p>

      <ul class="list-group mb-3">
        <?php foreach ($_SESSION['PaidEntries'] as $entry => $details) {
          $getEntry->execute([$entry, $_SESSION['UserID']]);
          $entry = $getEntry->fetch(PDO::FETCH_ASSOC);
          $notReady = !$entry['EntryProcessed'];
          $entryRequestDetails[] = [
            'label' => htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'][0]) . ' for ' . htmlspecialchars($entry['GalaName']),
            'amount' => $details['Amount']
          ];
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
                This entry will be locked from editing when you pay.
              </p>
              <?php } ?>

              <p>
                <strong>Fee &pound;<?=htmlspecialchars(number_format($details['Amount']/100 ,2, '.', ''))?></strong>
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
                <strong>&pound;<?=htmlspecialchars(number_format($total/100 ,2, '.', ''))?></strong>
              </p>
            </div>
          </div>
        </li>
      </ul>

      <h2 class="mb-3">Pay</h2>
        <div id="payment-request-card">
          <div class="card mb-3">
            <form>
              <div class="card-header" id="device-title">
                Pay quickly and securely
              </div>
              <div class="card-body">
                <div id="alert-placeholder"></div>
                <div id="payment-request-button">
                  <!-- A Stripe Element will be inserted here. -->
                </div>
              </div>
            </form>
          </div>

          <p class="text-center">Or</p>
        </div>

        <?php if (sizeof($cards) > 0) { ?>
        <div class="card mb-3" id="saved-cards">
          <form action="<?=autoUrl("galas/pay-for-entries/switch-method")?>" method="post">
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
                    <?=$card['Name']?> (<?=htmlspecialchars(getCardBrand($card['Brand']))?> ending <?=htmlspecialchars($card['Last4'])?>)
                  </option>
                  <?php } ?>
                </select>
              </div>

              <noscript>
                <p>
                  <button type="submit" class="btn btn-success">
                    Use selected card
                  </button>
                </p>
              </noscript>

              <?php if ($selected != null) { ?>
              <!-- Used to display form errors. -->
              <div id="saved-card-errors" role="alert"></div>

              <p class="mb-0">
                <button id="saved-card-button" class="btn btn-success btn-block" type="button" data-secret="<?= $intent->client_secret ?>">
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
            <form>
              <div class="form-group">
                <label for="new-cardholder-name">Cardholder name</label>
                <input id="new-cardholder-name" type="text" class="form-control">
              </div>
              <!-- placeholder for Elements -->
              <div class="form-group">
                <label for="card-element">
                  Credit or debit card
                </label>
                <div id="card-element" class="card-element">
                  <!-- A Stripe Element will be inserted here. -->
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

              <p>Your card details will be saved for use on future purchases</p>

              <!-- Used to display form errors. -->
              <div id="new-card-errors" role="alert"></div>

              <p class="mb-0">
                <button id="new-card-button" class="btn btn-success btn-block" type="button" data-secret="<?= $intent->client_secret ?>">
                  Pay now
                </button>
              </p>
            </form>
          </div>
        </div>

      <?php } ?>
    </div>
  </div>
</div>

<script>
var stripe = Stripe(<?=json_encode(env('STRIPE_PUBLISHABLE'))?>);
var cardButton = document.getElementById('new-card-button');
var clientSecret = cardButton.dataset.secret;
var elements = stripe.elements({
  fonts: [
    {
      cssSrc: 'https://fonts.googleapis.com/css?family=Open+Sans',
    },
  ]
});

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
// Try to match bootstrap 4 styling

var cardElement = elements.create('card', {
  iconStyle: 'solid',
  style: {
    base: {
      iconColor: '#ced4da',
      color: '#212529',
      fontWeight: 400,
      fontFamily: 'Open Sans, Segoe UI, sans-serif',
      fontSize: '16px',
      fontSmoothing: 'antialiased',
      ':-webkit-autofill': {
        color: '#868e96',
      },
      '::placeholder': {
        color: '#868e96',
      },
    },
    invalid: {
      iconColor: '#dc3545',
      color: '#dc3545',
    },
  },
});
cardElement.mount('#card-element');

var cardholderName = document.getElementById('new-cardholder-name');
<?php if ($selected != null) { ?>
var savedCardButton = document.getElementById('saved-card-button');
<?php } ?>
var clientSecret = cardButton.dataset.secret;

cardButton.addEventListener('click', function(ev) {
  stripe.handleCardPayment(
    clientSecret, cardElement, {
      payment_method_data: {
        billing_details: {name: cardholderName.value}
      }
    }
  ).then(function(result) {
    if (result.error) {
      // Display error.message in your UI.
      document.getElementById('new-card-errors').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
    } else {
      // The payment has succeeded. Display a success message.
      window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete/new"))?>);
    }
  });
});

var paymentRequest = stripe.paymentRequest({
  country: 'GB',
  currency: <?=json_encode($intent->currency)?>,
  total: {
    label: <?=json_encode(env('CLUB_NAME'))?>,
    amount: <?=$intent->amount?>,
  },
  displayItems: <?=json_encode($entryRequestDetails)?>,
  requestPayerName: true,
  requestPayerEmail: true,
  style: {
    paymentRequestButton: {
      type: 'buy', // default: 'default'
      theme: 'dark',// | 'light' | 'light-outline', // default: 'dark'
      height: '38px', // default: '40px', the width is always '100%'
    },
  },
});

var prButton = elements.create('paymentRequestButton', {
  paymentRequest: paymentRequest,
});

// Check the availability of the Payment Request API first.
paymentRequest.canMakePayment().then(function(result) {
  if (result) {
    prButton.mount('#payment-request-button');
  } else {
    document.getElementById('payment-request-card').style.display = 'none';
  }
});

paymentRequest.on('paymentmethod', function(ev) {
  stripe.confirmPaymentIntent(clientSecret, {
    payment_method: ev.paymentMethod.id,
  }).then(function(confirmResult) {
    if (confirmResult.error) {
      // Report to the browser that the payment failed, prompting it to
      // re-show the payment interface, or show an error message and close
      // the payment interface.
      ev.complete('fail');
    } else {
      // Report to the browser that the confirmation was successful, prompting
      // it to close the browser payment method collection interface.
      ev.complete('success'); 
      // Let Stripe.js handle the rest of the payment flow.
      stripe.handleCardPayment(clientSecret).then(function(result) {
        if (result.error) {
          // The payment failed -- ask your customer for a new payment method.
          document.getElementById('alert-placeholder').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
        } else {
          // The payment has succeeded.
          window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete/new"))?>);
        }
      });
    }
  });
});

<?php if ($selected != null) { ?>
savedCardButton.addEventListener('click', function(ev) {
  stripe.handleCardPayment(
    clientSecret,
    {
      payment_method: <?=json_encode($methodId)?>,
    }
  ).then(function(result) {
    if (result.error) {
      document.getElementById('saved-card-errors').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
      // Display error.message in your UI.
    } else {
      // The payment has succeeded. Display a success message.
      window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete"))?>);
    }
  });
});
<?php } ?>

</script>


<?php

include BASE_PATH . "views/footer.php"; ?>