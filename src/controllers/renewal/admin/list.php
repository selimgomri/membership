<?php

$db = app()->db;
$tenant = app()->tenant;

$sql = $db->prepare("SELECT * FROM `renewals` WHERE `ID` = ? AND Tenant = ?;");
$sql->execute([
	$id,
	$tenant->getId()
]);
$renewalArray = $sql->fetch(PDO::FETCH_ASSOC);

if ($renewalArray == null) {
	halt(404);
}

$getNumRenewals = $db->prepare("SELECT COUNT(*) FROM `renewalMembers` WHERE `RenewalID` = ? AND Renewed = ?;");
$getNumRenewals->execute([$id, true]);
$numRenewals = $getNumRenewals->fetchColumn();

$getNumRenewals->execute([$id, 0]);
$numMembers = $numRenewals + $getNumRenewals->fetchColumn();

$numRenewalsByCat = $db->prepare("SELECT COUNT(*) FROM (`renewalMembers` LEFT JOIN `members` ON `members`.`MemberID` = `renewalMembers`.`MemberID`) WHERE `RenewalID` = ? AND members.`ASACategory` = ? AND Renewed = ?;");
$numRenewalsByCat->execute([$id, 1, true]);
$numC1Renewals = $numRenewalsByCat->fetchColumn();

$numRenewalsByCat->execute([$id, 2, true]);
$numC2Renewals = $numRenewalsByCat->fetchColumn();

$numRenewalsByCat->execute([$id, 3, true]);
$numC3Renewals = $numRenewalsByCat->fetchColumn();

$sql = $db->prepare("SELECT `MForename`, `MSurname`, `Forename`, `Surname`, members.ASANumber, `payments`.`Status`, `RenewalID`, `Renewed` FROM ((((`renewalMembers` RIGHT JOIN `members`
ON members.MemberID = renewalMembers.MemberID) LEFT JOIN `users` ON
members.UserID = users.UserID) LEFT JOIN `paymentsPending` ON
renewalMembers.PaymentID = paymentsPending.PaymentID) LEFT JOIN `payments` ON
payments.PMkey = paymentsPending.PMkey) WHERE `renewalMembers`.`RenewalID` =
? ORDER BY renewalMembers.Date DESC, `MSurname` ASC, `MForename` ASC;");
$sql->execute([$id]);

$fluidContainer = true;

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

?>

<div class="container-fluid">
	<div class="">
		<h1><?=htmlspecialchars($renewalArray['Name'])?> Status</h1>
		<p class="lead">
			This is the current status for this membership renewal which started on <?php
			echo date("l j F Y", strtotime($renewalArray['StartDate'])); ?> and
			finishes on <?php echo date("l j F Y", strtotime($renewalArray['EndDate'])); ?>
		</p>
		<p class="mb-0">
			<?=$numRenewals?> Renewals (<?=$numC1Renewals?> Category 1, <?=$numC2Renewals?> Category 2, <?=$numC3Renewals?> Category 3)
			of <?=$numMembers?> members*.
		</p>
		<p class="small text-muted">
			* Number of members on first day of renewal
		</p>
		<p class="">
			<a href="<?=autoUrl("renewal/" . $id . "/edit")?>" class="btn
			btn-dark">
				Edit this Renewal Period
			</a>
		</p>
	</div>

	<?php
	$renewalItem = $sql->fetch(PDO::FETCH_ASSOC);
	if ($renewalItem == null) {
		// No renewals
		?>
		<div class="alert alert-warning">
			<p class="mb-0">
				<strong>
					There are no renewals to display at this time.
				</strong>
			</p>
			<p class="mb-0">
				Please try again later.
			</p>
		</div>
		<?php
	} else {  ?>
	<div class="table-responsive-sm">
		<table class="table <?php if (app('request')->isMobile()) { ?>table-sm small<?php } ?>">
			<thead class="thead-light">
				<tr>
					<th>
						Member
					</th>
					<th>
						Parent
					</th>
					<th>
						ASA
					</th>
					<th>
						Payment Status
					</th>
				</tr>
			</thead>
			<tbody>
				<?php do {
					if ($renewalItem['Status'] == "failed" || $renewalItem['Status'] == "charged_back") {
						?><tr class="table-danger"><?php
					} else if ($renewalItem['Status'] == "paid_out" || $renewalItem['Status'] == "confirmed" || $renewalItem['Status'] == "paid_manually") {
						?><tr class="table-success"><?php
					} else {
						?><tr><?php
					}
					?>
						<td>
							<?=htmlspecialchars($renewalItem['MForename'] . " " . $renewalItem['MSurname'])?>
						</td>
						<td>
							<?=htmlspecialchars($renewalItem['Forename'] . " " . $renewalItem['Surname'])?>
						</td>
						<td>
							<span class="mono">
								<?=htmlspecialchars($renewalItem['ASANumber'])?>
							</span>
						</td>
						<td>
							<?php if (!bool($renewalItem['Renewed'])) {
								?>Not yet renewed<?php
							} else if ($renewalItem['Status'] == "") {
								?>Payment not yet processed<?php
							} else {
								echo paymentStatusString($renewalItem['Status']);
							} ?>
						</td>
					</tr>
					<?php
				} while ($renewalItem = $sql->fetch(PDO::FETCH_ASSOC)); ?>
			</tbody>
		</table>
	</div>
	<?php } ?>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();