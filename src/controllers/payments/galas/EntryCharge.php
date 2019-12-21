<?php

global $db;

$disabled = "";

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
	halt(404);
}

$galaData = new GalaPrices($db, $id);

$getEntries = $db->prepare("SELECT members.UserID `user`, 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, MForename, MSurname, EntryID, Charged, FeeToPay, MandateID, EntryProcessed Processed FROM ((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) WHERE galaEntries.GalaID = ? ORDER BY MForename ASC, MSurname ASC");
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

$numFormatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

$pagetitle = "Charge Parents for " . htmlspecialchars($gala['name']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments/galas")?>">Galas</a></li>
			<li class="breadcrumb-item active" aria-current="page">Charge for gala</li>
		</ol>
	</nav>

	<h1>Charge Parents for <?=htmlspecialchars($gala['name'])?></h1>
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
		<div class="col-md-8">

			<?php if (isset($_SESSION['ChargeUsersSuccess'])) { 
				unset($_SESSION['ChargeUsersSuccess']); ?>
				<div class="alert alert-success">
					<strong>We've successfully charged the parents</strong>
				</div>
			<?php } ?>

			<?php if (isset($_SESSION['ChargeUsersFailure'])) { 
				unset($_SESSION['ChargeUsersFailure']); ?>
				<div class="alert alert-warning">
					<strong>An error occurred.</strong> Please try again later.
				</div>
			<?php } ?>

			<h2>How to charge parents</h2>
			<p>
				Please review the charges shown for all entries listed below. You can edit the amount to be charged for each entry.
			</p>

			<?php if ($gala['fixed']) { ?>
			<p>
				As this gala has a fixed fee for each swim, the amount due for each entry should already be correct but it is your responsibility to verify this.
			</p>
			<?php } else { ?>
			<p class="lead">
				As there is no fixed fee for each swim at this gala, you will have to verify the amount shown next to each swim as this amount has been supplied by parents. You may not be able to recover losses incurred due to incorrect data entry by parents.
			</p>
			<?php } ?>

			<p>
				Please note that if this is a SPORTSYSTEMS gala with a <strong>locked entry file</strong> you will need to discount any entries made by parents which were not accepted by the SPORTSYSTEMS Entry Manager as these will not be listed on the entry report and may lead to overpayment. Alternatively, you may wish to edit the gala entry itself  before proceeding on this page to remove the swim in question - This may help you when processing refunds.
			</p>

			<p>
				Entries <strong><span class="text-success">highlighted in Green</span></strong> have already been marked as paid.
			</p>

			<p>
				Entries <strong><span class="text-danger">highlighted in Red</span></strong> cannot be paid for by Direct Debit as either the parent does not have a Direct Debit Mandate, the parent has opted out of Direct Debit gala payments or the swimmer has no connected parent.
			</p>

			<p>
				This software will not let you charge more than &pound;150 for any individual gala entry.
			</p>

			<h2>Entries for this gala</h2>	

			<form method="post" onsubmit="return confirm('Are you sure you want to charge parents? You won\'t be able to modify charges once you proceed.');">
				<?php if ($entry != null) { ?>
				<ul class="list-group mb-3">
					<?php do { ?>
					<?php $hasNoDD = ($entry['MandateID'] == null) || (getUserOption($entry['user'], 'GalaDirectDebitOptOut')); ?>
					<?php $notReady = !$entry['Processed']; ?>
					<?php if (!$hasNoDD && $entry['Processed'] && !$entry['Charged']) { $countChargeable++; } ?>
					<li class="list-group-item <?php if ($hasNoDD || $notReady) { ?>list-group-item-danger<?php } ?> <?php if ($entry['Charged']) { ?>list-group-item-success<?php } ?>">
						<div class="row">
							<div class="col-sm-5 col-md-4 col-lg-6">
								<h3><?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?></h3>

								<p class="mb-0">
									<?=htmlspecialchars($entry['MForename'])?> is entered in;
								</p>
								<ul class="list-unstyled">
								<?php $count = 0; ?>
								<?php foreach($swimsArray as $colTitle => $text) { ?>
									<?php if ($entry[$colTitle]) { $count++; ?>
									<li class="row">
										<div class="col">
											<?=$text?>
										</div>
										<?php if ($galaData->getEvent($colTitle)->isEnabled()) { ?>
										<div class="col">
											&pound;<?=$galaData->getEvent($colTitle)->getPriceAsString()?>
										</div>
										<?php } ?>
									</li>
									<?php } ?>
								<?php } ?>
							</div>
							<div class="col">
								<div class="d-sm-none mb-3"></div>
								<p>
									<?=mb_convert_case($numFormatter->format($count),   MB_CASE_TITLE_SIMPLE)?> event<?php if ($count != 1) { ?>s<?php } ?>
								</p>

								<?php if ($hasNoDD) { ?>
								<p>
									The parent does not have a Direct Debit set up or has requested to pay by other means.
								</p>
								<?php } ?>
								<?php if ($entry['Charged']) { ?>
								<p>
									This entry has already been marked as being paid. This may mean it has been paid by Direct Debit, Cash, Card or Cheque.
								</p>
								<?php } ?>
								<?php if ($notReady) { ?>
								<p>
									This entry has not yet been marked as processed and so cannot yet be charged for.
								</p>
								<?php } ?>

								<?php if (isset($_SESSION['OverhighChargeAmount'][$entry['EntryID']]) && $_SESSION['OverhighChargeAmount'][$entry['EntryID']]) {
									unset($_SESSION['OverhighChargeAmount'][$entry['EntryID']]); ?>
								<div class="alert alert-danger">
									<strong>Refund failed!</strong> You have attempted to refund more than the user has paid.
								</div>
								<?php } ?>

								<div class="form-group mb-0">
									<label for="<?=$entry['EntryID']?>-amount">
										Amount to charge
									</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text mono">&pound;</div>
										</div>
										<input type="number" pattern="[0-9]*([\.,][0-9]*)?" class="form-control mono" id="<?=$entry['EntryID']?>-amount" name="<?=$entry['EntryID']?>-amount" placeholder="0.00" value="<?=htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $entry['FeeToPay'])->toScale(2)))?>" <?php if ($hasNoDD || $entry['Charged'] || $notReady) { ?> disabled <?php } ?> min="0" max="150" step="0.01">
									</div>
								</div>
							</div>
						</div>
					</li>
					<?php } while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)); ?>
				</ul>

				<?=SCDS\CSRF::write()?>
				<?=SCDS\FormIdempotency::write()?>

				<?php if ($countChargeable > 0) { ?>
				<div class="cell bg-warning">
					<h2>Are you sure you're ready?</h2>
					<p class="lead">
						You won't be able to modify charges once you press submit.
					</p>

					<p>
						If you spot a mistake, you will have to handle it as a <strong>Manual Refund</strong> or as part of the <strong>Rejections Refund Process</strong>.
					</p>

					<p>
						<button class="btn btn-danger" type="submit">Charge parents</button>
					</p>
				</div>
				<?php } else { ?>
				<div class="alert alert-warning">
					<p><strong>There are no entries that can be charged for at this time</strong></p>
					<p class="mb-0">
						To charge for gala entries there must be at least one meeting the following criteria.
					</p>
					<ul class="mb-0">
						<li>A direct debit mandate</li>
						<li>Their parent must not have opted out for gala payments</li>
						<li>The entry must be marked as processed</li>
						<li>The entry must not already be marked as paid</li>
					</ul>
				</div>
				<?php } ?>
			</form>
			<?php } else { ?>
			<div class="alert alert-warning">
				<strong>There are no entries for this gala</strong>
			</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php if (isset($_SESSION['OverhighChargeAmount'])) {
	unset($_SESSION['OverhighChargeAmount']);
} ?>

<?php

include BASE_PATH . 'views/footer.php';