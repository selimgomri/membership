<?php

$user = $_SESSION['UserID'];
$pagetitle = "Pending Messages";
$use_white_background = true;

$sql = "SELECT * FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE `Status` = 'Queued';";
$result = mysqli_query($link, $sql);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<h1>Pending Messages</h1>
	<p class="lead">Messages listed here are queued to be sent in batches.</p>
	<?php if (mysqli_num_rows($result) > 0) { ?>
		<div class="table-responsive-md">
			<table class="table">
				<thead class="thead-light">
					<tr>
						<th>Name</th>
						<th>Subject</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC) ?>
					<tr>
						<td>
							<a href="<?php echo autoUrl("notify/email/" . $row['EmailID']); ?>">
								<?php echo $row['Forename'] . " " . $row['Surname']; ?>
							</a>
						</td>
						<td>
							<?php echo $row['Subject']; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php } else { ?>
	<div class="alert alert-info">
		There are no messages in the queue to send.
	</div>
	<?php } ?>
</div>

<?php include BASE_PATH . "views/footer.php";
