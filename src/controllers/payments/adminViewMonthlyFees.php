<?php

$access = $_SESSION['AccessLevel'];
if ($access != "Admin") {
	halt(404);
}

global $db;

$getDetails = $db->query("SELECT `Forename`, `Surname`, `UserID` FROM `users` WHERE `AccessLevel` = 'Parent' ORDER BY `Forename` ASC, `Surname` ASC");

$pagetitle = "Administration";
include BASE_PATH . 'views/header.php'; ?>

<div class="container">
	<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
      <li class="breadcrumb-item active" aria-current="page">Member Fees</li>
    </ol>
  </nav>
	<h1>Member Fees</h1>
	<p class="lead">Fees for all registered parents and members</p>
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
				<?php while ($row = $getDetails->fetch(PDO::FETCH_ASSOC)) { ?>
				<tr>
					<td><?=htmlspecialchars($row['Forename'] . ' ' . $row['Surname'])?></td>
					<td><?=swimmers($link, $row['UserID'], true)?></td>
					<td>
						Squads: <?=(monthlyFeeCost($link, $row['UserID'], "string"))?> <br>
						Extras Fees: <?=(monthlyExtraCost($link, $row['UserID'], "string"))?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php
include BASE_PATH . 'views/footer.php';
