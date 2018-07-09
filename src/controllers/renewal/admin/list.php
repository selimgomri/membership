<?

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM `renewals` WHERE `ID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$renewalArray = mysqli_fetch_array($result, MYSQLI_ASSOC);

$sql = "SELECT `MForename`, `MSurname`, `Forename`, `Surname`, `ASANumber`,
`payments`.`Status`, `RenewalID` FROM ((((`renewalMembers` RIGHT JOIN `members`
ON members.MemberID = renewalMembers.MemberID) LEFT JOIN `users` ON
members.UserID = users.UserID) LEFT JOIN `paymentsPending` ON
renewalMembers.PaymentID = paymentsPending.PaymentID) LEFT JOIN `payments` ON
payments.PMkey = paymentsPending.PMkey) WHERE `renewalMembers`.`RenewalID` =
'$id' OR `renewalMembers`.`RenewalID` IS NULL OR `renewalMembers`.`RenewalID` IS
NOT NULL ORDER BY `MForename` ASC, `MSurname` ASC;";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";

?>

<div class="container">
	<h1><? echo $renewalArray['Name']; ?> Status</h1>
	<p class="lead">
		This is the current status for this membership renewal which started on <? echo date("l j F Y", strtotime($renewalArray['StartDate'])); ?> and finishes on <? echo date("l j F Y", strtotime($renewalArray['EndDate'])); ?>
	</p>

	<table class="table">
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
			<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				if ($row['RenewalID'] == "" || $row['RenewalID'] != $id ||
				$row['Status'] == "failed" || $row['Status'] == "charged_back") {
					?><tr class="table-danger"><?
				} else if ($row['Status'] == "paid_out" || $row['Status'] == "confirmed") {
					?><tr class="table-success"><?
				} else {
					?><tr><?
				}
				?>
					<td>
						<? echo $row['MForename'] . " " . $row['MSurname']; ?>
					</td>
					<td>
						<? echo $row['Forename'] . " " . $row['Surname']; ?>
					</td>
					<td>
						<span class="mono">
							<? echo $row['ASANumber']; ?>
						</span>
					</td>
					<td>
						<? if ($row['RenewalID'] == "" || $row['RenewalID'] != $id) {
							?>No Renewal Exists<?
						} else if ($row['Status'] == "") {
							?>Payment not yet processed<?
						} else {
							echo paymentStatusString($row['Status']);
						} ?>
					</td>
				</tr>
				<?
			} ?>
		</tbody>
	</table>
</div>

<?

include BASE_PATH . "views/footer.php";