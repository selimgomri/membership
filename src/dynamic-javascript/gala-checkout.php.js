<?php

if (!isset($_SESSION['PaidEntries'])) {
  halt(404);
}

\Stripe\Stripe::setApiKey(env('STRIPE'));
if (env('STRIPE_APPLE_PAY_DOMAIN')) {
  \Stripe\ApplePayDomain::create([
    'domain_name' => env('STRIPE_APPLE_PAY_DOMAIN')
  ]);
}

global $db;

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

$intent = null;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  halt(404);
} else {
  $intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);
}

if ($intent->status == 'succeeded') {
  halt(404);
}

$entryRequestDetails[] = [
  'label' => 'Subtotal',
  'amount' => $intent->amount
];

header("content-type: application/x-javascript");

?>

function disableButtons() {
  document.querySelectorAll('button').forEach(elem => {
    elem.disabled = true;
  });
}

function enableButtons() {
  document.querySelectorAll('button').forEach(elem => {
    elem.disabled = false;
  });
}

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
var successAlert = '<div class="alert alert-success"><p class="mb-0"><strong>Payment Successful</strong></p><p class="mb-0">Please wait while we redirect you</p></div>';

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
// Try to match bootstrap 4 styling

var cardElement = elements.create('card', {
  iconStyle: 'solid',
  hidePostalCode: true,
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
var cardholderAddress1 = document.getElementById('addr-line-1');
var cardholderZip = document.getElementById('addr-post-code');
cardholderZip.addEventListener('change', function(event) {
  cardElement.update({value: {postalCode: event.target.value.toUpperCase()}});
});
var cardholderCountry = document.getElementById('addr-country');

var savedCardButton = document.getElementById('saved-card-button');
var clientSecret = cardButton.dataset.secret;

var form = document.getElementById('new-card-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();
  stripe.handleCardPayment(
    clientSecret, cardElement, {
      payment_method_data: {
        billing_details: {
          name: cardholderName.value,
          address: {
            line1: cardholderAddress1.value,
            zip: cardholderZip.postal_code,
            country: cardholderCountry.value,
          },
        }
      }
    }
  ).then(function(result) {
    if (result.error) {
      // Display error.message in your UI.
      document.getElementById('new-card-errors').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
    } else {
      // Disable buttons
      disableButtons();
      document.getElementById('new-card-errors').innerHTML = successAlert;
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
});

var prButton = elements.create('paymentRequestButton', {
  paymentRequest: paymentRequest,
  style: {
    paymentRequestButton: {
      type: 'default', // default: 'default'
      theme: 'dark',// | 'light' | 'light-outline', // default: 'dark'
      height: '38px', // default: '40px', the width is always '100%'
    },
  },
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
      document.getElementById('alert-placeholder').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
    } else {
      // Report to the browser that the confirmation was successful, prompting
      // it to close the browser payment method collection interface.
      ev.complete('success');
      // Let Stripe.js handle the rest of the payment flow.
      stripe.retrievePaymentIntent(
        clientSecret
      ).then(function(result) {
        if (!result.error) {
          if (result.paymentIntent.status == 'succeeded') {
            // Disable buttons
            disableButtons();
            document.getElementById('alert-placeholder').innerHTML = successAlert;
            window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete/new"))?>);
          } else {
            stripe.handleCardPayment(clientSecret).then(function(result) {
              if (result.error) {
                // The payment failed -- ask your customer for a new payment method.
                document.getElementById('alert-placeholder').innerHTML = '<div class="alert alert-danger mt-3"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
              } else {
                // The payment has succeeded.
                // Disable buttons
                disableButtons();
                document.getElementById('new-card-errors').innerHTML = successAlert;
                window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete/new"))?>);
              }
            });
          }
        }
      });
    }
  });
});

if (savedCardButton != null) {
  savedCardButton.addEventListener('click', function(ev) {
    stripe.handleCardPayment(
      clientSecret,
      {
        payment_method: savedCardButton.dataset.methodId,
      }
    ).then(function(result) {
      if (result.error) {
        document.getElementById('saved-card-errors').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
        // Display error.message in your UI.
      } else {
        // The payment has succeeded. Display a success message.
        disableButtons();
        document.getElementById('alert-placeholder').innerHTML = successAlert;
        window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete"))?>);
      }
    });
  });
}