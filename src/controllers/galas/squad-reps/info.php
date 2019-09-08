<?php

global $db;

$squad = null;

if (isset($_GET['squad'])) {
  // Verify this squad is allowed for the user

  $squad = $_GET['squad'];
}

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
	halt(404);
}

$getEntries = $db->prepare("SELECT members.UserID `user`, 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, MForename, MSurname, EntryID, Charged, FeeToPay, MandateID, EntryProcessed Processed, Refunded, galaEntries.AmountRefunded, Intent, stripePayMethods.Brand, stripePayMethods.Last4, Funding, ASANumber FROM ((((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN stripePayments ON galaEntries.StripePayment = stripePayments.ID) LEFT JOIN stripePayMethods ON stripePayMethods.ID = stripePayments.Method) WHERE galaEntries.GalaID = ? ORDER BY MForename ASC, MSurname ASC");
$getEntries->execute([$id]);
$entry = $getEntries->fetch(PDO::FETCH_ASSOC);

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

$countChargeable = 0;

$pagetitle = "Squad Rep View for " . htmlspecialchars($gala['name']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("squad-reps")?>">Squad Reps</a></li>
			<li class="breadcrumb-item active" aria-current="page">View gala entries</li>
		</ol>
	</nav>

	<h1>SQUAD NAME entries for <?=htmlspecialchars($gala['name'])?></h1>
	<?php if ($gala['fixed']) { ?>
	<p class="lead">
		This gala costs &pound;<?=htmlspecialchars($gala['fee'])?>
	</p>
	<?php } else { ?>
	<p class="lead">
		There is no fixed fee for this gala
	</p>
  <?php } ?>

	<div class="row">

    <?php if ($squad != null) { ?>
    <div class="col-md-8">

			<h2>Entries for this gala</h2>	

      <?php if ($entry != null) { ?>
      <ul class="list-group mb-3">
        <?php do { ?>
          <?php $hasNoDD = ($entry['MandateID'] == null) || (getUserOption($entry['user'], 'GalaDirectDebitOptOut')); ?>
          <?php $amountRefundable = ((int) $entry['FeeToPay']*100) - ($entry['AmountRefunded']); ?>
        <?php if ($entry['Processed'] && $entry['Charged']) { $countChargeable++; } ?>
        <li class="list-group-item <?php if (bool($entry['Charged'])) {?>list-group-item-success<?php } ?>" id="refund-box-<?=htmlspecialchars($entry['EntryID'])?>">
          <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-6">
              <h3><?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?></h3>

              <p>
                <strong>Swim England Number:</strong> <?=htmlspecialchars($entry['ASANumber'])?>
              </p>

              <p class="mb-0">
                <?=htmlspecialchars($entry['MForename'])?> was entered in;
              </p>
              <ul class="list-unstyled">
              <?php $count = 0; ?>
              <?php foreach($swimsArray as $colTitle => $text) { ?>
                <?php if ($entry[$colTitle]) { $count++; ?>
                <li><?=$text?></li>
                <?php } ?>
              <?php } ?>
            </div>
            <div class="col">
              <div class="d-sm-none mb-3"></div>
              <?php if ($entry['Intent'] != null) { ?>
              <p>
                <strong>
                  Paid with
                </strong><br>
                <i class="fa <?=htmlspecialchars(getCardFA($entry['Brand']))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($entry['Brand']))?></span> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($entry['Last4'])?>
              </p>
              <?php } ?>

              <p>
                <?php if ($gala['fixed']) { ?>
                <?=$count?> &times; &pound;<?=htmlspecialchars(number_format($gala['fee'], 2))?>
                <?php } else { ?>
                <?=$count?> entries at no fixed fee
                <?php } ?>
              </p>

              <p class="mb-0">
                <strong>
                <?php if (bool($entry['Charged'])) { ?>Amount charged<?php } else { ?>Fee to pay<?php } ?>
                </strong><br>
                &pound;<?=htmlspecialchars(number_format($entry['FeeToPay'], 2))?>
              </p>

              <?php if ($entry['Refunded']) { ?>
              <p class="mt-3 mb-0">
                <strong>
                  Amount refunded
                </strong><br>
                &pound;<?=number_format($entry['AmountRefunded']/100, 2)?> has been refunded<?php if ($entry['Intent'] != null) { ?> to <?=htmlspecialchars(getCardBrand($entry['Brand']))?> <?=htmlspecialchars($entry['Funding'])?> card ending <?=htmlspecialchars($entry['Last4'])?><?php } ?>
              </p>

                <?php if ($hasNoDD && $entry['Intent'] == null) { ?>
                <p class="mt-3 mb-0">
                  The parent does not have a Direct Debit set up or has requested to pay by other means. Refund should be by cash, cheque or bank transfer.
                </p>
                <?php } else if (!$hasNoDD && $entry['Intent'] == null) { ?>
                <p class="mt-3 mb-0">
                  This gala will be refunded as a discount on the parent's next direct debit payment.
                </p>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </li>
        <?php } while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>

			<?php } else { ?>
			<div class="alert alert-warning">
				<strong>There are no entries for this gala</strong>
			</div>
      <?php } ?>
    </div>
    <?php } ?>
  
    <div class="col">
      <?php if (true) { ?>
      <h2>Select a squad</h2>
      <p class="lead">Select a squad to view entries for</p>
      <?php } else { ?>
      <h2>You do not have squad rep permissions</h2>
      <p class="lead">
        Only squad reps can view entries
      </p>
      <?php } ?>
    </div>
  </div>
</div>

<?php if (isset($_SESSION['OverhighChargeAmount'])) {
	unset($_SESSION['OverhighChargeAmount']);
} ?>

<?php

include BASE_PATH . 'views/footer.php';