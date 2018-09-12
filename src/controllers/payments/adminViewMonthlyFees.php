<?php

$access = $_SESSION['AccessLevel'];
if ($access != "Admin") {
	halt(404);
}

$sql = "SELECT `Forename`, `Surname`, `UserID` FROM `users` WHERE `AccessLevel` = 'Parent' ORDER BY `Forename` ASC, `Surname` ASC;";
$result = mysqli_query($link, $sql);

$pagetitle = "Administration";
include BASE_PATH . 'views/header.php'; ?>

<div class="container">
	<h1>Payment Administration</h1>
	<p>Fees for all registered parents</p>
	<div class="table-responsive-md">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Swimmers</th>
					<th>Expected Payment</th>
				</tr>
			</thead>
			<tbody>
				<?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
				<tr>
					<td><?php echo $row['Forename'] . ' ' . $row['Surname']; ?></td>
					<td><?php echo swimmers($link, $row['UserID'], true); ?></td>
					<td>
						Squads: <?php echo monthlyFeeCost($link, $row['UserID'], "string"); ?> <br>
						Extras (eg CF): <?php echo monthlyExtraCost($link, $row['UserID'], "string"); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php
include BASE_PATH . 'views/footer.php';
