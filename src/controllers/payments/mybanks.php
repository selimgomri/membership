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
	<h1>Bank Account Options</h1>
	<p class="lead">Control your Direct Debit details</p>

  <div class="my-3 p-3 bg-white rounded shadow">
  	<h2>My Direct Debit Mandates</h2>
    <p class="lead">
      View details about your current mandates, and switch your primary mandate (the bank we take money from)
    </p>
    <p>
      To delete a mandate permanently, contact your bank in person, by phone or through online banking, where you can cancel a mandate yourself.
    </p>
  	<?php if (mysqli_num_rows($result) > 0) { ?>
  	<div class="table-responsive-md">
  		<table class="table table-striped">
  			<thead>
  				<tr>
            <th>Mandate ID</th>
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
            <td class="mono"><a target="_blank" href="<?=autoUrl("payments/mandates/" . $row['Mandate'])?>" title="View Mandate Details"><?=$row['Mandate']?></a></td>
  					<td><?=$row['BankName']?></td>
  					<td class="mono" title="Name on bank account"><?=$row['AccountHolderName']?></td>
            <td class="mono" title="Last two digits of your account number">******<?=$row['AccountNumEnd']?></td>
  					<?php if (mysqli_num_rows($result) > 1 && $defaultAcc != $row['MandateID']) { ?>
  					<td><a href="<? echo autoUrl("payments/mandates/makedefault/" . $row['MandateID']); ?>">Make Default</a></td>
  					<?php } else { ?>
  					<td><small>Default Mandate</small></td>
  					<?php } ?>
  				</tr>
  			<?php } ?>
  			</tbody>
  		</table>
  	</div>
    <p class="mb-0">
      <a href="<?=autoUrl("payments/setup")?>" class="btn btn-dark">
        Setup New Direct Debit
      </a>
    </p>
  </div>
	<?php } else { ?>
	<div class="alert alert-warning">
		<strong>You have no Direct Debits</strong> <br>
		<a class="alert-link" href="<? echo autoUrl("payments/setup"); ?>">Create one now</a>
	</div>
	<?php } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
