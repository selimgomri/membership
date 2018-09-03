<?php

$user = $_SESSION['UserID'];
$pagetitle = "Transaction History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Transaction History</h1>
		<p class="lead">Previous Payments and Refunds</p>
		<? echo paymentHistory($link, $user); ?>
	</div>
</div>

<?php

include BASE_PATH . "views/footer.php";
