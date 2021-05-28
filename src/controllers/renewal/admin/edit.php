<?php

$db = app()->db;
$tenant = app()->tenant;

$renewal = $db->prepare("SELECT * FROM `renewals` WHERE `ID` = ? AND Tenant = ?");
$renewal->execute([
	$id,
	$tenant->getId()
]);

$row = $renewal->fetch(PDO::FETCH_ASSOC);

if (!$row) {
	halt(404);
}

$from = new DateTime($row['StartDate'], new DateTimeZone('Europe/London'));
$to = new DateTime($row['EndDate'], new DateTimeZone('Europe/London'));

$min = new DateTime('tomorrow', new DateTimeZone('Europe/London'));
if ($from < $min) {
	$min = clone $from;
}

$pagetitle = htmlspecialchars($row['Name']) . " - Edit Renewal";
include BASE_PATH . "views/header.php";
include BASE_PATH . "views/swimmersMenu.php";
?>

<div class="bg-light mt-n3 py-3 mb-3">
	<div class="container">

		<!-- Page header -->
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("renewal")) ?>">Renewal</a></li>
				<li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl("renewal/$id")) ?>">#<?= htmlspecialchars($id) ?></a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit</li>
			</ol>
		</nav>

		<div class="row align-items-center">
			<div class="col-lg-8">
				<h1>
					Editing <?= htmlspecialchars($row['Name']) ?>
				</h1>
				<p class="lead mb-0" id="leadDesc">
					<?= htmlspecialchars($from->format("l j F Y")) ?> to <?= htmlspecialchars($to->format("l j F Y")) ?>
				</p>
				<div class="mb-3 d-lg-none"></div>
			</div>
			<div class="ml-auto col-lg-auto">
				<button type="submit" form="form" class="btn btn-success">
					Save
					</a>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<form id="form" method="post" class="needs-validation" novalidate>

		<div class="row">
			<div class="col-lg-8">
				<?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'])) {
					echo $_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo'];
					unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewRenewalErrorInfo']);
				} ?>

				<div class="mb-3">
					<label class="form-label" for="name">Renewal Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($row['Name']) ?>" required>
				</div>

				<div class="row">
					<div class="mb-3 col-md-6">
						<label class="form-label" for="start">Start Date</label>
						<input type="date" class="form-control" id="start" name="start" value="<?= htmlspecialchars($from->format('Y-m-d')) ?>" min="<?= htmlspecialchars($min->format('Y-m-d')) ?>" required>
					</div>

					<div class="mb-3 col-md-6">
						<label class="form-label" for="end">End Date</label>
						<input type="date" class="form-control" id="end" name="end" value="<?= htmlspecialchars($to->format('Y-m-d')) ?>" required>
					</div>
				</div>

				<p class="mb-0">
					<button class="btn btn-success" type="submit">
						Save Changes
					</button>
					<a href="<?= htmlspecialchars(autoUrl("renewal/$id")) ?>" class="btn btn-danger">
						Cancel
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
