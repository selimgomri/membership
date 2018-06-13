<?php

$user = $_SESSION['UserID'];
$pagetitle = "Current Fees";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<h1>Current Fees</h1>
	<p class="lead">Fees and Charges created in the current Billing Period</p>
	<p>You'll be billed for these on your next billing date.</p>
	<? echo feesToPay($link, $user); ?>
</div>

<?php

include BASE_PATH . "views/footer.php";
