<?php

global $db;

$disabled = "";

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
	halt(404);
}

$getEntries = $db->prepare("SELECT members.UserID `user`, 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, MForename, MSurname, Forename, Surname, EntryID, Charged, FeeToPay, MandateID, EntryProcessed Processed, Refunded, galaEntries.AmountRefunded, Intent, stripePayMethods.Brand, stripePayMethods.Last4, stripePayMethods.Funding, stripePayments.Paid StripePaid FROM ((((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN stripePayments ON galaEntries.StripePayment = stripePayments.ID) LEFT JOIN stripePayMethods ON stripePayMethods.ID = stripePayments.Method) WHERE galaEntries.GalaID = ? AND Charged = ? ORDER BY MForename ASC, MSurname ASC");
$getEntries->execute([$id, '1']);
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

$pagetitle = "Refund Parents for " . htmlspecialchars($gala['name']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments/galas")?>">Galas</a></li>
			<li class="breadcrumb-item active" aria-current="page">Refund gala entries</li>
		</ol>
	</nav>

	<h1>Refund Parents for <?=htmlspecialchars($gala['name'])?></h1>
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
					<strong>We've successfully refunded the parents</strong>
				</div>
			<?php } ?>

			<?php if (isset($_SESSION['ChargeUsersFailure'])) { 
				unset($_SESSION['ChargeUsersFailure']); ?>
				<div class="alert alert-warning">
					<strong>An error occurred.</strong> Please try again later.
				</div>
			<?php } ?>

			<h2>How to refund parents</h2>
			<p>
				The amount paid by each parent for their gala entry is shown on next to each swimmer. In the box provided, enter the amount to be refunded for rejections, if there are any.
			</p>

			<p>
			  For increased security, you must press the <strong>Refund</strong> button next next to each entry. The system will ask for confirmation and report back to you whether the refund was successful.
			</p>

			<?php if (!$gala['fixed']) { ?>
			<p>
				As there is no fixed fee for each swim at this gala, you will have to check the amount to refund very carefully as rejections reports by software such as SPORTSYSTEMS do not support events with different fees (eg 1500m events) and so may not show the right amount to refund.
			</p>
			<?php } ?>

			<p>
				This software will prevent you from refunding more than the cost of a gala entry.
			</p>

			<h2>Entries for this gala</h2>	

			<?php if ($entry != null) { ?>
			<ul class="list-group mb-3" id="entries-list">
				<?php do { ?>
					<?php $hasNoDD = ($entry['MandateID'] == null) || (getUserOption($entry['user'], 'GalaDirectDebitOptOut')); ?>
					<?php $amountRefundable = ((int) ($entry['FeeToPay']*100)) - ($entry['AmountRefunded']); ?>
				<?php if ($entry['Processed'] && $entry['Charged']) { $countChargeable++; } ?>
				<?php $notReady = !$entry['Processed']; ?>
				<li class="list-group-item <?php if ($notReady) { ?>list-group-item-danger<?php } ?>" id="refund-box-<?=htmlspecialchars($entry['EntryID'])?>">
				  <form id="refund-form-<?=htmlspecialchars($entry['EntryID'])?>" novalidate autocomplete="off">
						<div class="row">
							<div class="col-sm-5 col-md-4 col-lg-6">
								<h3><?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?></h3>

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
								<?php if ($entry['Intent'] != null && bool($entry['StripePaid'])) { ?>
								<p>
									<strong><i class="fa <?=htmlspecialchars(getCardFA($entry['Brand']))?>" aria-hidden="true"></i> <span class="sr-only"><?=htmlspecialchars(getCardBrand($entry['Brand']))?></span> &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($entry['Last4'])?></strong>
								</p>
								<?php } ?>

								<p>
									<?php if ($gala['fixed']) { ?>
									<?=$count?> &times; &pound;<?=htmlspecialchars(number_format($gala['fee'], 2))?>
									<?php } else { ?>
									<?=$count?> entries at no fixed fee
									<?php } ?>
								</p>

								<div id="<?=$entry['EntryID']?>-amount-refunded">
								<?php if ($entry['Refunded']) { ?>
								<p>
									<strong>&pound;<?=number_format($entry['AmountRefunded']/100, 2)?></strong> has already been refunded!
								</p>
								<?php } ?>
								</div>

								<?php if ($hasNoDD && $entry['Intent'] == null) { ?>
								<p>
									The parent does not have a Direct Debit set up or has requested to pay by other means. Refund should be by cash, cheque or bank transfer.
								</p>
								<?php } else if ($entry['Intent'] != null && $amountRefundable > 0 && bool($entry['StripePaid'])) { ?>
								<p>
									This entry will be refunded to <?=htmlspecialchars(getCardBrand($entry['Brand']))?> <span class="mono"><?=htmlspecialchars($entry['Last4'])?></span>.
								</p>
								<?php } else if ($amountRefundable > 0) { ?>
								<p>
									This gala will be refunded as a discount on the parent's next direct debit payment.
								</p>
								<?php } ?>

								<?php

								$refundSource = '';
								if ($hasNoDD && $entry['Intent'] == null) {
									$refundSource = 'manual refund. No direct debit or payment card is available to issue an automatic refund';
								} else if ($entry['Intent'] != null && $amountRefundable > 0 && bool($entry['StripePaid'])) {
									$refundSource = $entry['Forename'] . ' ' . $entry['Surname'] . '\'s ' . getCardBrand($entry['Brand']) . ' ' . $entry['Funding'] . ' card ending ' . $entry['Last4'];
								} else if ($amountRefundable > 0) {
									$refundSource = 'This gala will be credited to ' . $entry['Forename'] . ' ' . $entry['Surname'] . '\'s account and discounted from their next direct debit payment';
								}

								?>

								<?php if (isset($_SESSION['OverhighChargeAmount'][$entry['EntryID']]) && $_SESSION['OverhighChargeAmount'][$entry['EntryID']]) {
									unset($_SESSION['OverhighChargeAmount'][$entry['EntryID']]); ?>
								<div class="alert alert-danger">
									<strong>Refund failed!</strong> You have attempted to refund more than the user has paid.
								</div>
								<?php } ?>

								<div class="form-row">
									<div class="col-xs col-sm-12 col-xl-6">
										<div class="form-group mb-0">
											<label for="<?=$entry['EntryID']?>-amount">
												Amount charged
											</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text mono">&pound;</div>
												</div>
												<input type="number" class="form-control mono" id="<?=$entry['EntryID']?>-amount" name="<?=$entry['EntryID']?>-amount" placeholder="0.00" value="<?=htmlspecialchars(number_format($entry['FeeToPay'], 2))?>" disabled>
											</div>
										</div>
										<div class="d-none d-sm-block d-xl-none mb-3"></div>
									</div>

									<div class="col-xs col-sm-12 col-xl-6">
										<div class="form-group mb-0">
											<label for="<?=$entry['EntryID']?>-refund">
												Amount to refund
											</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text mono">&pound;</div>
												</div>
												<input type="number" pattern="[0-9]*([\.,][0-9]*)?" class="form-control mono refund-amount-field" id="<?=$entry['EntryID']?>-refund" name="<?=$entry['EntryID']?>-refund" placeholder="0.00" min="0" max="<?=htmlspecialchars(number_format($amountRefundable/100, 2))?>" data-max-refundable="<?=$amountRefundable?>" data-amount-refunded="<?=$entry['AmountRefunded']?>" step="0.01" <?php if ($amountRefundable == 0 || $notReady) { ?>disabled<?php } ?>>
											</div>
										</div>
									</div>

									<?php if (!($amountRefundable == 0 || $notReady)) { ?>
									<div class="col-12 mt-3">
									  <span id="<?=$entry['EntryID']?>-refund-error-warning-box"></span>
										<p class="mb-0">
											<button type="button" id="<?=$entry['EntryID']?>-refund-button" class="refund-button btn btn-primary" data-entry-id="<?=$entry['EntryID']?>" data-refund-location="<?=htmlspecialchars($refundSource)?>" data-swimmer-name="<?=htmlspecialchars($entry['MForename'] . ' ' . $entry['MSurname'])?>">
												Refund
											</button>
										</p>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</form>
				</li>
				<?php } while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)); ?>
			</ul>

			<?php if ($countChargeable > 0) { ?>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="myModalBody">
        ...
      </div>
      <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="modalConfirmButton">Confirm refund</button>
      </div>
    </div>
  </div>
</div>

<script src="<?=autoUrl("js/galas/refund-entries.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';