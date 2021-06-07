<?php

$pagetitle = "Create New Renewal Period";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";

$date = new DateTime('tomorrow', new DateTimeZone('Europe/London'));
$datePlus = new DateTime('+2 week', new DateTimeZone('Europe/London'));

$val = [
	'',
	'',
	'',
];


?>

<div class="container">
	<form method="post" class="needs-validation" novalidate>
		<h1>Create a new Renewal Period</h1>
		<div class="row">
			<div class="col-lg-8">
				<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'])) {
					echo $_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'];
					unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo']);
					$val = $_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalForm'];
					unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalForm']);
				} ?>

				<div class="mb-3">
					<label class="form-label" for="name">Renewal Name</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="For <?= htmlspecialchars($datePlus->format('Y')) ?>" value="<?php if (isset($val[0])) { ?><?= htmlspecialchars($val[0]) ?><?php } ?>" required>
					<div class="invalid-feedback">
						Enter a name for the renewal period
					</div>
				</div>

				<div class="row">
					<div class="mb-3 col-md-6">
						<label class="form-label" for="start">Start Date</label>
						<input type="date" class="form-control" id="start" name="start" value="<?= htmlspecialchars($date->format('Y-m-d')) ?>" min="<?= htmlspecialchars($date->format('Y-m-d')) ?>" required>
						<div class="invalid-feedback">
							Enter a start date
						</div>
					</div>

					<div class="mb-3 col-md-6">
						<label class="form-label" for="end">End Date</label>
						<input type="date" class="form-control" id="end" name="end" value="<?= htmlspecialchars($datePlus->format('Y-m-d')) ?>" min="<?= htmlspecialchars($date->format('Y-m-d')) ?>" required>
						<div class="invalid-feedback">
							Enter an end date
						</div>
					</div>
				</div>

				<p class="mb-0">
					<button class="btn btn-success" type="submit">
						Add Renewal
					</button>
				</p>
			</div>
		</div>

	</form>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->render();
