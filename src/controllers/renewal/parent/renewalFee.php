<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

$pagetitle = "Your Memberships";

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

<div class="container-xl">
	<form method="post" id="form">
		<div class="row">
			<div class="col-lg-8">
				<h1>
					Memberships
				</h1>
				<p class="lead">
					There's just one more step to go. We now need you to pay any membership fees which are due.
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
					To do this, we will now redirect you to the Membership Centre where you can see any pending fees and make a payment.
				</p>

				<p>
					<button type="submit" class="btn btn-default">Continue</button>
				</p>

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
