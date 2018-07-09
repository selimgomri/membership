<?

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT `MForename`, `MSurname`, `Forename`, `Surname`, `ASANumber`,
`payments`.`Status`, `RenewalID` FROM ((((`members` LEFT JOIN `renewalMembers`
ON members.MemberID = renewalMembers.MemberID) LEFT JOIN `users` ON
members.UserID = users.UserID) LEFT JOIN `paymentsPending` ON
renewalMembers.PaymentID = paymentsPending.PaymentID) LEFT JOIN `payments` ON
payments.PMkey = paymentsPending.PMkey) WHERE `renewalMembers`.`RenewalID` =
'$id' OR `renewalMembers`.`RenewalID` IS NULL ORDER BY `MForename` ASC,
`MSurname` ASC;";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$pagetitle = "Membership Renewal";
include BASE_PATH . "views/header.php";

?>

<div class="container">
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
				if ($row['RenewalID'] == "" || $row['Status'] == "failed" || $row['Status'] == "charged_back") {
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
						<? echo paymentStatusString($row['Status']); ?>
					</td>
				</tr>
				<?
			} ?>
		</tbody>
	</table>
</div>

<?

include BASE_PATH . "views/footer.php";
