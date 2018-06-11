<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

//$customers = $client->customers()->list()->records;
//print_r($customers);

$sql = "SELECT * FROM `paymentMandates` WHERE `UserID` = $user AND `InUse` = 1;";
$result = mysqli_query($link, $sql);

 ?>

<div class="container">
	<h1>Your Direct Debits</h1>
	<p class="lead">Control your Direct Debit details</p>
	<h2>My Direct Debits</h2>
	<?php if (mysqli_num_rows($result) > 0) { ?>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Bank Name</th>
					<th>Account Holder</th>
					<th>Account Number</th>
					<th>In Use</th>
          <th></th>
				</tr>
			</thead>
			<tbody>
				<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);	?>
				<tr>
					<td><? echo $row['BankName']; ?></td>
					<td><? echo $row['AccountHolderName']; ?></td>
          <td>******<? echo $row['AccountNumEnd']; ?></td>
					<td><? echo $row['InUse']; ?></td>
					<?php if (mysqli_num_rows($result) > 1) { ?>
					<td><a href="<? echo autoUrl("payments/banks/makedefault/" . $row['MandateID']); ?>">Make Default</a></td>
					<?php } else { ?>
					<td><small>Default Direct Debit</small></td>
					<?php } ?>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } else { ?>
	<div class="alert alert-warning">
		<strong>You have no Direct Debits</strong> <br>
		Create one now
	</div>
	<?php } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
