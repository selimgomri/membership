<?php

$id = mysqli_real_escape_string($link, $id);
$name = getUserName($id);

if (!$name) {
	halt(404);
}

$use_white_background = true;
$user = $_SESSION['UserID'];
$pagetitle = $name . "'s Transaction History";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<div class="">
		<h1 class="border-bottom border-gray pb-2 mb-2">
			Transaction History for <?=$name?>
		</h1>
		<p class="lead">Previous Payments and Refunds</p>
		<? echo paymentHistory($link, $id, "admin"); ?>
	</div>
</div>

<?php

include BASE_PATH . "views/footer.php";
