<?php

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
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

$sql = "SELECT * FROM `members` WHERE `members`.`UserID` = '$user' AND
`ClubPays` = '0';";
$result = mysqli_query($link, $sql);

$clubFee = 0;
$totalFee = 0;

$payingSwimmerCount = mysqli_num_rows($result);

if ($payingSwimmerCount == 1) {
	$clubFee = 4000;
} else if ($partial_reg_require_topup) {
	$clubFee = 1000;
} else if ($payingSwimmerCount > 1 && !$partial_reg) {
	$clubFee = 5000;
} else {
	$clubFee = 0;
}

if ($partial_reg) {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = '$user' && `members`.`RR` = 1;";
} else {
	$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
	members.SquadID WHERE `members`.`UserID` = '$user';";
}
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

$totalFee += $clubFee;

$asaFees = [];
$member = [];

for ($i = 0; $i < $count; $i++) {
	$member[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = ASA_FEE_1;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = ASA_FEE_2;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = ASA_FEE_3;
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
	<form method="post" class="mb-3 p-3 bg-white rounded shadow">
		<h1 class="border-bottom border-gray pb-2">
			<?= $title ?>
		</h1>
		<p class="lead">
		  There's just one more step to go. We now need you to confirm your membership renewal.
		</p>
		<p class="lead">
			These Fees include your Swim England Membership fees for the governing bodies at
			National, Regional and County Level.
		</p>
		<?php $nf = "next";
		if ($renewal == 0) {
			$nf = "first";
		}; ?>
		<p>
			You will pay these fees as part of your <?= $nf ?> Direct Debit payment to
			<?=CLUB_NAME?>.
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
				You <?php if ($renewal == 0) { ?>will <?php } ?>pay for a family membership,
				covering all of your swimmers at a reduced cost.
			</p>
			<?
		} ?>
 		<p>Your club membership fee is &pound;<?php echo $clubFeeString; ?></p>

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
			<?
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
						<?php echo $member[$i]['MForename'] . " " . $member[$i]['MSurname']; ?>
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
			Your total renewal fee will be &pound;<?php echo $totalFeeString; ?>. By
			continuing to complete your membership renewal, you confirm that you will
			pay this amount as part of your next Direct Debit Payment.
		</p>
		<?php if (!userHasMandates($_SESSION['UserID'])) { ?>
			<p>
				We now need you to set up your Direct Debit agreement with
				<?=CLUB_NAME?>. We will redirect you to our payments system where
				you will setup a Direct Debit.
			</p>
		<?php } else { ?>
			<p>
				You're now ready to complete your <?php if ($renewal == 0) {
				?>Registration<?php } else { ?>Renewal<?php } ?>.
			</p>
		<?php } ?>
		<p class="mb-0">
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

<?
include BASE_PATH . 'views/footer.php';
