<?php

$user = $_SESSION['UserID'];
$pagetitle = "Payments and Direct Debits";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

//$customers = $client->customers()->list()->records;
//print_r($customers);

$defaultAcc = null;
$sql = "SELECT * FROM `paymentPreferredMandate` WHERE `UserID` = '$user';";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) == 1) {
  $defaultAcc = (mysqli_fetch_array($result, MYSQLI_ASSOC))['MandateID'];
}

$sql = "SELECT * FROM `paymentMandates` WHERE `UserID` = '$user' AND `InUse` = 1;";
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
          <th></th>
				</tr>
			</thead>
			<tbody>
				<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);	?>
				<tr>
					<td><a target="_blank" href="<? echo autoUrl("payments/mandates/" . $row['Mandate']); ?>"><? echo $row['BankName']; ?></a></td>
					<td><? echo $row['AccountHolderName']; ?></td>
          <td>******<? echo $row['AccountNumEnd']; ?></td>
					<?php if (mysqli_num_rows($result) > 1 && $defaultAcc != $row['MandateID']) { ?>
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
		<a class="alert-link" href="<? echo autoUrl("payments/setup"); ?>">Create one now</a>
	</div>
	<?php } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
