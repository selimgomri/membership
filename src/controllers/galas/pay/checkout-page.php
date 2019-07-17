<?php

\Stripe\Stripe::setApiKey(env('STRIPE'));

global $db;

$getCards = $db->prepare("SELECT stripePayMethods.ID, `MethodID`, stripePayMethods.Customer, stripePayMethods.Name, stripePayMethods.Last4, stripePayMethods.Brand FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ?");
$getCards->execute([$_SESSION['UserID']]);
$card = $getCards->fetch(PDO::FETCH_ASSOC);

if ($card == null) {
  halt(404);
}

$methodId = $card['MethodID'];
$customerID = $card['Customer'];

$selected = null;
if (isset($_SESSION['GalaPaymentMethodID'])) {
  $selected = $_SESSION['GalaPaymentMethodID'];
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

if (!isset($_SESSION['PaidEntries'])) {
  halt(404);
}

$total = 0;

foreach ($_SESSION['PaidEntries'] as $entry => $details) {
  $total += $details['Amount'];
}

$intent = null;

if (!isset($_SESSION['GalaPaymentIntent'])) {
  $intent = \Stripe\PaymentIntent::create([
    'amount' => $total,
    'currency' => 'gbp',
    'payment_method_types' => ['card'],
    'payment_method' => $methodId,
    'customer' => $customerID,
    'confirm' => false,
  ]);
  $_SESSION['GalaPaymentIntent'] = $intent->id;
} else {
  $intent = \Stripe\PaymentIntent::retrieve($_SESSION['GalaPaymentIntent']);
}

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
      <p>If you haven't opted out of direct debit gala payments and you don't make a payment by card, you'll be automatically charged for gala entries as part of your monthly payment when the gala coordinator submits the entries to the host club.</p>

      <h2>Payment method</h2>

      <form action="<?=autoUrl("galas/pay-for-entries/switch-method")?>" method="post">

        <div class="form-group">
          <label for="method">Payment card</label>
          <select class="custom-select" name="method" id="method">
            <option>Select a payment card</option>
            <?php do { ?>
            <option value="<?=$card['ID']?>" <?php if ($selected == $card['ID']) { ?>selected<?php } ?>>
              <?=$card['Name']?> (<?=htmlspecialchars(getCardBrand($card['Brand']))?> ending <?=htmlspecialchars($card['Last4'])?>)
            </option>
            <?php } while ($card = $getCards->fetch(PDO::FETCH_ASSOC)); ?>
          </select>
        </div>

        <p>
          <button type="submit" class="btn btn-success">
            Use selected card
          </button>
        </p>

      </form>

      <?php if (isset($_SESSION['GalaPaymentMethodID'])) { ?>

      <h2>Payment details</h2>

      <ul class="list-group mb-3">
        <?php foreach ($_SESSION['PaidEntries'] as $entry => $details) {
          $getEntry->execute([$entry, $_SESSION['UserID']]);
          $entry = $getEntry->fetch(PDO::FETCH_ASSOC);
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
                <strong>Total</strong>
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

      <div id="alert-placeholder"></div>

      <form>
        <input id="cardholder-name" type="hidden">
        <!-- placeholder for Elements -->
        <div id="card-element"></div>
        <p>
          <button id="card-button" class="btn btn-success" type="button" data-secret="<?= $intent->client_secret ?>">
            Pay now
          </button>
        </p>
      </form>

      <?php } ?>
    </div>
  </div>
</div>

<?php if (isset($_SESSION['GalaPaymentMethodID'])) { ?>
<script>
var stripe = Stripe(<?=json_encode(env('STRIPE_PUBLISHABLE'))?>);
var cardButton = document.getElementById('card-button');
var clientSecret = cardButton.dataset.secret;

cardButton.addEventListener('click', function(ev) {
  stripe.handleCardPayment(
    clientSecret,
    {
      payment_method: <?=json_encode($methodId)?>,
    }
  ).then(function(result) {
    if (result.error) {
      document.getElementById('alert-placeholder').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred trying to take your payment using card ending with ' + result.error.payment_method.card.last4 + '</strong></p><p class="mb-0">' + result.error.message + '</p></div>';
      // Display error.message in your UI.
    } else {
      // The payment has succeeded. Display a success message.
      window.location.replace(<?=json_encode(autoUrl("galas/pay-for-entries/complete"))?>);
    }
  });
});

</script>
<?php } ?>


<?php include BASE_PATH . "views/footer.php"; ?>