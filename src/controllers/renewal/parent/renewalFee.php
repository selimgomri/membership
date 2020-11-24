<?php

$db = app()->db;


$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$partial_reg = isPartialRegistration();

$partial_reg_require_topup = false;
if ($partial_reg) {
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = 0 AND ClubPays = 0";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
	if ($query->fetchColumn() == 1) {
		$partial_reg_require_topup = true;
	}
}

$month = (new DateTime('now', new DateTimeZone('Europe/London')))->format('m');

$discounts = json_decode(app()->tenant->getKey('MembershipDiscounts'), true);
$clubDiscount = $swimEnglandDiscount = 0;
if ($discounts != null && isset($discounts['CLUB'][$month])) {
	$clubDiscount = $discounts['CLUB'][$month];
}
if ($discounts != null && isset($discounts['ASA'][$month])) {
	$swimEnglandDiscount = $discounts['ASA'][$month];
}

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0'");
$sql->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$clubFee = $totalFeeDiscounted = $totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

$clubFees = \SCDS\Membership\ClubMembership::create($db, $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], $partial_reg);

$clubFee = $clubFees->getFee();

if ($partial_reg) {
	$sql = "SELECT * FROM members WHERE `members`.`UserID` = ? AND `members`.`RR` = 1";
} else {
	$sql = "SELECT * FROM members WHERE `members`.`UserID` = ?";
}
$getMembers = $db->prepare($sql);
$getMembers->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

if ($clubDiscount > 0 && $renewal == 0) {
	$totalFee += $clubFee;
	$totalFeeDiscounted += $clubFee * (1 - ($clubDiscount / 100));
} else {
	$totalFee += $clubFee;
	$totalFeeDiscounted += $clubFee;
}

$asaFees = [];

$asa1 = app()->tenant->getKey('ASA-County-Fee-L1') + app()->tenant->getKey('ASA-Regional-Fee-L1') + app()->tenant->getKey('ASA-National-Fee-L1');
$asa2 = app()->tenant->getKey('ASA-County-Fee-L2') + app()->tenant->getKey('ASA-Regional-Fee-L2') + app()->tenant->getKey('ASA-National-Fee-L2');
$asa3 = app()->tenant->getKey('ASA-County-Fee-L3') + app()->tenant->getKey('ASA-Regional-Fee-L3') + app()->tenant->getKey('ASA-National-Fee-L3');

for ($i = 0; $i < $count; $i++) {
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa1;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa2;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa3;
	} else {
		$asaFees[$i] = 0;
	}
	if ($member[$i]['RRTransfer']) {
		$totalFee += $asaFees[$i];
		// $totalFeeDiscounted += 0;
	} else if ($swimEnglandDiscount > 0 && $renewal == 0) {
		$totalFee += $asaFees[$i];
		$totalFeeDiscounted += $asaFees[$i] * (1 - ($swimEnglandDiscount / 100));
	} else {
		$totalFee += $asaFees[$i];
		$totalFeeDiscounted += $asaFees[$i];
	}
}

$clubFeeString = (string) (\Brick\Math\BigDecimal::of((string) $clubFee))->withPointMovedLeft(2)->toScale(2);
$totalFeeString = (string) (\Brick\Math\BigDecimal::of((string) $totalFee))->withPointMovedLeft(2)->toScale(2);

$pagetitle = "Your Renewal Fees";
$title = "Your Membership Renewal Fees";
if ($renewal == 0) {
	$pagetitle = "Your Registration Fees";
	$title = "Your Registration Fees";
}

$hasStripeMandate = false;
$hasGCMandate = false;
if (stripeDirectDebit(true)) {
	// Work out if has mandates
	$getCountNewMandates = $db->prepare("SELECT COUNT(*) FROM stripeMandates INNER JOIN stripeCustomers ON stripeMandates.Customer = stripeCustomers.CustomerID WHERE stripeCustomers.User = ? AND stripeMandates.MandateStatus != 'inactive';");
	$getCountNewMandates->execute([
		$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
	]);
	$hasStripeMandate = $getCountNewMandates->fetchColumn() > 0;
} else if (app()->tenant->getGoCardlessAccessToken()) {
	$hasGCMandate = userHasMandates($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);
}

include BASE_PATH . 'views/header.php';
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<form method="post" id="form">
		<h1>
			<?= $title ?>
		</h1>
		<p class="lead">
			There's just one more step to go. We now need you to confirm your membership <?php if ($renewal == 0) { ?>registration<?php } else { ?>renewal<?php } ?>.
		</p>

		<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) { ?>
			<div class="alert alert-success">
				<p class="mb-0">
					<strong>We've set up your new direct debit</strong>
				</p>
				<p class="mb-0">
					It will take a few days for the mandate to be confirmed at your bank.
				</p>
			</div>
		<?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']);
		} ?>

		<p>
			These fees include your Swim England membership fees for the sport's governing bodies at National, Regional and County Level.
		</p>
		<?php $nf = "next";
		if ($renewal == 0) {
			$nf = "first";
		}; ?>
		<?php if (app()->tenant->getGoCardlessAccessToken()) { ?>
			<p>
				You will pay these fees as part of your <?= $nf ?> Direct Debit payment to <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>.
			</p>
		<?php } ?>

		<h2>Your Membership Fees</h2>
		<p class="lead">
			Check that these are as you expected before you continue.
		</p>
		<div class="table-responsive-md">
			<table class="table">
				<thead class="">
					<tr class="bg-primary text-light">
						<th>
							Club Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<thead class="thead-light">
					<tr>
						<th>
							Type
						</th>
						<th>
							Fee
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($clubFees->getFeeItems() as $item) { ?>
						<tr>
							<td>
								<?= htmlspecialchars($item['description']) ?>
							</td>
							<td>
								&pound;<?= number_format($item['amount'] / 100, 2, '.', '') ?>
							</td>
						</tr>
					<?php } ?>
					<?php if ($clubDiscount > 0 && $renewal == 0) { ?>
						<tr>
							<td>
								Discretionary discount at <?= htmlspecialchars($clubDiscount) ?>%
							</td>
							<td>
								-&pound;<?= number_format(((int)$clubFee * ($clubDiscount / 100) / 100), 2, '.', '') ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
				<!--</table>
		</div>
		<?php if ($payingSwimmerCount > 1) {
		?>
			<p class="lead"<?= number_format($totalFee - $totalFeeDiscounted, 2, '.', '') ?>>
				You <?php if ($renewal == 0) { ?>will <?php } ?>pay for a family membership, covering all of your swimmers at a reduced cost.
			</p>
			<?php
		} ?>
 		<p>Your club membership fee is &pound;<?= $clubFeeString ?></p>

		<h2>Swim England Membership Fees</h2>
		<div class="table-responsive-md">
			<table class="table">-->
				<thead class="">
					<tr class="bg-primary text-light">
						<th>
							Swim England Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<thead class="thead-light">
					<tr>
						<th>
							Swimmer
						</th>
						<th>
							Fee
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i = 0; $i < $count; $i++) {
						$asaFeesString;
						if ($member[$i]['ClubPays']) {
							$asaFeesString = "0.00 (Paid by club)";
						} else if (isset($asaFees[$i])) {
							$asaFeesString = (string) (\Brick\Math\BigDecimal::of((string) $asaFees[$i]))->withPointMovedLeft(2)->toScale(2);
						} else {
							$asaFeesString = "0.00 (No fee information)";
						}
					?>
						<tr>
							<td>
								<?= htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname']) ?>
							</td>
							<td>
								&pound;<?php echo $asaFeesString; ?>
							</td>
						</tr>
						<?php if ($member[$i]['RRTransfer']) { ?>
							<tr>
								<td>
									<?= htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname']) ?> (Swim England Membership Transfer Credit)
								</td>
								<td>
									-&pound;<?= $asaFeesString ?>
								</td>
							</tr>
						<?php } else if ($swimEnglandDiscount > 0 && $renewal == 0) { ?>
							<tr>
								<td>
									Discretionary discount at <?= htmlspecialchars($swimEnglandDiscount) ?>%
								</td>
								<td>
									-&pound;<?= number_format(((int)$asaFees[$i] * ($swimEnglandDiscount / 100)) / 100, 2, '.', '') ?>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
				<tbody>
					<tr class="table-active">
						<td>
							Total Membership Fee
						</td>
						<td>
							&pound;<?= $totalFeeString ?>
						</td>
					</tr>
					<?php if (($swimEnglandDiscount > 0 || $clubDiscount > 0)	 && $renewal == 0) { ?>
						<tr class="table-active">
							<td>
								Total discounts
							</td>
							<td>
								-&pound;<?= number_format(((int)$totalFee - $totalFeeDiscounted) / 100, 2, '.', '') ?>
							</td>
						</tr>
						<tr class="table-active">
							<td>
								Total Membership Fee (with discounts)
							</td>
							<td>
								&pound;<?= number_format(((int)$totalFeeDiscounted) / 100, 2, '.', '') ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div class="row">
			<div class="col-lg-8">
				<?php if ($totalFeeDiscounted > 0) { ?>
					<h2>How would you like to pay?</h2>
					<p class="lead">
						We accept multiple payment methods
					</p>

					<div class="mb-3" id="payment-method-select">
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CARD') && $tenant->getStripeAccount()) { ?>
							<div class="custom-control custom-radio">
								<input type="radio" id="payment-method-card" name="payment-method" class="custom-control-input" value="card" checked>
								<label class="custom-control-label" for="payment-method-card">Credit/Debit Card</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_DD') && ($tenant->getStripeAccount() || $tenant->getGoCardlessAccessToken()) && $tenant->getKey('USE_DIRECT_DEBIT') && ($hasStripeMandate || $hasGCMandate)) { ?>
							<div class="custom-control custom-radio">
								<input type="radio" id="payment-method-dd" name="payment-method" class="custom-control-input" value="dd">
								<label class="custom-control-label" for="payment-method-dd">As part of my next Direct Debit payment</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_BACS')) { ?>
							<div class="custom-control custom-radio">
								<input type="radio" id="payment-method-bacs" name="payment-method" class="custom-control-input" value="bacs">
								<label class="custom-control-label" for="payment-method-bacs">Bank Transfer</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CASH')) { ?>
							<div class="custom-control custom-radio">
								<input type="radio" id="payment-method-cash" name="payment-method" class="custom-control-input" value="cash">
								<label class="custom-control-label" for="payment-method-cash">Cash</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CHEQUE')) { ?>
							<div class="custom-control custom-radio">
								<input type="radio" id="payment-method-cheque" name="payment-method" class="custom-control-input" value="cheque">
								<label class="custom-control-label" for="payment-method-cheque">Cheque</label>
							</div>
						<?php } ?>
					</div>

					<div id="descripton-box"></div>

					<p>
						<button type="submit" class="btn btn-primary" id="checkout-button">
							Checkout
						</button>
					</p>

				<?php } else { ?>
					<h2>You have nothing to pay</h2>
					<p class="lead">
						Welcome to <?= htmlspecialchars($tenant->getName()) ?>
					</p>
					<p>
						<button type="submit" class="btn btn-success">
							Finish registration/renewal
						</button>
					</p>
				<?php } ?>
			</div>
		</div>

	</form>
</div>

<script>
	
</script>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/registration-and-renewal/renewal-fee.js');
$footer->render();
