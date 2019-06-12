<?php

global $db;

function getCard($brand) {
  if ($brand == 'Visa') {
    return 'fa-cc-visa';
  } else if ($brand == 'MasterCard') {
    return 'fa-cc-mastercard';
  } else if ($brand == 'American Express') {
    return 'fa-cc-amex';
  } else if ($brand == 'Diners Club') {
    return 'fa-cc-diners-club';
  } else if ($brand == 'Discover') {
    return 'fa-cc-discover';
  } else if ($brand == 'JCB') {
    return 'fa-cc-jcb';
  } else if ($brand == 'UnionPay') {
    return 'fa-cc-stripe';
  } else {
    return 'fa-cc-stripe';
  }
}

\Stripe\Stripe::setApiKey(env('STRIPE'));
$paymentsMeths = \Stripe\PaymentMethod::all(["customer" => "cus_FF5F1cnWIA7UAI", "type" => "card"]);

$getCards = $db->prepare("SELECT Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ?");
$getCards->execute([$_SESSION['UserID']]);
$card = $getCards->fetch(PDO::FETCH_ASSOC);

$pagetitle = 'Payment Cards';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Payment Cards</h1>
      <p class="lead">Introducing new ways to pay!</p>

      <?php if (isset($_SESSION['PayCardSetupSuccess'])) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>We have added your new card</strong>
          </p>
          <?php if (isset($_SESSION['PayCardSetupSuccessBrand'])) { ?>
          <p class="mb-0">Your <?=htmlspecialchars($_SESSION['PayCardSetupSuccessBrand'])?> card is now ready.</p>
          <?php } ?>
        </div>
      <?php } ?>

      <div class="cell d-inline-block">
        <p>
          We proudly accept all major credit and debit cards!
        </p>
        <p>
          <i class="fa fa-2x fa-cc-visa" aria-hidden="true"></i> <i class="fa fa-2x fa-cc-mastercard" aria-hidden="true"></i> <i class="fa fa-2x fa-cc-amex" aria-hidden="true"></i>
        </p>
      </div>

      <?php if ($card != null) { ?>
      <ul class="list-group mb-3">
      <?php do { ?>
        <li class="list-group-item">
          <p>
            <i class="fa fa-3x <?=htmlspecialchars(getCard($card['Brand']))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars($card['Brand'])?></span>
          </p>
          <p class="lead mb-0">
            Card ending <?=htmlspecialchars($card['Last4'])?> (<?=htmlspecialchars($card['Funding'])?> card)
          </p>
        </li>
      <?php } while ($card = $getCards->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>
      <?php } else { ?>
      <div class="alert alert-warning">
        You have no payment cards available.
      </div>
      <?php } ?>

      <p>
        <a href="<?=autoUrl("payments/cards/add")?>" class="btn btn-success">
          Add a card
        </a>
      </p>
      <p>
        We accept Visa, MasterCard and American Express.
      </p>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';