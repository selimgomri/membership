<?php

$user = mysqli_real_escape_string($link, $id);

$name = getUserName($user);
$pagetitle = "Current Fees for " . $name;

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php'; ?>

<div class="container">
	<div class="my-3 p-3 bg-white rounded shadow">
		<h1 class="border-bottom border-gray pb-2 mb-2">Current Fees for <?php echo $name; ?></h1>
		<p class="lead">Fees and Charges created in the current Billing Period for <?php echo $name; ?>.</p>
		<p>These fees will be billed on <?php echo $name; ?>'s billing date.</p>
		<?php echo feesToPay($link, $user); ?>
	</div>
</div>

<?php

include BASE_PATH . "views/footer.php";
