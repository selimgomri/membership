<?php

global $db;
global $systemInfo;

$user = $_SESSION['UserID'];
$partial_reg = false;//isPartialRegistration();

$partial_reg_require_topup = false;
if ($partial_reg) {
	global $db;
	$sql = "SELECT COUNT(*) FROM `members` WHERE UserID = ? AND RR = 0 AND ClubPays = 0";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID']]);
	} catch (PDOException $e) {
		halt(500);
	}
	if ($query->fetchColumn() == 1) {
		$partial_reg_require_topup = true;
	}
}

$sql = $db->prepare("SELECT COUNT(*) FROM `members` WHERE `members`.`UserID` = ? AND `ClubPays` = '0'");
$sql->execute([$_SESSION['UserID']]);

$clubFee = 0;
$totalFee = 0;

$payingSwimmerCount = $sql->fetchColumn();

if ($payingSwimmerCount == 1) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeIndividual');
} else if ($partial_reg_require_topup) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeFamily') - $clubFee;
} else if ($payingSwimmerCount > 1 && !$partial_reg) {
	$clubFee = $systemInfo->getSystemOption('ClubFeeIndividual');
} else {
	$clubFee = 0;
}

if ($partial_reg) {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = ? && `members`.`RR` = 1";
} else {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = ?";
}
$getMembers = $db->prepare($sql);
$getMembers->execute([$_SESSION['UserID']]);

$member = $getMembers->fetchAll(PDO::FETCH_ASSOC);
$count = sizeof($member);

$totalFee += $clubFee;

$asaFees = [];

$asa1 = $systemInfo->getSystemOption('ASA-County-Fee-L1') + $systemInfo->getSystemOption('ASA-Regional-Fee-L1') + $systemInfo->getSystemOption('ASA-National-Fee-L1');
$asa2 = $systemInfo->getSystemOption('ASA-County-Fee-L2') + $systemInfo->getSystemOption('ASA-Regional-Fee-L2') + $systemInfo->getSystemOption('ASA-National-Fee-L2');
$asa3 = $systemInfo->getSystemOption('ASA-County-Fee-L3') + $systemInfo->getSystemOption('ASA-Regional-Fee-L3') + $systemInfo->getSystemOption('ASA-National-Fee-L3');

for ($i = 0; $i < $count; $i++) {
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa1;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa2;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = $asa3;
	}
	$totalFee += $asaFees[$i];
}

$clubFeeString = number_format($clubFee/100,2,'.','');
$totalFeeString = number_format($totalFee/100,2,'.','');

$pagetitle = "Your Renewal Fees";
$title = "Your Membership Renewal Fees";
if ($renewal == 0) {
	$pagetitle = "Your Registration Fees";
	$title = "Your Registration Fees";
}

include BASE_PATH . 'views/header.php';
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<form method="post">
		<h1>
			<?= $title ?>
		</h1>
		<p class="lead">
		  There's just one more step to go. We now need you to confirm your membership <?php if ($renewal == 0) { ?>registration<?php } else { ?>renewal<?php } ?>.
		</p>
		<p class="lead">
			These fees include your Swim England membership fees for the sport's governing bodies at National, Regional and County Level.
		</p>
		<?php $nf = "next";
		if ($renewal == 0) {
			$nf = "first";
		}; ?>
		<p>
			You will pay these fees as part of your <?= $nf ?> Direct Debit payment to <?=htmlspecialchars(env('CLUB_NAME'))?>.
		</p>

		<h2>Your Membership Fees</h2>
		<div class="table-responsive-md">
			<table class="table">
				<thead class="">
					<tr class="bg-primary text-light">
						<th>
							Club Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<thead class="thead-light">
					<tr>
						<th>
							Type
						</th>
						<th>
							Fee
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php if ($partial_reg && $partial_reg_require_topup) { ?>
							Membership Top Up (Individual to Family)
						<?php } else if ($payingSwimmerCount > 1) { ?>
							Family Membership
						<?php }else { ?>
							Individual Membership
							<?php } ?>
						</td>
						<td>
							&pound;<?= $clubFeeString ?>
						</td>
					</tr>
				</tbody>
			<!--</table>
		</div>
		<?php if ($payingSwimmerCount > 1) {
			?>
			<p class="lead">
				You <?php if ($renewal == 0) { ?>will <?php } ?>pay for a family membership, covering all of your swimmers at a reduced cost.
			</p>
			<?php
		} ?>
 		<p>Your club membership fee is &pound;<?=$clubFeeString?></p>

		<h2>Swim England Membership Fees</h2>
		<div class="table-responsive-md">
			<table class="table">-->
				<thead class="">
					<tr class="bg-primary text-light">
						<th>
							Swim England Membership
						</th>
						<th>
						</th>
					</tr>
				</thead>
				<thead class="thead-light">
					<tr>
						<th>
							Swimmer
						</th>
						<th>
							Fee
						</th>
					</tr>
				</thead>
				<tbody>
			<?php
			for ($i = 0; $i < $count; $i++) {
				$asaFeesString;
				if ($member[$i]['ClubPays']) {
					$asaFeesString = "0.00 (Paid by club)";
				} else {
					$asaFeesString = number_format($asaFees[$i]/100,2,'.','');
				}
				?>
				<tr>
					<td>
						<?=htmlspecialchars($member[$i]['MForename'] . " " . $member[$i]['MSurname'])?>
					</td>
					<td>
						&pound;<?php echo $asaFeesString; ?>
					</td>
				</tr>
			<?php } ?>
				</tbody>
				<tbody>
					<tr class="table-active">
						<td>
							Total Membership Fee
						</td>
						<td>
							&pound;<?= $totalFeeString ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<p>
			Your total <?php if ($renewal == 0) { ?>registration<?php } else { ?>renewal<?php } ?> fee will be &pound;<?=$totalFeeString?>. By continuing to complete your membership <?php if ($renewal == 0) { ?>registration<?php } else { ?>renewal<?php } ?>, you confirm that you will pay this amount as part of your <?= $nf ?> Direct Debit Payment.
		</p>
		<?php if (!userHasMandates($_SESSION['UserID'])) { ?>
			<p>
				We now need you to set up your Direct Debit agreement with <?=htmlspecialchars(env('CLUB_NAME'))?>. We will redirect you to our payments system where you will setup a Direct Debit.
			</p>
		<?php } else { ?>
			<p>
				You're now ready to complete your <?php if ($renewal == 0) {
				?>registration<?php } else { ?>renewal<?php } ?>.
			</p>
		<?php } ?>
		<p>
			<button type="submit" class="btn btn-success btn-lg">
				<?php if (!userHasMandates($_SESSION['UserID'])) { ?>
					Setup Direct Debit
				<?php } else if ($renewal == 0) { ?>
					Complete Registration
				<?php } else { ?>
					Complete Renewal
				<?php } ?>
			</button>
		</p>
	</form>
</div>

<?php

include BASE_PATH . 'views/footer.php';
