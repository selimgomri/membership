<?php

global $db;

$getCard = $db->prepare("SELECT `Name`, Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName, MethodID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
$getCard->execute([$_SESSION['UserID'], $id]);

$card = $getCard->fetch(PDO::FETCH_ASSOC);

if ($card == null) {
  halt(404);
}

$pagetitle = htmlspecialchars(getCardBrand($card['Brand'])) . ' &#0149;&#0149;&#0149;&#0149; ' . htmlspecialchars($card['Last4']);

include BASE_PATH . 'views/header.php';

\Stripe\Stripe::setApiKey(env('STRIPE'));
$pm = \Stripe\PaymentMethod::retrieve($card['MethodID']);

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/cards")?>">Cards</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit card</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <div class="row align-items-center mb-2 text-dark">
        <div class="col-auto">
          <img src="<?=autoUrl("public/img/stripe/" . $card['Brand'] . ".png")?>" srcset="<?=autoUrl("public/img/stripe/" . $card['Brand'] . "@2x.png")?> 2x, <?=autoUrl("public/img/stripe/" . $card['Brand'] . "@3x.png")?> 3x" style="width:48px;"> <span class="sr-only"><?=htmlspecialchars(getCardBrand($card['Brand']))?></span>
        </div>
        <div class="col-auto">
          <h1 class="h1 my-0">
            <?=getCardBrand($card['Brand'])?> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($card['Last4'])?>
          </h1>
        </div>
      </div>
      <p class="lead">
      <?=htmlspecialchars(mb_convert_case($card['Funding'], MB_CASE_TITLE))?> card
      </p>

      <h2>Expiry</h2>
      <p>
        Expires at the end of <?=htmlspecialchars(date("F Y", strtotime($card['ExpYear'] . '-' . $card['ExpMonth'] . '-01')))?>.
      </p>

      <p>
        Depending on your issuing bank, we may be able automatically update your card details when it expires or is replaced. If this is the case, we'll update the last 4 digits and expiry date.
      </p>

      <p>
        If you don't want to have your cards automatically updated, you can opt out of these services by contacting your issuing bank.
      </p>

      <?php if (isset($pm->billing_details->name) || isset($pm->billing_details->address->line1) || isset($pm->billing_details->address->postal_code)) { ?>
      <h2>Billing details</h2>
      <address class="mb-3">
        <?php if (isset($pm->billing_details->name)) { ?>
        <strong><?=htmlspecialchars($pm->billing_details->name)?><br></strong>
        <?php } ?>
        <?php if (isset($pm->billing_details->address->line1)) { ?>
        <?=htmlspecialchars($pm->billing_details->address->line1)?><br>
        <?php } ?>
        <?php if (isset($pm->billing_details->address->postal_code)) { ?>
        <?=htmlspecialchars(mb_strtoupper($pm->billing_details->address->postal_code))?>
        <?php } ?>
      </address>
      <?php } ?>

      <h2>Forget card</h2>
      <p class="lead">Forget this card to remove it from your list</p>
      <p class="mb-5">
        <a href="<?=autoUrl("payments/cards/" . $id . "/delete")?>" class="btn btn-danger">
          Forget card
        </a>
      </p>

      <div class="text-muted">
        <p>
          We accept Visa, MasterCard and American Express.
        </p>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';