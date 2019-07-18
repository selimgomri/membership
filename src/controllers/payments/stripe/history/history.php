<?php

global $db;

\Stripe\Stripe::setApiKey(env('STRIPE'));

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

$getCount = $db->prepare("SELECT COUNT(*) FROM stripePayments WHERE User = ?");
$getCount->execute([$_SESSION['UserID']]);
$count = $getCount->fetchColumn();

if ($start > $count) {
  halt(404);
}

$payments = $db->prepare("SELECT stripePayments.ID, stripePayments.DateTime, stripePayMethods.Brand, stripePayMethods.Last4, stripePayments.Amount FROM stripePayments LEFT JOIN stripePayMethods ON stripePayments.Method = stripePayMethods.ID WHERE User = :user ORDER BY `DateTime` DESC LIMIT :offset, :num;");
$payments->bindValue(':user', $_SESSION['UserID'], PDO::PARAM_INT);
$payments->bindValue(':offset', $start, PDO::PARAM_INT); 
$payments->bindValue(':num', 10, PDO::PARAM_INT); 
$payments->execute();

$pagetitle = 'Card Payment History';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments/cards")?>">Cards</a></li>
      <li class="breadcrumb-item active" aria-current="page">History</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <h1>Card payment history</h1>
      <p class="lead">Previous card payments</p>

      <div class="list-group">
        <?php while ($pm = $payments->fetch(PDO::FETCH_ASSOC)) {
          $date = new DateTime($pm['DateTime'], new DateTimeZone('UTC'));
          $date->setTimezone(new DateTimeZone('Europe/London'));
        ?>
        <a href="<?=autoUrl("payments/card-transactions/" . $pm['ID'])?>" class="list-group-item list-group-item-action">
          <h2 class="mb-0"><i class="fa <?=htmlspecialchars(getCardFA($pm['Brand']))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($pm['Brand']))?></span> <?=htmlspecialchars($pm['Last4'])?></h2>
          <p class="lead">At <?=$date->format("H:i \o\\n j F Y")?></p>
          <p class="mono mb-0">&pound;<?=number_format($pm['Amount']/100, 2, '.', '')?></p>
        </a>
        <?php } ?>
      </div>

      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          <?php if ($count <= 10) { ?>
          <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
          <?php } else if ($count <= 20) { ?>
            <?php if ($page == 1) { ?>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
      			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
      			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page-1 ?>">Previous</a></li>
      	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
      	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php } ?>
          <?php } else { ?>
      			<?php if ($page == 1) { ?>
      			<li class="page-item active"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
      	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
      			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
      			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
      			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page-1 ?>">Previous</a></li>
            <?php if ($page > 2) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page-2 ?>"><?php echo $page-2 ?></a></li>
      	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
      	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
      			<?php if ($count > $page*10) { ?>
      	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <?php if ($count > $page*10+10) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
            <?php } ?>
      	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("payments/card-transactions/page/"); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } ?>
          <?php } ?>
        <?php }
        } ?>
        </ul>
      </nav>

    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';