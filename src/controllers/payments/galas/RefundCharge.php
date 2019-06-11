<?php

global $db;

$disabled = "";

$getGala = $db->prepare("SELECT GalaName `name`, GalaFee fee, GalaVenue venue, GalaFeeConstant fixed FROM galas WHERE GalaID = ?");
$getGala->execute([$id]);
$gala = $getGala->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
	halt(404);
}

$getEntries = $db->prepare("SELECT 50Free, 100Free, 200Free, 400Free, 800Free, 1500Free, 50Back, 100Back, 200Back, 50Breast, 100Breast, 200Breast, 50Fly, 100Fly, 200Fly, 100IM, 150IM, 200IM, 400IM, MForename, MSurname, EntryID, Charged, FeeToPay, MandateID, userOptions.Value OptOut, EntryProcessed Processed, Refunded, AmountRefunded FROM (((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN userOptions ON users.UserID = userOptions.User) WHERE galaEntries.GalaID = ? AND (userOptions.Option = 'GalaDirectDebitOptOut' OR userOptions.Option IS NULL	) AND Charged = ? AND EntryProcessed = ? ORDER BY MForename ASC, MSurname ASC");
$getEntries->execute([$id, '1', '1']);
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

			<?php if (!$gala['fixed']) { ?>
			<p>
				As there is no fixed fee for each swim at this gala, you will have to check the amount to refund very carefully as rejections reports by software such as SPORTSYSTEMS do not support events with different fees (eg 1500m events) and so may not show the right amount to refund.
			</p>
			<?php } ?>

			<p>
				<strong>Warning:</strong> The software does not yet show that entries have been refunded previously!
			</p>

			<h2>Entries for this gala</h2>	

			<form method="post" onsubmit="return confirm('Are you sure you want to refund parents? You won\'t be able to modify refunds once you proceed.');">
				<?php if ($entry != null) { ?>
				<ul class="list-group mb-3">
					<?php do { ?>
						<?php $hasNoDD = ($entry['MandateID'] == null) || ($entry['OptOut']); ?>
					<?php if ($entry['Processed'] && $entry['Charged']) { $countChargeable++; } ?>
					<li class="list-group-item">
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
								<p>
									<?php if ($gala['fixed']) { ?>
									<?=$count?> &times; &pound;<?=htmlspecialchars(number_format($gala['fee'], 2))?>
									<?php } else { ?>
									<?=$count?> entries at no fixed fee
									<?php } ?>
								</p>

								<?php if ($entry['Refunded']) { ?>
								<p>
									<strong>&pound;<?=number_format($entry['AmountRefunded']*100, 2)?></strong> has already been refunded!
								</p>
								<?php } ?>

								<?php if ($hasNoDD) { ?>
								<p>
									The parent does not have a Direct Debit set up or has requested to pay by other means. Refund should be by cash, cheque or bank transfer.
								</p>
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
												<input type="text" class="form-control mono" id="<?=$entry['EntryID']?>-amount" name="<?=$entry['EntryID']?>-amount" placeholder="0.00" value="<?=htmlspecialchars(number_format($entry['FeeToPay'], 2))?>" disabled>
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
												<input type="text" class="form-control mono" id="<?=$entry['EntryID']?>-refund" name="<?=$entry['EntryID']?>-refund" placeholder="0.00">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
					<?php } while ($entry = $getEntries->fetch(PDO::FETCH_ASSOC)); ?>
				</ul>

				<?php if ($countChargeable > 0) { ?>
				<div class="cell bg-warning">
					<h2>Are you sure you're ready?</h2>
					<p class="lead">
						You won't be able to modify refunds once you press submit.
					</p>

					<p>
						If you spot a mistake, you will have to handle it as a <strong>Manual Charge</strong> or <strong>Manual Event</strong>.
					</p>

					<p>
						<button class="btn btn-danger" type="submit">Refund parents</button>
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

<?php

include BASE_PATH . 'views/footer.php';