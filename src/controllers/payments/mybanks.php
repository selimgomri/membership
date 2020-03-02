<?php

require 'GoCardlessSetup.php';

global $db;

$user = $_SESSION['UserID'];
$use_white_background = true;
$pagetitle = "Payments and Direct Debits";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

//$customers = $client->customers()->list()->records;
//print_r($customers);

/*
 * Get the user's preferred mandate (If exists)
 */
$getPreferred = $db->prepare("SELECT MandateID FROM `paymentPreferredMandate` WHERE `UserID` = ?");
$getPreferred->execute([$_SESSION['UserID']]);
$defaultAcc = null;
if ($row = $getPreferred->fetch()) {
  $defaultAcc = $row['MandateID'];
}

/*
 * Get all mandates
 */
$mandateDetails = $db->prepare("SELECT * FROM `paymentMandates` WHERE `UserID` = ? AND `InUse` = ?");
$mandateDetails->execute([$_SESSION['UserID'], true]);

?>

<div class="container">
	<h1>Bank Account Options</h1>
	<p class="lead">Control your Direct Debit details</p>

  <div class="cell">
  	<h2>My Direct Debit Mandates</h2>
    <p class="lead">
      View details about your current mandates, and switch your primary mandate (the bank we take money from)
    </p>
    <p>
      To delete a mandate permanently, contact your bank in person, by phone or through online banking, where you can cancel a mandate yourself.
    </p>
  	<?php if ($row = $mandateDetails->fetch(PDO::FETCH_ASSOC)) { ?>
  	<div class="table-responsive-md bg-white">
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
  				<?php do { ?>
  				<tr>
            <td class="mono">
              <a target="_blank" href="<?=autoUrl("payments/mandates/" . htmlspecialchars($row['Mandate']))?>" title="View Mandate Details">
                <?=htmlspecialchars($row['Mandate'])?>
              </a>
            </td>
  					<td>
              <?=htmlspecialchars(getBankName($row['BankName']))?>
            </td>
  					<td class="mono" title="Name on bank account">
              <?=htmlspecialchars($row['AccountHolderName'])?>
            </td>
            <td class="mono" title="Last two digits of your account number">
              ******<?=htmlspecialchars($row['AccountNumEnd'])?>
            </td>
  					<?php if ($defaultAcc != null > 1 && $defaultAcc != $row['MandateID']) { ?>
  					<td>
              <a href="<?=autoUrl("payments/mandates/makedefault/" . htmlspecialchars($row['MandateID']))?>">
                Make Default
              </a>
            </td>
  					<?php } else { ?>
  					<td>
              <small>Default Mandate</small>
            </td>
  					<?php } ?>
  				</tr>
  			<?php } while ($row = $mandateDetails->fetch(PDO::FETCH_ASSOC)); ?>
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
		<strong>You have no Direct Debit Mandates</strong> <br>
		<a class="alert-link" href="<?=autoUrl("payments/setup")?>">Create one now</a>
	</div>
	<?php } ?>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
