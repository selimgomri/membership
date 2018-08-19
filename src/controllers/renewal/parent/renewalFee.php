<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$sql = "SELECT * FROM `members` WHERE `members`.`UserID` = '$user' AND
`ClubPays` = '0';";
$result = mysqli_query($link, $sql);

$clubFee = 0;
$totalFee = 0;

$payingSwimmerCount = mysqli_num_rows($result);

if ($payingSwimmerCount == 1) {
	$clubFee = 4000;
} else if ($payingSwimmerCount > 1) {
	$clubFee = 5000;
}

$sql = "SELECT * FROM `members` INNER JOIN `squads` ON squads.SquadID =
members.SquadID WHERE `members`.`UserID` = '$user';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);

$totalFee += $clubFee;

$asaFees = [];
$member = [];

for ($i = 0; $i < $count; $i++) {
	$member[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($member[$i]['ASACategory'] == 1 && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 1620;
	} else if ($member[$i]['ASACategory'] == 2  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 3300;
	} else if ($member[$i]['ASACategory'] == 3  && !$member[$i]['ClubPays']) {
		$asaFees[$i] = 1250;
	}
	$totalFee += $asaFees[$i];
}

$clubFeeString = number_format($clubFee/100,2,'.','');
$totalFeeString = number_format($totalFee/100,2,'.','');

$pagetitle = "Your Renewal Fees";

include BASE_PATH . 'views/header.php';
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
	<form method="post" class="mb-3 p-3 bg-white rounded box-shadow">
		<h1 class="border-bottom border-gray pb-2">
			Your Membership Renewal Fees
		</h1>
		<p class="lead">
			Renewal Fees include your ASA membership fees for the governing bodies at
			National, Regional and County Level.
		</p>
		<p>
			You will pay these fees as part of your next Direct Debit payment to
			Chester-le-Street ASC.
		</p>

		<h2>Club Membership Fee</h2>
		<? if ($payingSwimmerCount > 1) {
			?>
			<p class="lead">
				You pay for a family membership, covering all of your swimmers at a
				reduced cost
			</p>
			<?
		} ?>
 		<p>Your club membership fee is &pound;<? echo $clubFeeString; ?></p>

		<h2>ASA Fees</h2>
		<div class="table-responsive">
			<table class="table">
				<thead class="thead-light">
					<tr>
						<th>
							Swimmer
						</th>
						<th>
							ASA Membership Fee
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
					<th>
						<? echo $member[$i]['MForename'] . " " . $member[$i]['MSurname']; ?>
					</th>
					<th>
						&pound;<? echo $asaFeesString; ?>
					</th>
				</tr>
			<? } ?>
				</tbody>
			</table>
		</div>

		<h2>Total Fees</h2>
		<p>
			Your total renewal fee will be &pound;<? echo $totalFeeString; ?>. By
			continuing to complete your membership renewal, you confirm that you will
			pay this amount as part of your next Direct Debit Payment.
		</p>
		<p>
			If you have not yet set up a Direct Debit with Chester-le-Street ASC, we
			will redirect you to our Payments system. You must setup a Direct Debit
			there and then return to the Renewal system.
		</p>
		<p class="mb-0">
			<button type="submit" class="btn btn-success">
				Complete Renewal
			</button>
		</p>
	</form>
</div>

<?
include BASE_PATH . 'views/footer.php';
