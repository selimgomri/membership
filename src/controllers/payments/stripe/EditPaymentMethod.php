<?php

global $db;

$getCard = $db->prepare("SELECT `Name`, Last4, Brand, ExpMonth, ExpYear, Funding, PostCode, Line1, Line2, CardName FROM stripePayMethods INNER JOIN stripeCustomers ON stripeCustomers.CustomerID = stripePayMethods.Customer WHERE User = ? AND stripePayMethods.ID = ?");
$getCard->execute([$_SESSION['UserID'], $id]);

$card = $getCard->fetch(PDO::FETCH_ASSOC);

if ($card == null) {
  halt(404);
}

$pagetitle = htmlspecialchars($card['Name']) . ' - ' . htmlspecialchars($card['Brand']) . ' ' . htmlspecialchars($card['Last4']);

include BASE_PATH . 'views/header.php';

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

      <p>
        Expires at the end of <?=htmlspecialchars(date("F Y", strtotime($card['ExpYear'] . '-' . $card['ExpMonth'] . '-01')))?>.
      </p>

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