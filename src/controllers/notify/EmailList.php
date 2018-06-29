<?php

$user = $_SESSION['UserID'];
$pagetitle = "Pending Messages";

$sql = "SELECT * FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE `Status` = 'Queued';";
$result = mysqli_query($link, $sql);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<h1>Pending Messages</h1>
	<p class="lead">Messages listed here are queued to be sent in batches.</p>
	<? if (mysqli_num_rows($result) > 0) { ?>
		<div class="table-responsive">
			<table class="table">
				<thead class="thead-light">
					<tr>
						<th>Name</th>
						<th>Subject</th>
					</tr>
				</thead>
				<tbody>
				<? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC) ?>
					<tr>
						<td>
							<a href="<? echo autoUrl("notify/email/" . $row['EmailID']); ?>">
								<? echo $row['Forename'] . " " . $row['Surname']; ?>
							</a>
						</td>
						<td>
							<? echo $row['Subject']; ?>
						</td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
	<? } else { ?>
	<div class="alert alert-info">
		There are no messages in the queue to send.
	</div>
	<? } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
