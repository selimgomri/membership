<?

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$sql = "SELECT * FROM `members` WHERE `UserID` = '$user';";
$result = mysqli_query($link, $sql);

$clubFee = 0;
$totalFee = 0;

$count = mysqli_num_rows($result);

if ($count == 1) {
	$clubFee = 4000;
} else {
	$clubFee = 5000;
}

$totalFee += $clubFee;

$asaFees = [];
$member = [];

for ($i = 0; $i < $count; $i++) {
	$member[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($member[$i]['ASACategory'] == 1) {
		$asaFees[$i] = 1620;
	} else if ($member[$i]['ASACategory'] == 2) {
		$asaFees[$i] = 3300;
	} else if ($member[$i]['ASACategory'] == 2) {
		$asaFees[$i] = 1250;
	}
	$totalFee += $asaFees[$i];
}

$clubFeeString = number_format($clubFee/100,2,'.','');
$totalFeeString = number_format($totalFee/100,2,'.','');

$pagetitle = "Your Renewal Fees";

include BASE_PATH . 'views/header.php';
?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
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

		<h2>Club Fee</h2>
		<? if ($count > 1) {
			?>
			<p class="lead">
				You pay for a family membership
			</p>
			<?
		} ?>
 		<p>&pound;<? echo $clubFeeString; ?></p>
		<p>Integer value as pence: <? echo $clubFee; ?></p>

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
				?>
				<tr>
					<th>
						<? echo $member[$i]['MForename'] . " " . $member[$i]['MSurname']; ?>
					</th>
					<th>
						<? echo number_format($asaFees[$i]/100,2,'.',''); ?>
					</th>
				</tr>
			<? } ?>
				</tbody>
			</table>
		</div>

		<h2>Total Fees</h2>
		<p>&pound;<? echo $totalFeeString; ?></p>
		<p>Integer value as pence: <? echo $totalFee; ?></p>
	</div>
</div>

<?
include BASE_PATH . 'views/footer.php';
