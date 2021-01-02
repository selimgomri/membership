<?php

$db = app()->db;
$tenant = app()->tenant;

$renewal = $db->prepare("SELECT * FROM `renewals` WHERE `ID` = ? AND Tenant = ?");
$renewal->execute([
	$id,
	$tenant->getId()
]);

$row = $renewal->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($row['Name']) . " - Edit Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";
?>

<div class="container">
	<form method="post" class="needs-validation" novalidate>
		<h1>Editing <?= htmlspecialchars($row['Name']) ?></h1>

		<div class="row">
			<div class="col-lg-8">
				<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'])) {
					echo $_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'];
					unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo']);
				} ?>

				<div class="form-group">
					<label for="name">Renewal Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($row['Name']) ?>" required>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="start">Start Date</label>
						<input type="date" class="form-control" id="start" name="start" value="<?= htmlspecialchars(date("Y-m-d", strtotime($row['StartDate']))) ?>">
					</div>

					<div class="form-group col-md-6">
						<label for="end">End Date</label>
						<input type="date" class="form-control" id="end" name="end" value="<?= htmlspecialchars(date("Y-m-d", strtotime($row['EndDate']))) ?>" required>
					</div>
				</div>

				<p class="mb-0">
					<button class="btn btn-success" type="submit">
						Save Changes
					</button>
					<a href="<?= htmlspecialchars(autoUrl("renewal/$id")) ?>" class="btn btn-danger">
						Return to Status List
					</a>
				</p>
			</div>
		</div>

	</form>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
