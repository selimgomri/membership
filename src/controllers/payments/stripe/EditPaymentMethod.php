<?php

global $db;

$getCard = $db->prepare("SELECT `Name`, Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName, MethodID FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
$getCard->execute([$_SESSION['UserID'], $id]);

$card = $getCard->fetch(PDO::FETCH_ASSOC);

if ($card == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($card['Name']) . ' - ' . htmlspecialchars($card['Brand']) . ' ' . htmlspecialchars($card['Last4']);

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
      <h1><i class="fa <?=htmlspecialchars(getCardFA($card['Brand']))?>" aria-hidden="true"></i> <?=htmlspecialchars($card['Name'])?></h1>
      <p class="lead">
        <?=getCardBrand($card['Brand'])?> <?=htmlspecialchars($card['Funding'])?> card ending in <spn class="mono"><?=htmlspecialchars($card['Last4'])?></span>.
      </p>

      <h2>Expiry</h2>
      <p>
        Expires at the end of <?=htmlspecialchars(date("F Y", strtotime($card['ExpYear'] . '-' . $card['ExpMonth'] . '-01')))?>.
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

      <?php if (isset($_SESSION['CardNameUpdate'])) { ?>
      <div class="alert alert-success">
        <p class="mb-0">
          <strong>We've updated the name of your card to <?=htmlspecialchars($card['Name'])?></strong>
        </p>
      </div>
      <?php unset($_SESSION['CardNameUpdate']); ?>
      <?php } ?>

      <form action="<?=currentUrl()?>" method="post" id="payment-form" class="mb-5">
        <div class="form-group">
          <label for="name">Name this card</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Card Name" required aria-describedby="cardNameHelp" value="<?=htmlspecialchars($card['Name'])?>">
          <small id="cardNameHelp" class="form-text text-muted">Name your card to help you select it more easily</small>
        </div>

        <p>
          <button class="btn btn-success">Rename card</button>
        </p>
      </form>

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