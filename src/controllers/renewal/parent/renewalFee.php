<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$partial_reg = isPartialRegistration();

$partial_reg_require_topup = false;
if ($partial_reg) {
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = 0";
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

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ?");
$sql->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$clubFee = $totalFeeDiscounted = $totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

$clubFees = MembershipFees\MembershipFees::getByUser($user->getId(), $partial_reg);

$clubFee = $clubFees->getTotal();

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

for ($i = 0; $i < $count; $i++) {
	if ($member[$i]['NGBCategory'] && !$member[$i]['ASAPaid']) {
		$asaFees[$i] = MembershipClassInfo::getFee($member[$i]['NGBCategory']);
	} else {
		$asaFees[$i] = 0;
	}

	$totalFee += $asaFees[$i];
	$totalFeeDiscounted += $asaFees[$i];
}

$clubFeeString = $clubFees->getFormattedTotal();
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

		<h2>Your Membership Fees</h2>
		<p class="lead">
			Your annual membership fees are as follows;
		</p>
		<div class="table-responsive-md">
			<table class="table table-light">
				<thead>
					<tr class="">
						<th>
							Club Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<?php foreach ($clubFees->getClubClasses() as $class) { ?>
					<thead class="">
						<tr class="">
							<th>
								<?= htmlspecialchars($class->getName()) ?>
							</th>
							<th>
							</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>
								Member
							</th>
							<th>
								Fee
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($class->getFeeItems() as $item) { ?>
							<tr>
								<td>
									<?= htmlspecialchars($item->getDescription()) ?>
								</td>
								<td>
									&pound;<?= htmlspecialchars($item->getFormattedAmount()) ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				<?php } ?>
				<thead class="">
					<tr class="">
						<th>
							<?= htmlspecialchars($tenant->getKey('NGB_NAME')) ?> Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<?php foreach ($clubFees->getNGBClasses() as $class) { ?>
					<thead class="">
						<tr class="">
							<th>
								<?= htmlspecialchars($class->getName()) ?>
							</th>
							<th>
							</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>
								Member
							</th>
							<th>
								Fee
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($class->getFeeItems() as $item) { ?>
							<tr>
								<td>
									<?= htmlspecialchars($item->getDescription()) ?>
								</td>
								<td>
									&pound;<?= htmlspecialchars($item->getFormattedAmount()) ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				<?php } ?>
				<tbody>
					<tr class="table-active">
						<td>
							Total Membership Fee
						</td>
						<td>
							&pound;<?= $totalFeeString ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<p>
			We will now direct you to the Membership Centre where you will pay for your mandatory memberships can optionally select other memberships offered by <?= app()->tenant->getName() ?>.
		</p>

		<div class="row">
			<div class="col-lg-8">
				<?php if ($totalFeeDiscounted > 0) { ?>
					<h2>How would you like to pay?</h2>
					<p class="lead">
						We accept multiple payment methods
					</p>

					<div class="mb-3" id="payment-method-select">
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CARD') && $tenant->getStripeAccount()) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-card" name="payment-method" class="form-check-input" value="card" checked>
								<label class="form-check-label" for="payment-method-card">Credit/Debit Card</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_DD') && ($tenant->getStripeAccount() || $tenant->getGoCardlessAccessToken()) && $tenant->getKey('USE_DIRECT_DEBIT') && ($hasStripeMandate || $hasGCMandate)) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-dd" name="payment-method" class="form-check-input" value="dd">
								<label class="form-check-label" for="payment-method-dd">As part of my next Direct Debit payment</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getKey('USE_DIRECT_DEBIT') && !$hasStripeMandate && $tenant->getBooleanKey('ALLOW_DIRECT_DEBIT_OPT_OUT')) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-manual" name="payment-method" class="form-check-input" value="manual">
								<label class="form-check-label" for="payment-method-manual">Manually</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_BACS')) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-bacs" name="payment-method" class="form-check-input" value="bacs">
								<label class="form-check-label" for="payment-method-bacs">Bank Transfer</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CASH')) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-cash" name="payment-method" class="form-check-input" value="cash">
								<label class="form-check-label" for="payment-method-cash">Cash</label>
							</div>
						<?php } ?>
						<?php if ($tenant->getBooleanKey('MEMBERSHIP_FEE_PM_CHEQUE')) { ?>
							<div class="form-check">
								<input type="radio" id="payment-method-cheque" name="payment-method" class="form-check-input" value="cheque">
								<label class="form-check-label" for="payment-method-cheque">Cheque</label>
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
