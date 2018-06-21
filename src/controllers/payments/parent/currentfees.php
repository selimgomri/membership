<?php

$user = $_SESSION['UserID'];
$pagetitle = "Current Fees";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded box-shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Current Fees</h1>
		<p class="lead">Fees and Charges created in the current Billing Period</p>
		<p>You'll be billed for these on your next billing date.</p>
		<? echo feesToPay($link, $user); ?>
	</div>
</div>

<?php

include BASE_PATH . "views/footer.php";
