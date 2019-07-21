<?php

global $db;

\Stripe\Stripe::setApiKey(env('STRIPE'));
//$paymentsMeths = \Stripe\PaymentMethod::all(["customer" => "cus_FF5F1cnWIA7UAI", "type" => "card"]);

$expMonth = date("m");
$expYear = date("Y");

$getCards = $db->prepare("SELECT stripePayMethods.ID, `Name`, Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND Reusable = ? AND (ExpYear > ? OR (ExpYear = ? AND ExpMonth >= ?)) ORDER BY `Name` ASC");
$getCards->execute([$_SESSION['UserID'], 1, $expYear, $expYear, $expMonth]);
$card = $getCards->fetch(PDO::FETCH_ASSOC);

$pagetitle = 'Payment Cards';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item active" aria-current="page">Cards</li>
    </ol>
  </nav>

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
      <?php
      unset($_SESSION['PayCardSetupSuccessBrand']);
      unset($_SESSION['PayCardSetupSuccess']);
      ?>

      <?php if (isset($_SESSION['CardDeleted'])) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>Card deleted</strong>
          </p>
          <p class="mb-0">
            Your card details will no longer be shown in the list of saved cards.
          </p>
        </div>
      <?php unset($_SESSION['CardDeleted']); } ?>

      <div>
        <p>
          We proudly accept all major credit and debit cards!
        </p>
        <p>
          <i class="fa fa-2x fa-cc-visa" aria-hidden="true"></i> <i class="fa fa-2x fa-cc-mastercard" aria-hidden="true"></i> <i class="fa fa-2x fa-cc-amex" aria-hidden="true"></i>
        </p>
      </div>

      <?php if ($card != null) { ?>
      <div class="list-group mb-3">
      <?php do { ?>
        <a href="<?=autoUrl("payments/cards/" . $card['ID'])?>" class="list-group-item list-group-item-action">
          <div class="row align-items-center mb-3 text-dark">
            <div class="col-auto">
              <img src="<?=autoUrl("public/img/stripe/" . $card['Brand'] . ".png")?>" srcset="<?=autoUrl("public/img/stripe/" . $card['Brand'] . "@2x.png")?> 2x, <?=autoUrl("public/img/stripe/" . $card['Brand'] . "@3x.png")?> 3x" style="width:40px;"> <span class="sr-only"><?=htmlspecialchars(getCardBrand($card['Brand']))?></span>
            </div>
            <div class="col-auto">
              <h2 class="h1 my-0">
                <?=htmlspecialchars($card['Name'])?>
              </h2>
            </div>
          </div>
          <p class="lead">
            Card ending <?=htmlspecialchars($card['Last4'])?> (<?=htmlspecialchars($card['Funding'])?> card)
          </p>

          <p class="mb-0">
            <span class="text-primary">
              Edit card
            </span>
          </p>
        </a>
      <?php } while ($card = $getCards->fetch(PDO::FETCH_ASSOC)); ?>
      </div>
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