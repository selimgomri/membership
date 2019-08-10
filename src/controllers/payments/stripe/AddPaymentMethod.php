<?php

// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(env('STRIPE'));

global $db;

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE UserID = ?");
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
    "description" => "Customer for " . $_SESSION['UserID'] . ' (' . $user['EmailAddress'] . ')'
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

      <form action="<?=currentUrl()?>" method="post" id="payment-form" class="mb-5">
        <div class="form-group">
          <label for="cardholder-name">Cardholder name</label>
          <input type="text" class="form-control" id="cardholder-name" placeholder="C F Frost" required aria-describedby="cardholder-name-help" autocomplete="cc-name">
          <small id="cardholder-name-help" class="form-text text-muted">The name shown on your card</small>
        </div>

        <div class="form-group">
          <label for="addr-line-1">Address line 1</label>
          <input type="text" class="form-control" id="addr-line-1" placeholder="1 Burns Green" required autocomplete="address-line1">
        </div>

        <div class="form-group">
          <label for="addr-post-code">Post Code</label>
          <input type="text" class="form-control text-uppercase" id="addr-post-code" placeholder="NE99 1AA" required autocomplete="postal-code">
        </div>

        <div class="form-group">
          <label for="addr-post-code">Country</label>
          <select class="custom-select" required id="addr-country" autocomplete="country">
            <?php foreach ($countries as $code => $name) { ?>
            <option <?php if ($code == 'GB') { ?>selected<?php } ?> value="<?=htmlspecialchars($code)?>"><?=htmlspecialchars($name)?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="card-element">
            Credit or debit card
          </label>
          <div id="card-element" class="card-element">
            <!-- A Stripe Element will be inserted here. -->
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
var stripe = Stripe('<?=htmlspecialchars(env('STRIPE_PUBLISHABLE'))?>');

var elements = stripe.elements({
  fonts: [
    {
      cssSrc: 'https://fonts.googleapis.com/css?family=Open+Sans',
    },
  ]
});

// Create an instance of the card Element.
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

// Add an instance of the card Element into the `card-element` <div>.
cardElement.mount('#card-element');

cardElement.addEventListener('change', function(event) {
  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.innerHTML = '<div class="alert alert-danger" id="card-errors-message"></div>'
    document.getElementById('card-errors-message').textContent = event.error.message;
  } else {
    displayError.innerHTML = '';
  }
});

var cardholderName = document.getElementById('cardholder-name');
var cardholderAddress1 = document.getElementById('addr-line-1');
var cardholderZip = document.getElementById('addr-post-code');
cardholderZip.addEventListener('change', function(event) {
  cardElement.update({value: {postalCode: event.target.value.toUpperCase()}});
});
var cardholderCountry = document.getElementById('addr-country');
var cardButton = document.getElementById('card-button');
var clientSecret = cardButton.dataset.secret;

var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();
  stripe.handleCardSetup(
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
    var displayError = document.getElementById('card-errors');
    if (result.error) {
      // Display error.message in your UI.
      displayError.innerHTML = '<div class="alert alert-danger" id="card-errors-message"></div>'
      document.getElementById('card-errors-message').textContent = result.error.message;
    } else {
      // The setup has succeeded. Display a success message.
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

<?php

include BASE_PATH . 'views/footer.php';