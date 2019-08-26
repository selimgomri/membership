<?php

// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(env('STRIPE'));

global $db;

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$getUserEmail->execute([$_SESSION['UserID']]);
$user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

$checkIfCustomer = $db->prepare("SELECT COUNT(*) FROM stripeCustomers WHERE User = ?");
$checkIfCustomer->execute([$_SESSION['UserID']]);

if ($checkIfCustomer->fetchColumn() == 0) {
  // See your keys here: https://dashboard.stripe.com/account/apikeys
  \Stripe\Stripe::setApiKey(env('STRIPE'));

  // Create a Customer:
  $customer = \Stripe\Customer::create([
    "name" => $user['Forename'] . ' ' . $user['Surname'],
    "description" => "Customer for " . $_SESSION['UserID'] . ' (' . $user['EmailAddress'] . ')',
    'email' => $user['EmailAddress'],
    'phone' => $user['Mobile']
  ]);

  // YOUR CODE: Save the customer ID and other info in a database for later.
  $id = $customer->id;
  $addCustomer = $db->prepare("INSERT INTO stripeCustomers (User, CustomerID) VALUES (?, ?)");
  $addCustomer->execute([
    $_SESSION['UserID'],
    $id
  ]);
}

$setupIntent = null;
if (!isset($_SESSION['StripeSetupIntentId'])) {
  $setupIntent = \Stripe\SetupIntent::create();
  $_SESSION['StripeSetupIntentId'] = $setupIntent->id;
} else {
  $setupIntent = \Stripe\SetupIntent::retrieve($_SESSION['StripeSetupIntentId']);
}

$countries = getISOAlpha2Countries();

include BASE_PATH . 'views/header.php';

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
  border-radius: 0.25rem;
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
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/cards")?>">Cards</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add card</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <h1>Add a payment card</h1>

      <?php if (isset($_SESSION['PayCardError'])) { ?>
      <div class="alert alert-danger">
        <p class="mb-0">
          <strong>An error occurred</strong>
        </p>
        <?php if (isset($_SESSION['PayCardErrorMessage'])) { ?>
        <p class="mb-0"><?=htmlspecialchars($_SESSION['PayCardErrorMessage'])?></p>
        <?php } ?>
      </div>
      <?php unset($_SESSION['PayCardError']); unset($_SESSION['PayCardErrorMessage']); ?>
      <?php } ?>

      <form action="<?=currentUrl()?>" method="post" id="payment-form" class="mb-5 needs-validation" novalidate>
        <div class="form-group">
          <label for="cardholder-name">Cardholder name</label>
          <input type="text" class="form-control" id="cardholder-name" placeholder="C F Frost" required aria-describedby="cardholder-name-help" autocomplete="cc-name">
          <small id="cardholder-name-help" class="form-text text-muted">The name shown on your card</small>
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
          <select class="custom-select" required id="addr-country" autocomplete="country">
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
              <span class="input-group-text" id="card-brand-element"><i class="fa fa-fw fa-credit-card" aria-hidden="true"></i></span>
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

        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>

        <p>
          <button id="card-button" class="btn btn-success" data-secret="<?= $setupIntent->client_secret ?>">Add payment card</button>
        </p>
      </form>

      <div class="text-muted">
        <p>
          We accept Visa, MasterCard and American Express.
        </p>
      </div>
    </div>
  </div>
</div>

<script>

function setCardBrandIcon(brand) {
  var content = '<i class="fa fa-fw fa-credit-card" aria-hidden="true"></i>';
  if (brand == 'visa') {
    content = '<i class="fa fa-cc-visa" aria-hidden="true"></i>';
  } else if (brand == 'mastercard') {
    content = '<i class="fa fa-cc-mastercard" aria-hidden="true"></i>';
} else if (brand == 'amex') {
    content = '<i class="fa fa-cc-amex" aria-hidden="true"></i>';
  }
  document.getElementById('card-brand-element').innerHTML = content;
}

function disableButtons() {
  document.querySelectorAll('.pm-can-disable').forEach(elem => {
    elem.disabled = true;
  });
}

function enableButtons() {
  document.querySelectorAll('.pm-can-disable').forEach(elem => {
    elem.disabled = false;
  });
}

var stripe = Stripe('<?=htmlspecialchars(env('STRIPE_PUBLISHABLE'))?>');

var elements = stripe.elements({
  fonts: [
    {
      cssSrc: 'https://fonts.googleapis.com/css?family=Open+Sans',
    },
  ]
});

var style = {
  base: {
    iconColor: '#ced4da',
    lineHeight: '1.5',
    height: '1.5rem',
    color: '#212529',
    fontWeight: 400,
    fontFamily: 'Open Sans, Segoe UI, sans-serif',
    fontSize: '16px',
    fontSmoothing: 'antialiased',
    '::placeholder': {
      color: '#868e96',
    },
  },
  invalid: {
    color: '#212529',
  },
}

// Create an instance of the card Element.
/*
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
*/

// Add an instance of the card Element into the `card-element` <div>.
//cardElement.mount('#card-element');

var cardNumberElement = elements.create('cardNumber', {
  style: style
});
cardNumberElement.mount('#card-number-element');
var cardExpiryElement = elements.create('cardExpiry', {
  style: style
});
cardExpiryElement.mount('#card-expiry-element');
var cardCvcElement = elements.create('cardCvc', {
  style: style
});
cardCvcElement.mount('#card-cvc-element');

function cardChangeEvent(event, elementId) {
  var displayError = document.getElementById(elementId);
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
}

cardNumberElement.addEventListener('change', function(event) {
  cardChangeEvent(event, 'card-number-element-errors');
  if (event.brand) {
  	setCardBrandIcon(event.brand);
  }
});
cardExpiryElement.addEventListener('change', function(event) {
  cardChangeEvent(event, 'card-expiry-element-errors');
});
cardCvcElement.addEventListener('change', function(event) {
  cardChangeEvent(event, 'card-cvc-element-errors');
});

var cardholderName = document.getElementById('cardholder-name');
var cardholderAddress1 = document.getElementById('addr-line-1');
var cardholderZip = document.getElementById('addr-post-code');
cardholderZip.addEventListener('change', function(event) {
  //cardNumberElement.update({value: {postalCode: event.target.value.toUpperCase()}});
});
var cardholderCountry = document.getElementById('addr-country');
var cardButton = document.getElementById('card-button');
var clientSecret = cardButton.dataset.secret;

var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();
  stripe.handleCardSetup(
    clientSecret, cardNumberElement, {
      payment_method_data: {
        billing_details: {
          name: cardholderName.value,
          address: {
            line1: cardholderAddress1.value,
            postal_code: cardholderZip.value,
            country: cardholderCountry.value,
          },
        }
      }
    }
  ).then(function(result) {
    var displayError = document.getElementById('card-errors');
    if (result.error) {
      // Display error.message in your UI.
      displayError.innerHTML = '<div class="alert alert-danger" id="card-errors-message"></div>'
      document.getElementById('card-errors-message').textContent = result.error.message;
    } else {
      // The setup has succeeded. Display a success message.
      disableButtons();
      displayError.innerHTML = '<div class="alert alert-success" id="card-errors-message"></div>'
      document.getElementById('card-errors-message').textContent = 'Card setup successfully. Please wait while we redirect you.';
      // The payment has succeeded. Display a success message.
      var form = document.getElementById('payment-form');
      //var hiddenInput = document.createElement('input');
      //hiddenInput.setAttribute('type', 'hidden');
      //hiddenInput.setAttribute('name', 'stripeToken');
      //hiddenInput.setAttribute('value', token.id);
      //form.appendChild(hiddenInput);
      // Submit the form
      form.submit();
    }
  });
});


</script>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';